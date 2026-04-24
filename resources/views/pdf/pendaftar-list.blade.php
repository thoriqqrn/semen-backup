<!DOCTYPE html>
<html>
<head>
    <title>Daftar Pendaftar Haji</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; font-size: 10px; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2 style="text-align: center;">Daftar Pendaftar Haji KBIHU Aswaja</h2>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Kode</th>
                <th>Nama Lengkap</th>
                <th>Status</th>
                <th>Tanggal Daftar</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pendaftars as $pendaftar)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $pendaftar->kode_pendaftaran }}</td>
                    <td>{{ $pendaftar->nama_lengkap }}</td>
                    <td>{{ $pendaftar->status_pendaftaran }}</td>
                    <td>{{ $pendaftar->created_at->format('d M Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>