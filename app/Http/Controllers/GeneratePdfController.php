<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class GeneratePdfController extends Controller
{
    public function sertifikat()
    {
        $data = [
            'nama' => Auth::user()->name,
            'tanggal' => date('d M Y')
        ];

        $pdf = Pdf::loadView('pdf.sertifikat', $data)
                  ->setPaper('a4', 'landscape');

        return $pdf->download('sertifikat.pdf');
    }

    public function undangan()
    {
        $data = [
            'judul' => 'Undangan Kegiatan Literasi',
            'tanggal' => date('d M Y')
        ];

        $pdf = Pdf::loadView('pdf.undangan', $data)
                  ->setPaper('a4', 'portrait');

        return $pdf->download('undangan.pdf');
    }
}