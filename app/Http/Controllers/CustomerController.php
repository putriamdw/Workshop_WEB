<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CustomerController extends Controller
{
    // Data Customer
    public function index()
    {
        $customers = Customer::orderBy('created_at', 'desc')->get();
        return view('customer_data.index', compact('customers'));
    }

    // Form tambah customer (blob)
    public function createBlob()
    {
        return view('customer_data.tambah1');
    }

    // Simpan foto sebagai blob di db
    public function storeBlob(Request $request)
{
    $request->validate([
        'nama'        => 'required|string|max:100',
        'foto_base64' => 'required|string',
    ]);

    // Simpan sebagai base64 string (PostgreSQL)
    $fotoBase64 = $request->foto_base64;
    // Buang header "data:image/jpeg;base64," - simpan base64 murni
    $fotoBase64Clean = preg_replace('/^data:image\/\w+;base64,/', '', $fotoBase64);

    Customer::create([
        'nama'      => $request->nama,
        'alamat'    => $request->alamat,
        'provinsi'  => $request->provinsi,
        'kota'      => $request->kota,
        'kecamatan' => $request->kecamatan,
        'kodepos'   => $request->kodepos,
        'kelurahan' => $request->kelurahan,
        'foto_blob' => $fotoBase64Clean, // Simpan base64 string, bukan binary
    ]);

    return redirect()->route('customer-data.index')
        ->with('success', 'Customer berhasil ditambahkan (blob)!');
}

    // Form Tambah Customer (file)
    public function createFile()
    {
        return view('customer_data.tambah2');
    }

    // Simpan foto sebagai file, path disimpan di db
    public function storeFile(Request $request)
    {
        $request->validate([
            'nama'        => 'required|string|max:100',
            'foto_base64' => 'required|string',
        ]);

        $fotoBase64 = preg_replace('/^data:image\/\w+;base64,/', '', $request->foto_base64);
        $fotoBinary = base64_decode($fotoBase64);

        $filename = 'customer_' . time() . '.jpg';
        Storage::disk('public')->put('customers/' . $filename, $fotoBinary);

        Customer::create([
            'nama'      => $request->nama,
            'alamat'    => $request->alamat,
            'provinsi'  => $request->provinsi,
            'kota'      => $request->kota,
            'kecamatan' => $request->kecamatan,
            'kodepos'   => $request->kodepos,
            'kelurahan' => $request->kelurahan,
            'foto_path' => 'customers/' . $filename, // Simpan path, bukan binary
        ]);

        return redirect()->route('customer-data.index')
            ->with('success', 'Customer berhasil ditambahkan (file)!');
    }

    // Tampilkan foto blob sebagai response gambar
    public function fotoBlob($id)
    {
        $customer = Customer::findOrFail($id);
        $imageData = base64_decode($customer->foto_blob);
        return response($imageData)
            ->header('Content-Type', 'image/jpeg');
    }
}