<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">

<style>
body {
    font-family: "Times New Roman", serif;
    margin: 40px 55px;
    font-size: 13.5pt;
}

.kop-table {
    width: 100%;
    border-collapse: collapse;
}

.kop-table td {
    vertical-align: middle;
}

.logo {
    width: 80px;
}

.kop-text {
    text-align: center;
    line-height: 1.4;
    font-size: 13pt;
}

.garis {
    border-top: 3px solid black;
    border-bottom: 1px solid black;
    height: 5px;
    margin: 12px 0 25px 0;
}

.judul {
    text-align: center;
    font-weight: bold;
    text-decoration: underline;
    font-size: 15pt;
    margin-bottom: 10px;
}

.nomor {
    text-align: left;
    margin-bottom: 25px;
}

.isi {
    text-align: justify;
    line-height: 1.7;
    margin-bottom: 10px;
}

.detail {
    margin-left: 35px;
    margin-top: 10px;
    margin-bottom: 20px;
    line-height: 1.7;
}

.ttd-table {
    width: 100%;
    margin-top: 45px;
}

.ttd-kanan {
    width: 40%;
    text-align: center;
    vertical-align: top;
}
</style>

</head>
<body>

<!-- Kop Surat -->
<table class="kop-table">
<tr>
    <td width="15%">
        <img src="{{ public_path('logo.png') }}" class="logo">
    </td>
    <td class="kop-text">
        <strong>FAKULTAS VOKASI</strong><br>
        PROGRAM STUDI D4 TEKNIK INFORMATIKA<br>
        UNIVERSITAS AIRLANGGA<br>
        Surabaya
    </td>
</tr>
</table>

<div class="garis"></div>

<!-- Judul -->
<div class="judul">
    {{ $judul }}
</div>
<br>
<div class="nomor">
    Nomor: 001/UN3.VOKASI/TI/{{ date('Y') }}
</div>

<!-- Isi Surat -->
<div class="isi">
    Dengan hormat,<br>

    Sehubungan dengan kegiatan akademik Program Studi D4 Teknik Informatika 
    Fakultas Vokasi Universitas Airlangga, kami mengundang Saudara/i untuk 
    menghadiri kegiatan yang akan dilaksanakan pada:
</div>

<div class="detail">
    Hari/Tanggal : {{ $tanggal }} <br>
    Tempat       : Fakultas Vokasi Universitas Airlangga <br>
    Acara        : Kegiatan Literasi dan Pengembangan Kompetensi
</div>

<div class="isi">
    Demikian undangan ini kami sampaikan. Atas perhatian dan kehadirannya,
    kami ucapkan terima kasih.
</div>

<table class="ttd-table">
    <tr>
        <td width="60%"></td>
        <td class="ttd-kanan">
            Surabaya, {{ $tanggal }}<br>
            Ketua Program Studi D4 Teknik Informatika<br><br><br><br>
            <strong>(_________________________)</strong>
        </td>
    </tr>
</table>

</body>
</html>