<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vendor;
use App\Models\Menu;
use App\Models\Pesanan;
use Illuminate\Support\Facades\Storage;

class VendorController extends Controller
{
    public function dashboard()
    {
        $vendor          = auth()->user()->vendor;
        $totalMenu       = $vendor->menus()->count();
        $pesananLunas    = $vendor->pesanan()->where('status_bayar', 'lunas')->count();
        $totalPendapatan = $vendor->pesanan()->where('status_bayar', 'lunas')->sum('total');
        $pesananTerbaru  = $vendor->pesanan()
            ->where('status_bayar', 'lunas')
            ->with('details')
            ->latest()
            ->take(5)
            ->get();

        return view('vendor.dashboard', compact(
            'vendor', 'totalMenu', 'pesananLunas', 'totalPendapatan', 'pesananTerbaru'
        ));
    }

    // Form setup profil vendor (pertama kali login)
    public function setupForm()
    {
        if (auth()->user()->vendor) {
            return redirect()->route('vendor.dashboard');
        }
        return view('vendor.setup');
    }

    public function setupStore(Request $request)
    {
        $request->validate([
            'nama_kantin' => 'required|string|max:100',
            'deskripsi'   => 'nullable|string',
            'foto'        => 'nullable|image|max:2048',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('vendor', 'public');
        }

        Vendor::create([
            'id_user'     => auth()->id(),
            'nama_kantin' => $request->nama_kantin,
            'deskripsi'   => $request->deskripsi,
            'foto'        => $fotoPath,
        ]);

        return redirect()->route('vendor.dashboard')
            ->with('success', 'Profil kantin berhasil dibuat!');
    }

    // CRUD Menu 
    public function menuIndex()
    {
        $vendor = auth()->user()->vendor;
        $menus  = $vendor->menus()->latest()->get();
        return view('vendor.menu.index', compact('vendor', 'menus'));
    }

    public function menuCreate()
    {
        return view('vendor.menu.create');
    }

    public function menuStore(Request $request)
    {
        $request->validate([
            'nama_menu' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'harga'     => 'required|numeric|min:0',
            'foto'      => 'nullable|image|max:2048',
        ]);

        $vendor   = auth()->user()->vendor;
        $fotoPath = null;

        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('menu', 'public');
        }

        Menu::create([
            'id_vendor' => $vendor->id_vendor,
            'nama_menu' => $request->nama_menu,
            'deskripsi' => $request->deskripsi,
            'harga'     => $request->harga,
            'foto'      => $fotoPath,
            'tersedia'  => $request->boolean('tersedia', true),
        ]);

        return redirect()->route('vendor.menu.index')
            ->with('success', 'Menu berhasil ditambahkan!');
    }

    public function menuEdit(Menu $menu)
    {
        $vendor = auth()->user()->vendor;
        abort_if($menu->id_vendor !== $vendor->id_vendor, 403);
        return view('vendor.menu.edit', compact('menu'));
    }

    public function menuUpdate(Request $request, Menu $menu)
    {
        $vendor = auth()->user()->vendor;
        abort_if($menu->id_vendor !== $vendor->id_vendor, 403);

        $request->validate([
            'nama_menu' => 'required|string|max:100',
            'harga'     => 'required|numeric|min:0',
            'foto'      => 'nullable|image|max:2048',
        ]);

        $data = [
            'nama_menu' => $request->nama_menu,
            'deskripsi' => $request->deskripsi,
            'harga'     => $request->harga,
            'tersedia'  => $request->has('tersedia'),
        ];

        if ($request->hasFile('foto')) {
            if ($menu->foto) Storage::disk('public')->delete($menu->foto);
            $data['foto'] = $request->file('foto')->store('menu', 'public');
        }

        $menu->update($data);

        return redirect()->route('vendor.menu.index')
            ->with('success', 'Menu berhasil diperbarui!');
    }

    public function menuDestroy(Menu $menu)
    {
        $vendor = auth()->user()->vendor;
        abort_if($menu->id_vendor !== $vendor->id_vendor, 403);
        if ($menu->foto) Storage::disk('public')->delete($menu->foto);
        $menu->delete();

        return redirect()->route('vendor.menu.index')
            ->with('success', 'Menu berhasil dihapus!');
    }

    // Pesanan Lunas 
    public function pesananIndex()
    {
        $vendor  = auth()->user()->vendor;
        $pesanan = $vendor->pesanan()
            ->where('status_bayar', 'lunas')
            ->with('details')
            ->latest()
            ->paginate(15);

        return view('vendor.pesanan.index', compact('vendor', 'pesanan'));
    }

    public function pesananShow(string $idPesanan)
    {
        $vendor  = auth()->user()->vendor;
        $pesanan = Pesanan::where('id_pesanan', $idPesanan)
            ->where('id_vendor', $vendor->id_vendor)
            ->where('status_bayar', 'lunas')
            ->with('details')
            ->firstOrFail();

        return view('vendor.pesanan.show', compact('pesanan'));
    }
}