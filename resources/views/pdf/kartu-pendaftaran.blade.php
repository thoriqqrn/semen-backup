<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kartu Pendaftaran Haji</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .container { border: 2px solid #000; padding: 20px; width: 100%; }
        .header { text-align: center; border-bottom: 1px solid #ccc; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { margin: 0; color: #28a745; }
        .header p { margin: 5px 0 0; }
        .content-table { width: 100%; border-collapse: collapse; }
        .content-table td { padding: 8px; vertical-align: top; }
        .content-table td:first-child { font-weight: bold; width: 35%; }
        .barcode-section { text-align: center; margin-top: 25px; }
        .barcode-section p { margin-bottom: 5px; font-weight: bold; }
        .footer { text-align: center; margin-top: 30px; font-size: 10px; color: #777; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>KBIHU ASWAJA</h1>
            <p>Kelompok Bimbingan Ibadah Haji dan Umrah Ahlussunnah wal Jama'ah</p>
            <h2>BUKTI PENDAFTARAN ONLINE</h2>
        </div>

        <table class="content-table">
            <tr>
                <td>NAMA LENGKAP</td>
                <td>: {{ $pendaftar->nama_lengkap }}</td>
            </tr>
            <tr>
                <td>TEMPAT, TGL LAHIR</td>
                <td>: {{ $pendaftar->tempat_lahir }}, {{ \Carbon\Carbon::parse($pendaftar->tanggal_lahir)->format('d F Y') }}</td>
            </tr>
            <tr>
                <td>NOMOR PORSI HAJI</td>
                <td>: {{ $pendaftar->nomor_porsi_haji }}</td>
            </tr>
            <tr>
                <td>TANGGAL DAFTAR</td>
                <td>: {{ $pendaftar->created_at->format('d F Y, H:i') }} WIB</td>
            </tr>
        </table>

        <div class="barcode-section">
            <p>KODE PENDAFTARAN ANDA</p>
            {{-- DomPDF tidak bisa render QR Code dari JS. Kita tampilkan sebagai teks besar --}}
            <div style="font-size: 24px; font-weight: bold; letter-spacing: 2px; border: 1px solid #ccc; padding: 10px; margin: 0 auto; display: inline-block;">
                {{ $pendaftar->kode_pendaftaran }}
            </div>
            <p style="font-size: 10px; margin-top: 10px;">Gunakan kode ini untuk mengecek status pendaftaran Anda.</p>
        </div>

        <div class="footer">
            Ini adalah bukti pendaftaran online yang sah. Mohon simpan dengan baik.<br>
            Tim kami akan menghubungi Anda untuk proses verifikasi selanjutnya.
        </div>
    </div>
</body>
</html>