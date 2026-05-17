@extends('admin.layouts.app')
@section('title', 'Pengaturan RING Wilayah')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 fw-bold" style="color: #28a745;">Pengaturan RING Wilayah</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Terdapat kesalahan:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        $ring1Count = $kelurahans->where('ring_status', 1)->count();
        $nonRingCount = $kelurahans->filter(function ($kelurahan) {
            return empty($kelurahan->ring_status) || (int) $kelurahan->ring_status !== 1;
        })->count();
    @endphp

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">Ring 1</small>
                            <div class="fs-4 fw-bold text-success">{{ $ring1Count }}</div>
                        </div>
                        <i class="fas fa-star text-success fs-3"></i>
                    </div>
                    <button class="btn btn-sm btn-outline-success mt-3 ring-summary-btn" type="button" data-bs-toggle="collapse" data-bs-target="#kelurahanCrudCollapse" data-ring-filter="ring1" aria-expanded="false" aria-controls="kelurahanCrudCollapse">
                        Lihat Kelurahan Ring
                    </button>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">Non Ring</small>
                            <div class="fs-4 fw-bold text-dark">{{ $nonRingCount }}</div>
                        </div>
                        <i class="fas fa-map-marker-alt text-dark fs-3"></i>
                    </div>
                    <button class="btn btn-sm btn-outline-dark mt-3 ring-summary-btn" type="button" data-bs-toggle="collapse" data-bs-target="#kelurahanCrudCollapse" data-ring-filter="nonring" aria-expanded="false" aria-controls="kelurahanCrudCollapse">
                        Lihat Non Ring
                    </button>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">Total Kelurahan</small>
                            <div class="fs-4 fw-bold text-primary">{{ $kelurahans->count() }}</div>
                        </div>
                        <i class="fas fa-layer-group text-primary fs-3"></i>
                    </div>
                    <button class="btn btn-sm btn-outline-primary mt-3" type="button" data-bs-toggle="collapse" data-bs-target="#wilayahRingCollapse" aria-expanded="false" aria-controls="wilayahRingCollapse">
                        Atur Ring Massal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="accordion" id="ringWilayahAccordion">
        <div class="card shadow mb-4">
            <div class="card-header" id="kecamatanCrudHeading">
                <h6 class="m-0 font-weight-bold text-success">
                    <button class="btn btn-link text-success text-decoration-none" type="button" data-bs-toggle="collapse" data-bs-target="#kecamatanCrudCollapse" aria-expanded="false" aria-controls="kecamatanCrudCollapse">
                        CRUD Kecamatan
                    </button>
                </h6>
            </div>
            <div id="kecamatanCrudCollapse" class="collapse" aria-labelledby="kecamatanCrudHeading" data-bs-parent="#ringWilayahAccordion">
                <div class="card-body">
                    <form action="{{ route('admin.wilayah.kecamatan.store') }}" method="POST" class="row g-2 mb-3">
                        @csrf
                        <div class="col-md-8">
                            <input type="text" name="nama_kecamatan" class="form-control" placeholder="Nama Kecamatan" required>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-success w-100">Tambah Kecamatan</button>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="kecamatanTable">
                            <thead>
                                <tr>
                                    <th style="width: 8%;">No</th>
                                    <th>Nama Kecamatan</th>
                                    <th style="width: 28%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($kecamatans as $kecamatan)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <span class="d-none dt-search-value">{{ $kecamatan->nama_kecamatan }}</span>
                                            <form action="{{ route('admin.wilayah.kecamatan.update', $kecamatan->id) }}" method="POST" class="d-flex gap-2">
                                                @csrf
                                                @method('PUT')
                                                <input type="text" name="nama_kecamatan" class="form-control form-control-sm" value="{{ $kecamatan->nama_kecamatan }}" required>
                                                <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
                                            </form>
                                        </td>
                                        <td>
                                            <form action="{{ route('admin.wilayah.kecamatan.destroy', $kecamatan->id) }}" method="POST" onsubmit="return confirm('Hapus kecamatan ini? Kelurahan terkait juga akan terhapus.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header" id="kelurahanCrudHeading">
                <h6 class="m-0 font-weight-bold text-success">
                    <button class="btn btn-link text-success text-decoration-none" type="button" data-bs-toggle="collapse" data-bs-target="#kelurahanCrudCollapse" aria-expanded="false" aria-controls="kelurahanCrudCollapse">
                        CRUD Kelurahan / Desa
                    </button>
                </h6>
            </div>
            <div id="kelurahanCrudCollapse" class="collapse" aria-labelledby="kelurahanCrudHeading" data-bs-parent="#ringWilayahAccordion">
                <div class="card-body">
                    <form action="{{ route('admin.wilayah.kelurahan.store') }}" method="POST" class="row g-2 mb-3">
                        @csrf
                        <div class="col-md-4">
                            <select name="kecamatan_id" class="form-select" required>
                                <option value="">Pilih Kecamatan</option>
                                @foreach($kecamatans as $kecamatan)
                                    <option value="{{ $kecamatan->id }}">{{ $kecamatan->nama_kecamatan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <input type="text" name="nama_kelurahan" class="form-control" placeholder="Nama Kelurahan/Desa" required>
                        </div>
                        <div class="col-md-1">
                            <input type="number" name="ring_status" class="form-control" min="1" placeholder="R">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-success w-100">Tambah</button>
                        </div>
                    </form>

                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <button type="button" class="btn btn-sm btn-outline-secondary ring-filter-btn active" data-ring-filter="all">Semua</button>
                        <button type="button" class="btn btn-sm btn-outline-success ring-filter-btn" data-ring-filter="ring1">Ring 1</button>
                        <button type="button" class="btn btn-sm btn-outline-dark ring-filter-btn" data-ring-filter="nonring">Non Ring</button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="kelurahanCrudTable">
                            <thead>
                                <tr>
                                    <th style="width: 6%;">No</th>
                                    <th style="width: 26%;">Kecamatan</th>
                                    <th>Kelurahan/Desa</th>
                                    <th style="width: 10%;">Ring</th>
                                    <th style="width: 20%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($kelurahans as $kelurahan)
                                    @php $formId = 'form-update-kelurahan-' . $kelurahan->id; @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <span class="d-none dt-search-value">{{ $kelurahan->kecamatan->nama_kecamatan }}</span>
                                            <select name="kecamatan_id" form="{{ $formId }}" class="form-select form-select-sm" required>
                                                    @foreach($kecamatans as $kecamatan)
                                                        <option value="{{ $kecamatan->id }}" {{ $kelurahan->kecamatan_id == $kecamatan->id ? 'selected' : '' }}>
                                                            {{ $kecamatan->nama_kecamatan }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                        </td>
                                        <td>
                                            <span class="d-none dt-search-value">{{ $kelurahan->nama_kelurahan }}</span>
                                            <input type="text" name="nama_kelurahan" form="{{ $formId }}" class="form-control form-control-sm" value="{{ $kelurahan->nama_kelurahan }}" required>
                                        </td>
                                        <td>
                                            <span class="d-none dt-search-value">{{ $kelurahan->ring_status }}</span>
                                            <input type="number" name="ring_status" form="{{ $formId }}" class="form-control form-control-sm" value="{{ $kelurahan->ring_status }}" min="1" placeholder="-">
                                        </td>
                                        <td>
                                            <form id="{{ $formId }}" action="{{ route('admin.wilayah.kelurahan.update', $kelurahan->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                            </form>
                                            <button type="submit" form="{{ $formId }}" class="btn btn-sm btn-primary me-1">Simpan</button>
                                            <form action="{{ route('admin.wilayah.kelurahan.destroy', $kelurahan->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus kelurahan/desa ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header" id="wilayahRingHeading">
                <h6 class="m-0 font-weight-bold text-success">
                    <button class="btn btn-link text-success text-decoration-none" type="button" data-bs-toggle="collapse" data-bs-target="#wilayahRingCollapse" aria-expanded="false" aria-controls="wilayahRingCollapse">
                        Atur Status RING untuk Kelurahan
                    </button>
                </h6>
            </div>
            <div id="wilayahRingCollapse" class="collapse" aria-labelledby="wilayahRingHeading" data-bs-parent="#ringWilayahAccordion">
                <div class="card-body">
                    <form action="{{ route('admin.wilayah.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="table-responsive">
                            <table class="table table-bordered" id="wilayahTable">
                                <thead>
                                    <tr>
                                        <th>Kecamatan</th>
                                        <th>Kelurahan</th>
                                        <th style="width: 15%;">Status RING (1, 2, 3, dst)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($kelurahans as $kelurahan)
                                    <tr>
                                        <td>{{ $kelurahan->kecamatan->nama_kecamatan }}</td>
                                        <td>{{ $kelurahan->nama_kelurahan }}</td>
                                        <td>
                                            <input type="number" name="rings[{{ $kelurahan->id }}]" class="form-control" value="{{ $kelurahan->ring_status }}" min="1">
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">Simpan Semua Perubahan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Script untuk DataTables --}}
<script>
    $(document).ready(function() {
        let ringFilter = 'all';

        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            if (settings.nTable.id !== 'kelurahanCrudTable') {
                return true;
            }

            if (ringFilter === 'all') {
                return true;
            }

            const rowNode = settings.aoData[dataIndex].nTr;
            const ringInput = rowNode ? rowNode.querySelector('input[name="ring_status"]') : null;
            const ringValue = ringInput ? ringInput.value.trim() : '';

            if (ringFilter === 'ring1') {
                return ringValue === '1';
            }

            if (ringFilter === 'nonring') {
                return ringValue === '' || ringValue !== '1';
            }

            return true;
        });

        const kecamatanTable = $('#kecamatanTable').DataTable({
            "language": { "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json" },
            "pageLength": 25
        });

        const kelurahanTable = $('#kelurahanCrudTable').DataTable({
            "language": { "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json" },
            "pageLength": 25
        });

        const wilayahTable = $('#wilayahTable').DataTable({
            "language": { "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json" },
            "pageLength": 50 // Tampilkan 50 entri per halaman
        });

        $('.ring-filter-btn').on('click', function() {
            $('.ring-filter-btn').removeClass('active');
            $(this).addClass('active');
            ringFilter = $(this).data('ringFilter');
            kelurahanTable.draw();
        });

        $('.ring-summary-btn').on('click', function() {
            const targetFilter = $(this).data('ringFilter');
            if (targetFilter) {
                ringFilter = targetFilter;
                $('.ring-filter-btn').removeClass('active');
                $(`.ring-filter-btn[data-ring-filter="${targetFilter}"]`).addClass('active');
                kelurahanTable.draw();
            }
        });

        $('#kecamatanTable').on('input', 'input[name="nama_kecamatan"]', function() {
            $(this).closest('td').find('.dt-search-value').text(this.value);
        });

        $('#kelurahanCrudTable').on('change', 'select[name="kecamatan_id"]', function() {
            const selectedText = $(this).find('option:selected').text();
            $(this).closest('td').find('.dt-search-value').text(selectedText);
        });

        $('#kelurahanCrudTable').on('input', 'input[name="nama_kelurahan"]', function() {
            $(this).closest('td').find('.dt-search-value').text(this.value);
        });

        $('#kelurahanCrudTable').on('input', 'input[name="ring_status"]', function() {
            $(this).closest('td').find('.dt-search-value').text(this.value);
            kelurahanTable.draw(false);
        });

        const kelurahanCollapse = document.getElementById('kelurahanCrudCollapse');
        if (kelurahanCollapse) {
            kelurahanCollapse.addEventListener('shown.bs.collapse', function() {
                kelurahanTable.columns.adjust().draw(false);
            });
        }

        const kecamatanCollapse = document.getElementById('kecamatanCrudCollapse');
        if (kecamatanCollapse) {
            kecamatanCollapse.addEventListener('shown.bs.collapse', function() {
                kecamatanTable.columns.adjust().draw(false);
            });
        }

        const wilayahCollapse = document.getElementById('wilayahRingCollapse');
        if (wilayahCollapse) {
            wilayahCollapse.addEventListener('shown.bs.collapse', function() {
                wilayahTable.columns.adjust().draw(false);
            });
        }
    });
</script>
@endpush