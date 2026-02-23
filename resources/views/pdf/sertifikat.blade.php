<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">

<style>
@page {
    size: A4 landscape;
    margin: 25px;
}

body {
    font-family: "Times New Roman", serif;
    margin: 0;
}

.container {
    border: 10px solid #0b3d91;
    padding: 15px;
}

.inner {
    border: 4px solid #d4af37;
    padding: 50px 60px;
    text-align: center;
}

.kop {
    font-size: 16px;
    font-weight: bold;
    color: #0b3d91;
    line-height: 1.5;
}

.title {
    font-size: 48px;
    font-weight: bold;
    letter-spacing: 6px;
    margin: 40px 0 10px 0;
    color: #0b3d91;
}

.subtitle {
    font-size: 20px;
    margin-top: 10px;
}

.nama {
    font-size: 36px;
    font-weight: bold;
    color: #d4af37;
    margin: 25px 0;
    text-decoration: underline;
}

.deskripsi {
    font-size: 18px;
    margin-top: 20px;
    line-height: 1.7;
}

.signature-block {
    margin-top: 50px;
    text-align: right;
    font-size: 16px;
}
</style>

</head>
<body>

<div class="container">
<div class="inner">

    <div class="kop">
        FAKULTAS VOKASI<br>
        PROGRAM STUDI D4 TEKNIK INFORMATIKA<br>
        UNIVERSITAS AIRLANGGA
    </div>

    <div class="title">SERTIFIKAT</div>

    <div class="subtitle">Diberikan Kepada</div>

    <div class="nama">{{ $nama }}</div>

    <div class="deskripsi">
        Atas partisipasi aktif dan kontribusi dalam kegiatan akademik
        Program Studi D4 Teknik Informatika Fakultas Vokasi
        Universitas Airlangga.
    </div>

    <div class="signature-block">
    Surabaya, {{ $tanggal }}<br><br>
    Koordinator Program Studi<br><br><br>
    <strong>(_________________________)</strong>
    </div>

</div>
</div>

</body>
</html>