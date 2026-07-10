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
                            <th>No</th>
                            <th>Kode Pendaftaran</th>
                            <th>Kode Referral</th>
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
                        @forelse ($pendaftars as $pendaftar)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-2 fw-semibold">
                                        {{ $pendaftar->kode_pendaftaran ?? '-' }}
                                    </span>
                                </td>
                                <td><small>{{ $pendaftar->referral_code ?? '-' }}</small></td>
                                <td>{{ $pendaftar->nama_lengkap }}</td>
                                <td><small>{{ $pendaftar->tempat_lahir }}</small></td>
                                <td><small>{{ $pendaftar->tanggal_lahir ? \Carbon\Carbon::parse($pendaftar->tanggal_lahir)->format('d M Y') : '-' }}</small></td>
                                <td><small>{{ Str::limit($pendaftar->alamat, 40) }}</small></td>
                                <td><small>{{ $pendaftar->nomor_porsi_haji ?? '-' }}</small></td>
                                <td>
                                    <small>{{ $pendaftar->nomor_hp }}</small>
                                    @php
                                        $waNumber = preg_replace('/\D+/', '', $pendaftar->nomor_hp ?? '');
                                        if (str_starts_with($waNumber, '0')) {
                                            $waNumber = '62' . substr($waNumber, 1);
                                        }
                                    @endphp
                                    @if ($waNumber)
                                        <a href="https://wa.me/{{ $waNumber }}" target="_blank" class="text-success ms-2" title="WhatsApp">
                                            <i class="fab fa-whatsapp"></i>
                                        </a>
                                    @endif
                                </td>
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
                                    <span class="badge bg-success">✅ Diterima</span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.pendaftar.show', $pendaftar->id) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="15" class="text-center">
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
            dom: 'frtip',
            buttons: [
                { extend: 'excelHtml5', title: exportTitle, exportOptions: { columns: ':not(.no-export)' } },
                { extend: 'pdfHtml5', title: exportTitle, orientation: 'landscape', pageSize: 'LEGAL', exportOptions: { columns: ':not(.no-export)' } }
            ]
        });
        $('#exportExcelBtn').on('click', function() { table.button('.buttons-excel').trigger(); });
        $('#exportPdfBtn').on('click', function() { table.button('.buttons-pdf').trigger(); });
    });
</script>
@endpush