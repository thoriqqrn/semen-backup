@extends('admin.layouts.app')

@section('title', 'Daftar Peserta Haji')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        {{-- Judulnya sekarang statis, tidak perlu logika aneh-aneh --}}
        <h1 class="h3 mb-0 fw-bold" style="color: #28a745;">
            Daftar Peserta Haji Diterima
        </h1>
        <div>
            <button id="exportExcelBtn" class="btn btn-success">
                <i class="fas fa-file-excel me-2"></i> Ekspor Excel
            </button>
            <button id="exportPdfBtn" class="btn btn-danger">
                <i class="fas fa-file-pdf me-2"></i> Ekspor PDF
            </button>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="pendaftarTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Kode</th>
                            <th>Nama Lengkap</th>
                            <th>No. HP</th>
                            <th>Status</th>
                            <th>Tgl Daftar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pendaftars as $pendaftar)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><span class="fw-bold">{{ $pendaftar->kode_pendaftaran }}</span></td>
                                <td>{{ $pendaftar->nama_lengkap }}</td>
                                <td>{{ $pendaftar->nomor_hp }}</td>
                                <td>
                                    <span class="badge bg-success">{{ $pendaftar->status_pendaftaran }}</span>
                                </td>
                                <td>{{ $pendaftar->created_at->format('d M Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('admin.pendaftar.show', $pendaftar->id) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">
                                    Belum ada peserta haji yang diterima.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Script untuk DataTables dan Ekspor (Sama seperti sebelumnya) --}}
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

<script>
    $(document).ready(function() {
        var exportTitle = 'Daftar Peserta Haji Diterima';
        var table = $('#pendaftarTable').DataTable({
            "language": { "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json" },
            dom: 'Bfrtip',
            buttons: [
                { extend: 'excelHtml5', title: exportTitle, exportOptions: { columns: [0, 1, 2, 3, 4, 5] } },
                { extend: 'pdfHtml5', title: exportTitle, orientation: 'landscape', pageSize: 'LEGAL', exportOptions: { columns: [0, 1, 2, 3, 4, 5] } }
            ]
        });
        $('.dt-buttons').hide();
        $('#exportExcelBtn').on('click', function() { table.button('.buttons-excel').trigger(); });
        $('#exportPdfBtn').on('click', function() { table.button('.buttons-pdf').trigger(); });
    });
</script>
@endpush