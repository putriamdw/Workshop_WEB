<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Barang;
use Picqer\Barcode\BarcodeGeneratorSVG;

class GeneratePdfController extends Controller
{

    public function sertifikat()
    {
        $data = [
            'nama'    => Auth::user()->name,
            'tanggal' => date('d M Y'),
        ];

        $pdf = Pdf::loadView('pdf.sertifikat', $data)
                  ->setPaper('a4', 'landscape');

        return $pdf->download('sertifikat.pdf');
    }

    public function undangan()
    {
        $data = [
            'judul'   => 'Undangan Kegiatan Literasi',
            'tanggal' => date('d M Y'),
        ];

        $pdf = Pdf::loadView('pdf.undangan', $data)
                  ->setPaper('a4', 'portrait');

        return $pdf->download('undangan.pdf');
    }

    public function tagHarga(Request $request)
    {
        $request->validate([
            'selected' => 'required|array|min:1',
            'x'        => 'required|numeric|min:1|max:5',
            'y'        => 'required|numeric|min:1|max:8',
        ], [
            'selected.required' => 'Pilih minimal satu barang untuk dicetak!',
        ]);

        $barang     = Barang::whereIn('id_barang', $request->selected)->get();
        $startIndex = (($request->y - 1) * 5) + ($request->x - 1);

        $generator = new BarcodeGeneratorSVG();
        $barcodes  = [];
        foreach ($barang as $b) {
            $barcodes[$b->id_barang] = base64_encode(
                $generator->getBarcode(
                    $b->id_barang,
                    $generator::TYPE_CODE_128,
                    1,  // lebar bar
                    28  // tinggi bar (label 18mm)
                )
            );
        }

        $mm  = 2.83465;
        $pdf = Pdf::loadView('barang.cetak', [
            'barang'     => $barang,
            'startIndex' => $startIndex,
            'barcodes'   => $barcodes,
        ])->setPaper([0, 0, 210 * $mm, 167 * $mm], 'portrait');

        return $pdf->stream('label-barang.pdf');
    }
}