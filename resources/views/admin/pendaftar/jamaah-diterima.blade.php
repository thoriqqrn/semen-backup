@extends('admin.layouts.app')

@section('title', 'Jamaah Haji Diterima')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <h1 class="h3 mb-0 fw-bold" style="color: #28a745;">
            <i class="fas fa-check-circle me-2"></i>Jamaah Haji Diterima
        </h1>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.pendaftar.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Semua Pendaftar
            </a>
            <button id="exportExcelBtn" class="btn btn-success">
                <i class="fas fa-file-excel me-1"></i> Ekspor Excel
            </button>
            <button id="exportPdfBtn" class="btn btn-danger">
                <i class="fas fa-file-pdf me-1"></i> Ekspor PDF
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="alert alert-info mb-4">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Total Jamaah Diterima:</strong> {{ $pendaftars->count() }} orang
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="jamaahTable" width="100%" cellspacing="0">
                    <thead class="table-success">
                        <tr>
                            <th>No</th>
                            <th>Kode</th>
                            <th>Nama Lengkap</th>
                            <th>Tempat, Tgl Lahir</th>
                            <th>No. HP</th>
                            <th>No. Porsi</th>
                            <th>Tgl Diterima</th>
                            <th>Ring</th>
                            <th>Catatan Admin</th>
                            <th class="no-export">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pendaftars as $pendaftar)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><small><code>{{ $pendaftar->kode_pendaftaran }}</code></small></td>
                                <td>{{ $pendaftar->nama_lengkap }}</td>
                                <td><small>{{ $pendaftar->tempat_lahir }}, {{ \Carbon\Carbon::parse($pendaftar->tanggal_lahir)->format('d M Y') }}</small></td>
                                <td><small>{{ $pendaftar->nomor_hp }}</small></td>
                                <td><small>{{ $pendaftar->nomor_porsi_haji ?? '-' }}</small></td>
                                <td><small>{{ $pendaftar->updated_at->format('d M Y, H:i') }}</small></td>
                                <td>
                                    @if ($pendaftar->kelurahan_id)
                                        @if($pendaftar->kelurahan->ring_status == 1)
                                            <span class="badge bg-success"><i class="fas fa-star"></i> R1</span>
                                        @elseif($pendaftar->kelurahan->ring_status == 2)
                                            <span class="badge bg-info"><i class="fas fa-star"></i> R2</span>
                                        @elseif($pendaftar->kelurahan->ring_status == 3)
                                            <span class="badge bg-warning"><i class="fas fa-star"></i> R3</span>
                                        @else
                                            <span class="badge bg-primary">R{{ $pendaftar->kelurahan->ring_status }}</span>
                                        @endif
                                    @else
                                        <span class="badge bg-dark"><i class="fas fa-map-marker-alt"></i> Luar</span>
                                    @endif
                                </td>
                                <td><small>{{ $pendaftar->catatan_admin ? Str::limit($pendaftar->catatan_admin, 30) : '-' }}</small></td>
                                <td class="no-export">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.pendaftar.show', $pendaftar->id) }}" class="btn btn-sm btn-primary" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-warning" title="Ubah Status" data-bs-toggle="modal" data-bs-target="#ubahStatusModal{{ $pendaftar->id }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                    <p class="text-muted">Belum ada jamaah yang diterima.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modals Section (Outside of Table) -->
    @foreach ($pendaftars as $pendaftar)
        <div class="modal fade" id="ubahStatusModal{{ $pendaftar->id }}" tabindex="-1" aria-labelledby="ubahStatusModalLabel{{ $pendaftar->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('admin.pendaftar.ubah-status', $pendaftar->id) }}" method="POST">
                        @csrf
                        <div class="modal-header bg-warning">
                            <h5 class="modal-title" id="ubahStatusModalLabel{{ $pendaftar->id }}">
                                <i class="fas fa-edit me-2"></i>Ubah Status Pendaftar
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Mengubah status: <strong>{{ $pendaftar->nama_lengkap }}</strong>
                            </div>

                            <div class="mb-3">
                                <label class="form-label"><strong>Ubah Status Menjadi:</strong></label>
                                <select name="status_baru" class="form-select status-select" data-modal-id="{{ $pendaftar->id }}" required>
                                    <option value="">-- Pilih Status --</option>
                                    <option value="menunggu">⏳ Menunggu Verifikasi</option>
                                    <option value="ditolak">❌ Ditolak</option>
                                </select>
                            </div>

                            <div class="mb-3 alasan-container" id="alasanContainer{{ $pendaftar->id }}" style="display: none;">
                                <label class="form-label"><strong>Alasan Penolakan <span class="text-danger">*</span></strong></label>
                                <textarea name="alasan" class="form-control" rows="4" placeholder="Masukkan alasan jika status diubah ke Ditolak..."></textarea>
                                <small class="text-muted">Wajib diisi jika status diubah ke Ditolak (min 10 karakter)</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-check me-2"></i>Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

    <script>
        $(document).ready(function () {
            var table = $('#jamaahTable').DataTable({
                "language": { "url": "//cdn.datatables.net/plug-ins/1.13.6/i1n/id.json" },
                dom: 'frtip',
                order: [[6, 'desc']], // Sort by Tgl Diterima descending
                buttons: [
                    { 
                        extend: 'excelHtml5', 
                        title: 'Daftar Jamaah Haji Diterima',
                        exportOptions: { 
                            columns: ':not(.no-export)',
                            format: {
                                body: function (data) {
                                    return data.replace(/<[^>]*>/g, '').trim();
                                }
                            }
                        }
                    },
                    { 
                        extend: 'pdfHtml5', 
                        title: 'Daftar Jamaah Haji Diterima',
                        orientation: 'landscape', 
                        pageSize: 'A4',
                        exportOptions: { 
                            columns: ':not(.no-export)',
                            format: {
                                body: function (data) {
                                    return data.replace(/<[^>]*>/g, '').trim();
                                }
                            }
                        },
                        customize: function (doc) {
                            doc.defaultStyle.fontSize = 8;
                            doc.styles.tableHeader.fontSize = 9;
                            doc.styles.tableHeader.bold = true;
                            doc.styles.tableHeader.fillColor = '#28a745';
                            doc.styles.tableHeader.color = 'white';
                            // 9 kolom yang di-export (No, Kode, Nama, TTL, HP, Porsi, Tgl, Ring, Catatan) - kolom Aksi diabaikan karena .no-export
                            doc.content[1].table.widths = ['4%', '10%', '15%', '12%', '10%', '9%', '12%', '6%', '15%'];
                        }
                    }
                ]
            });
            
            $('#exportExcelBtn').on('click', function () { table.button('.buttons-excel').trigger(); });
            $('#exportPdfBtn').on('click', function () { table.button('.buttons-pdf').trigger(); });

            // Handle toggle alasan container untuk semua modal dengan event delegation
            $(document).on('change', '.status-select', function() {
                const modalId = $(this).data('modal-id');
                const status = $(this).val();
                const alasanContainer = $('#alasanContainer' + modalId);
                
                if (status === 'ditolak') {
                    alasanContainer.slideDown(300);
                    alasanContainer.find('textarea').prop('required', true);
                } else {
                    alasanContainer.slideUp(300);
                    alasanContainer.find('textarea').prop('required', false).val('');
                }
            });

            // Reset modal saat ditutup
            $('.modal').on('hidden.bs.modal', function () {
                $(this).find('select').val('');
                $(this).find('textarea').val('');
                $(this).find('.alasan-container').hide();
            });
        });
    </script>
@endpush
