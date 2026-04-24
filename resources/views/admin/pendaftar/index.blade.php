@extends('admin.layouts.app')

@section('title', 'Manajemen Pendaftar')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <h1 class="h3 mb-0 fw-bold" style="color: #28a745;">
            {{ request()->routeIs('admin.pendaftar.peserta') ? 'Daftar Peserta Haji Diterima' : 'Manajemen Pendaftar Haji' }}
        </h1>
        <div class="d-flex flex-wrap gap-2">
            <button id="exportExcelBtn" class="btn btn-success">
                <i class="fas fa-file-excel me-1"></i> <span class="d-none d-sm-inline">Ekspor</span> Excel
            </button>
            <button id="exportPdfBtn" class="btn btn-danger">
                <i class="fas fa-file-pdf me-1"></i> <span class="d-none d-sm-inline">Ekspor</span> PDF
            </button>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="pendaftarTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Lengkap</th>
                            <th>Tempat Lahir</th>
                            <th>Tgl Lahir</th>
                            <th>Alamat</th>
                            <th>No. Porsi</th>
                            <th>No. HP</th>
                            <th>Tgl Daftar</th>
                            <th>Kecamatan</th>
                            <th>Kelurahan</th>
                            <th>Ring</th>
                            <th>Status</th>
                            <th class="no-export">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pendaftars as $pendaftar)
                            {{-- Memberi highlight pada baris jika pendaftar adalah RING 1 --}}
                            <tr class="{{ ($pendaftar->kelurahan_id && $pendaftar->kelurahan->ring_status == 1) ? 'table-success' : '' }}">
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $pendaftar->nama_lengkap }}</td>
                                <td><small>{{ $pendaftar->tempat_lahir }}</small></td>
                                <td><small>{{ \Carbon\Carbon::parse($pendaftar->tanggal_lahir)->format('d M Y') }}</small></td>
                                <td><small>{{ Str::limit($pendaftar->alamat, 40) }}</small></td>
                                <td><small>{{ $pendaftar->nomor_porsi_haji ?? '-' }}</small></td>
                                <td><small>{{ $pendaftar->nomor_hp }}</small></td>
                                <td><small>{{ $pendaftar->created_at->format('d M Y') }}</small></td>
                                <td>
                                    <small>
                                        @if ($pendaftar->kelurahan_id)
                                            Kec. {{ $pendaftar->kelurahan->kecamatan->nama_kecamatan ?? '-' }}
                                        @else
                                            Kec. {{ $pendaftar->kecamatan_manual }}
                                        @endif
                                    </small>
                                </td>
                                <td>
                                    <small>
                                        @if ($pendaftar->kelurahan_id)
                                            {{ $pendaftar->kelurahan->nama_kelurahan ?? '-' }}
                                        @else
                                            {{ $pendaftar->kelurahan_manual }}
                                        @endif
                                    </small>
                                </td>
                                <td>
                                    @if ($pendaftar->kelurahan_id)
                                        @if($pendaftar->kelurahan->ring_status == 1)
                                            <span class="badge bg-success"><i class="fas fa-star"></i> R1</span>
                                        @elseif($pendaftar->kelurahan->ring_status == 2)
                                            <span class="badge bg-info"><i class="fas fa-star"></i> R2</span>
                                        @elseif($pendaftar->kelurahan->ring_status == 3)
                                            <span class="badge bg-warning"><i class="fas fa-star"></i> R3</span>
                                        @elseif($pendaftar->kelurahan->ring_status)
                                            <span class="badge bg-primary">R{{ $pendaftar->kelurahan->ring_status }}</span>
                                        @else
                                            <span class="badge bg-secondary">No Ring</span>
                                        @endif
                                    @else
                                        <span class="badge bg-dark"><i class="fas fa-map-marker-alt"></i> Luar</span>
                                    @endif
                                </td>
                                <td>
                                    @if($pendaftar->status_pendaftaran === 'diterima')
                                        <span class="badge bg-success">✅ Diterima</span>
                                    @elseif($pendaftar->status_pendaftaran === 'ditolak')
                                        <span class="badge bg-danger">❌ Ditolak</span>
                                    @else
                                        <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Verifikasi</span>
                                    @endif
                                </td>
                                <td class="no-export">
                                    <a href="{{ route('admin.pendaftar.show', $pendaftar->id) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    {{-- Script untuk DataTables dan Ekspor via CDN --}}
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

    <script>
        $(document).ready(function () {
            var exportTitle = '{{ request()->routeIs("admin.pendaftar.peserta") ? "Daftar Peserta Haji Diterima" : "Daftar Pendaftar Haji" }}';
            var table = $('#pendaftarTable').DataTable({
                "language": { "url": "//cdn.datatables.net/plug-ins/1.13.6/i1n/id.json" },
                dom: 'frtip',
                buttons: [
                    { 
                        extend: 'excelHtml5', 
                        title: exportTitle,
                        exportOptions: { 
                            columns: ':not(.no-export)',
                            format: {
                                body: function (data, row, column, node) {
                                    // Hapus HTML tags dari badge untuk export yang bersih
                                    return data.replace(/<[^>]*>/g, '').trim();
                                }
                            }
                        }
                    },
                    { 
                        extend: 'pdfHtml5', 
                        title: exportTitle,
                        orientation: 'landscape', 
                        pageSize: 'A4',
                        exportOptions: { 
                            columns: ':not(.no-export)',
                            format: {
                                body: function (data, row, column, node) {
                                    // Hapus HTML tags dari badge untuk export yang bersih
                                    return data.replace(/<[^>]*>/g, '').trim();
                                }
                            }
                        },
                        customize: function (doc) {
                            // Styling PDF
                            doc.defaultStyle.fontSize = 8;
                            doc.styles.tableHeader.fontSize = 9;
                            doc.styles.tableHeader.bold = true;
                            doc.styles.tableHeader.fillColor = '#28a745';
                            doc.styles.tableHeader.color = 'white';
                            // Update: 12 kolom (No, Nama, Tempat, Tgl, Alamat, Porsi, HP, Tgl Daftar, Kec, Kel, Ring, Status)
                            doc.content[1].table.widths = ['3%', '11%', '8%', '7%', '11%', '7%', '8%', '7%', '9%', '9%', '6%', '8%'];
                        }
                    }
                ]
            });
            
            // Trigger ekspor dari tombol custom
            $('#exportExcelBtn').on('click', function () { table.button('.buttons-excel').trigger(); });
            $('#exportPdfBtn').on('click', function () { table.button('.buttons-pdf').trigger(); });
        });
    </script>
@endpush