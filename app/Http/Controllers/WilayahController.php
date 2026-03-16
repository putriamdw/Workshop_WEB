<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WilayahController extends Controller
{
    public function index()
    {
        return view('wilayah.index');
    }

    public function getProvinsi()
    {
        $data = DB::table('reg_provinces')
                  ->orderBy('name')
                  ->get();

        return response()->json([
            'status'  => 'success',
            'code'    => 200,
            'message' => 'Data provinsi berhasil diambil',
            'data'    => $data
        ]);
    }

    public function getKota($id_provinsi)
    {
        $data = DB::table('reg_regencies')
                  ->where('province_id', $id_provinsi)
                  ->orderBy('name')
                  ->get();

        return response()->json([
            'status'  => 'success',
            'code'    => 200,
            'message' => 'Data kota berhasil diambil',
            'data'    => $data
        ]);
    }

    public function getKecamatan($id_kota)
    {
        $data = DB::table('reg_districts')
                  ->where('regency_id', $id_kota)
                  ->orderBy('name')
                  ->get();

        return response()->json([
            'status'  => 'success',
            'code'    => 200,
            'message' => 'Data kecamatan berhasil diambil',
            'data'    => $data
        ]);
    }

    public function getKelurahan($id_kecamatan)
    {
        $data = DB::table('reg_villages')
                  ->where('district_id', $id_kecamatan)
                  ->orderBy('name')
                  ->get();

        return response()->json([
            'status'  => 'success',
            'code'    => 200,
            'message' => 'Data kelurahan berhasil diambil',
            'data'    => $data
        ]);
    }

    public function indexAxios()
    {
        return view('wilayah.index-axios');
    }
}
