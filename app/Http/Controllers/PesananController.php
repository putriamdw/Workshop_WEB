<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vendor;
use App\Models\Menu;
use App\Models\Pesanan;
use App\Models\PesananDetail;
use Illuminate\Support\Facades\DB;
use Midtrans\Config as MidtransConfig;
use Midtrans\Snap;
use Midtrans\Notification;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\SvgWriter;

class PesananController extends Controller
{

    public function __construct()
    {
        MidtransConfig::$serverKey    = config('midtrans.server_key');
        MidtransConfig::$isProduction = config('midtrans.is_production');
        MidtransConfig::$isSanitized  = true;
        MidtransConfig::$is3ds        = true;
    }

    // Halaman pilih vendor
    public function index()
    {
        $vendors = Vendor::where('aktif', true)->with('menus')->get();
        return view('customer.index', compact('vendors'));
    }

    // Halaman menu dari vendor tertentu
    public function pilihVendor(Vendor $vendor)
    {
        abort_if(!$vendor->aktif, 404);
        $menus = $vendor->menus()->where('tersedia', true)->get();
        return view('customer.pesan', compact('vendor', 'menus'));
    }

    // Proses buat pesanan
    public function store(Request $request)
    {
        $request->validate([
            'id_vendor'           => 'required|integer|exists:vendor,id_vendor',
            'items'               => 'required|array|min:1',
            'items.*.id_menu'     => 'required|integer|exists:menu,id_menu',
            'items.*.jumlah'      => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $vendor  = Vendor::findOrFail($request->id_vendor);
            $total   = 0;
            $details = [];

            foreach ($request->items as $item) {
                $menu     = Menu::findOrFail($item['id_menu']);
                $subtotal = $menu->harga * $item['jumlah'];
                $total   += $subtotal;

                $details[] = [
                    'id_menu'      => $menu->id_menu,
                    'nama_menu'    => $menu->nama_menu,
                    'harga_satuan' => $menu->harga,
                    'jumlah'       => $item['jumlah'],
                    'subtotal'     => $subtotal,
                ];
            }

            $idUser    = auth()->id() ?? null;
            $namaGuest = null;
            if (!$idUser) {
                $namaGuest = Pesanan::generateNamaGuest();
            }

            $idPesanan = Pesanan::generateId();
            $pesanan   = Pesanan::create([
                'id_pesanan'   => $idPesanan,
                'id_user'      => $idUser,
                'nama_guest'   => $namaGuest,
                'id_vendor'    => $vendor->id_vendor,
                'total'        => $total,
                'status_bayar' => 'belum_bayar',
            ]);

            foreach ($details as $d) {
                PesananDetail::create(array_merge(['id_pesanan' => $idPesanan], $d));
            }

            DB::commit();
            return redirect()->route('customer.bayar', $idPesanan);

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membuat pesanan: ' . $e->getMessage());
        }
    }

    // Halaman pembayaran
    public function bayar(string $idPesanan)
    {
        $pesanan = Pesanan::with(['details', 'vendor'])->findOrFail($idPesanan);

        if ($pesanan->isLunas()) {
            return redirect()->route('customer.sukses', $idPesanan);
        }

        // Generate snap token klo belum ada
        if (!$pesanan->midtrans_token) {
            $snapToken = $this->generateSnapToken($pesanan);
            $pesanan->update([
                'midtrans_token'    => $snapToken,
                'midtrans_order_id' => $idPesanan,
            ]);
        }

        $clientKey    = config('midtrans.client_key');
        $isProduction = config('midtrans.is_production');

        return view('customer.bayar', compact('pesanan', 'clientKey', 'isProduction'));
    }

    private function generateSnapToken(Pesanan $pesanan): string
    {
        $itemDetails = $pesanan->details->map(fn($d) => [
            'id'       => (string) $d->id_menu,
            'price'    => (int) $d->harga_satuan,
            'quantity' => $d->jumlah,
            'name'     => $d->nama_menu,
        ])->toArray();

        $params = [
            'transaction_details' => [
                'order_id'     => $pesanan->id_pesanan,
                'gross_amount' => (int) $pesanan->total,
            ],
            'item_details'     => $itemDetails,
            'customer_details' => [
                'first_name' => $pesanan->user?->name ?? $pesanan->nama_guest,
                'email'      => $pesanan->user?->email ?? ($pesanan->nama_guest . '@guest.kantin'),
            ],
            'enabled_payments' => [
                'bca_va', 'bni_va', 'bri_va', 'mandiri_va', 'gopay', 'qris',
            ],
            'expiry' => ['unit' => 'hours', 'duration' => 2],
        ];

        return Snap::getSnapToken($params);
    }

    // Halaman sukses
    public function sukses(string $idPesanan)
    {
        $pesanan = Pesanan::with(['details', 'vendor'])->findOrFail($idPesanan);

        $baseUrl = env('NGROK_URL', config('app.url'));
        $qrCode  = new QrCode($baseUrl . '/pesan/sukses/' . $pesanan->id_pesanan);
        $writer  = new SvgWriter();
        $result  = $writer->write($qrCode);
        $qrSvg   = base64_encode($result->getString());

        return view('customer.sukses', compact('pesanan', 'qrSvg'));    
    }

    // Webhook Midtrans
    public function webhook(Request $request)
    {
        try {
            // Verifikasi
            $serverKey    = config('midtrans.server_key');
            $orderId      = $request->input('order_id');
            $statusCode   = $request->input('status_code');
            $grossAmount  = $request->input('gross_amount');
            $sigKey       = $request->input('signature_key');
            $expectedSig  = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

            if ($sigKey !== $expectedSig) {
                return response()->json(['message' => 'Invalid signature'], 403);
            }

            $notif             = new Notification();
            $transactionStatus = $notif->transaction_status;
            $fraudStatus       = $notif->fraud_status;
            $paymentType       = $notif->payment_type;

            $pesanan = Pesanan::find($orderId);
            if (!$pesanan) {
                return response()->json(['message' => 'Order not found'], 404);
            }

            if ($transactionStatus === 'capture' && $fraudStatus === 'accept') {
                $this->markLunas($pesanan, $paymentType, $notif);
            } elseif ($transactionStatus === 'settlement') {
                $this->markLunas($pesanan, $paymentType, $notif);
            } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
                $pesanan->update(['status_bayar' => 'expired']);
            }

            return response()->json(['message' => 'OK']);

        } catch (\Throwable $e) {
            \Log::error('Midtrans webhook: ' . $e->getMessage());
            return response()->json(['message' => 'Error'], 500);
        }
    }

    private function markLunas(Pesanan $pesanan, string $paymentType, $notif): void
    {
        $vaNumber = null;
        if (isset($notif->va_numbers) && count($notif->va_numbers) > 0) {
            $vaNumber = $notif->va_numbers[0]->va_number ?? null;
        }

        $pesanan->update([
            'status_bayar' => 'lunas',
            'metode_bayar' => in_array($paymentType, ['qris', 'gopay']) ? 'qris' : 'virtual_account',
            'bank'         => $notif->va_numbers[0]->bank ?? $paymentType,
            'va_number'    => $vaNumber,
            'paid_at'      => now(),
        ]);
    }

    public function cekStatus(string $idPesanan)
    {
        $pesanan = Pesanan::find($idPesanan);
        if (!$pesanan) return response()->json(['status' => 'not_found'], 404);

        return response()->json([
            'status'       => $pesanan->status_bayar,
            'redirect_url' => $pesanan->isLunas()
                ? route('customer.sukses', $idPesanan)
                : null,
        ]);
    }
}