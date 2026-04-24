@extends('admin.layouts.app')
@section('title', 'Pengaturan Slot Pendaftar')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 fw-bold" style="color: #28a745;">Pengaturan Website</h1>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header"><h6 class="m-0 font-weight-bold text-success">Atur Slot Pendaftar Haji</h6></div>
        <div class="card-body">
            <form action="{{ route('admin.settings.update') }}" method="POST">
                @csrf
                @method('PUT')
                
                {{-- KUOTA RING 1 --}}
                <div class="mb-3">
                    <label for="kuota_ring1" class="form-label fw-bold">Kuota Ring 1 (Kelurahan Prioritas)</label>
                    <input type="number" name="kuota_ring1" id="kuota_ring1" class="form-control"
                           value="{{ old('kuota_ring1', $kuota_ring1->value ?? 30) }}" required min="0">
                    <small class="form-text text-muted">Jumlah kursi maksimal untuk warga kelurahan prioritas (Ring 1).</small>
                </div>
                
                {{-- KUOTA UMUM --}}
                <div class="mb-3">
                    <label for="kuota_umum" class="form-label fw-bold">Kuota Umum (Luar Ring 1)</label>
                    <input type="number" name="kuota_umum" id="kuota_umum" class="form-control"
                           value="{{ old('kuota_umum', $kuota_umum->value ?? 20) }}" required min="0">
                    <small class="form-text text-muted">Jumlah kursi maksimal untuk pendaftar luar Ring 1 (termasuk luar kota).</small>
                </div>
                
                {{-- TOTAL OTOMATIS --}}
                <div class="alert alert-info">
                    <i class="fas fa-calculator"></i> <strong>Total Kuota:</strong> 
                    <span id="total_kuota" class="fs-5 fw-bold">{{ ($kuota_ring1->value ?? 30) + ($kuota_umum->value ?? 20) }}</span> kursi
                </div>
                
                <hr class="my-4">
                
                {{-- MAX SLOTS (Auto-calculated from Ring 1 + Umum) --}}
                <input type="hidden" name="max_slots" id="max_slots" value="{{ ($kuota_ring1->value ?? 30) + ($kuota_umum->value ?? 20) }}">
                
                {{-- PORSI TERTINGGI --}}
                <div class="mb-3">
                    <label for="max_porsi" class="form-label">Porsi Tertinggi (Maksimal)</label>
                    <input type="number" name="max_porsi" id="max_porsi" class="form-control"
                           value="{{ old('max_porsi', $max_porsi->value ?? 999999) }}" required>
                    <small class="form-text text-muted">Pendaftar dengan nomor porsi melebihi angka ini akan ditolak otomatis. Contoh: Jika diisi 15000000, maka porsi 15000001 ke atas akan tertolak.</small>
                </div>
                
                <button type="submit" class="btn btn-success btn-lg"><i class="fas fa-save"></i> Simpan Pengaturan</button>
            </form>
        </div>
    </div>
    
    {{-- JavaScript untuk hitung total otomatis --}}
    <script>
        const inputRing1 = document.getElementById('kuota_ring1');
        const inputUmum = document.getElementById('kuota_umum');
        const totalKuotaSpan = document.getElementById('total_kuota');
        const maxSlotsHidden = document.getElementById('max_slots');
        
        function updateTotal() {
            const ring1 = parseInt(inputRing1.value) || 0;
            const umum = parseInt(inputUmum.value) || 0;
            const total = ring1 + umum;
            
            totalKuotaSpan.textContent = total;
            maxSlotsHidden.value = total; // Update hidden input max_slots
        }
        
        // Trigger saat user mengetik
        inputRing1.addEventListener('input', updateTotal);
        inputUmum.addEventListener('input', updateTotal);
        
        // Trigger saat halaman load
        updateTotal();
    </script>
</div>
@endsection