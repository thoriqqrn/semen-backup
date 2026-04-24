@extends('layouts.main')

@section('title', 'Galeri Kegiatan | KBIHU Aswaja')

@section('content')

    <!-- 1. Page Header -->
    <div class="py-5 text-center" style="background-color: #f0f4f2;">
        <div class="container">
            <h1 class="display-4 fw-bold">Galeri Kegiatan</h1>
            <p class="lead text-muted col-lg-8 mx-auto">Dokumentasi perjalanan dan bimbingan ibadah haji bersama KBIHU
                Aswaja dari tahun ke tahun.</p>
        </div>
    </div>

    <!-- 2. Galeri Section -->
    <section class="py-5 my-5">
        <div class="container">
            <!-- Filter Bar dengan Dropdown Modern -->
            <div class="row align-items-center mb-5">
                <div class="col-md-6">
                    <h2 class="fw-bold mb-0" id="gallery-year-title">Galeri Tahun {{ $selectedYear }}</h2>
                </div>
                <div class="col-md-6 d-flex justify-content-md-end">
                    <div class="dropdown">
                        <button class="btn btn-success dropdown-toggle px-4 py-2" type="button" id="galleryDropdownButton"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="me-2">Pilih Tahun:</span>
                            <span id="dropdownMenuButtonText" class="fw-bold">{{ $selectedYear }}</span>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="galleryDropdownButton">
                            @foreach ($availableYears as $year)
                                <li>
                                    <a class="dropdown-item year-filter-item {{ $year == $selectedYear ? 'active' : '' }}"
                                       href="{{ route('galeri', ['tahun' => $year]) }}" data-year="{{ $year }}">
                                        @if ($year == $selectedYear) <i class="fas fa-check me-2"></i> @endif {{ $year }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Konten Galeri -->
            <div>
                <div class="gallery-content" id="year-{{ $selectedYear }}">
                    <div class="row g-4">
                        @forelse ($galeris as $item)
                            <div class="col-lg-4 col-md-6">
                                <a href="#" class="gallery-item" data-bs-toggle="modal" data-bs-target="#galleryModal"
                                    data-file-src="{{ asset('storage/' . $item->file_path) }}"
                                    data-file-type="{{ $item->tipe }}"
                                    data-file-title="{{ $item->judul }} ({{ $item->tahun_kegiatan }})">
                                    
                                    @if($item->tipe === 'video')
                                        {{-- Tampilkan thumbnail video atau frame pertama --}}
                                        @if($item->thumbnail_path)
                                            <img src="{{ asset('storage/' . $item->thumbnail_path) }}" class="img-fluid" alt="{{ $item->judul }}">
                                        @else
                                            <video class="img-fluid" muted>
                                                <source src="{{ asset('storage/' . $item->file_path) }}" type="video/mp4">
                                            </video>
                                        @endif
                                        <div class="gallery-overlay">
                                            <div class="gallery-text">
                                                <i class="fas fa-play-circle fa-3x"></i>
                                                <h5 class="mb-0 mt-2">{{ $item->judul }}</h5>
                                            </div>
                                        </div>
                                    @else
                                        {{-- Tampilkan gambar --}}
                                        <img src="{{ asset('storage/' . $item->file_path) }}" class="img-fluid" alt="{{ $item->judul }}">
                                        <div class="gallery-overlay">
                                            <div class="gallery-text">
                                                <i class="fas fa-search-plus"></i>
                                                <h5 class="mb-0 mt-2">{{ $item->judul }}</h5>
                                            </div>
                                        </div>
                                    @endif
                                </a>
                            </div>
                        @empty
                            <div class="col-12 text-center">
                                <p class="lead text-muted">Belum ada kegiatan galeri untuk tahun ini.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal (Lightbox) untuk Galeri -->
    <div class="modal fade" id="galleryModal" tabindex="-1" aria-labelledby="galleryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="galleryModalLabel">Detail Media</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    {{-- Container untuk gambar --}}
                    <img id="modalImage" src="" class="img-fluid w-100 d-none" alt="Detail Gambar Galeri">
                    {{-- Container untuk video --}}
                    <video id="modalVideo" class="w-100 d-none" controls>
                        <source src="" type="video/mp4">
                        Browser Anda tidak mendukung video tag.
                    </video>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Update modal content when a gallery item is clicked
            var galleryModal = document.getElementById('galleryModal');
            var modalImage = document.getElementById('modalImage');
            var modalVideo = document.getElementById('modalVideo');
            var modalTitle = galleryModal.querySelector('.modal-title');
            
            galleryModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget; // Button that triggered the modal
                var fileSrc = button.getAttribute('data-file-src');
                var fileType = button.getAttribute('data-file-type');
                var fileTitle = button.getAttribute('data-file-title');

                // Reset visibility
                modalImage.classList.add('d-none');
                modalVideo.classList.add('d-none');
                
                // Update title
                modalTitle.textContent = fileTitle;

                if (fileType === 'video') {
                    // Show video
                    modalVideo.querySelector('source').src = fileSrc;
                    modalVideo.load(); // Reload video with new source
                    modalVideo.classList.remove('d-none');
                } else {
                    // Show image
                    modalImage.src = fileSrc;
                    modalImage.alt = fileTitle;
                    modalImage.classList.remove('d-none');
                }
            });

            // Pause video when modal is closed
            galleryModal.addEventListener('hide.bs.modal', function () {
                if (!modalVideo.classList.contains('d-none')) {
                    modalVideo.pause();
                    modalVideo.currentTime = 0;
                }
            });

            // Script for year filtering (now using direct links instead of JS toggling)
            // This part is mainly to handle the active class and dropdown text
            const dropdownItems = document.querySelectorAll('.year-filter-item');
            const dropdownMenuButtonText = document.getElementById('dropdownMenuButtonText');
            const galleryYearTitle = document.getElementById('gallery-year-title');

            dropdownItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    // Tidak perlu preventDefault lagi karena kita pakai href asli
                    // e.preventDefault();
                    const selectedYear = this.getAttribute('data-year');

                    // Update text and title
                    dropdownMenuButtonText.textContent = selectedYear;
                    galleryYearTitle.textContent = `Galeri Tahun ${selectedYear}`;

                    // Remove active from all and add to current
                    dropdownItems.forEach(dItem => dItem.classList.remove('active'));
                    this.classList.add('active');

                    // Redireksi ke halaman dengan parameter tahun
                    window.location.href = this.href;
                });
            });
            // Ensure the dropdown text and title are correct on initial load
            const activeYearItem = document.querySelector('.year-filter-item.active');
            if (activeYearItem) {
                const initialSelectedYear = activeYearItem.getAttribute('data-year');
                dropdownMenuButtonText.textContent = initialSelectedYear;
                galleryYearTitle.textContent = `Galeri Tahun ${initialSelectedYear}`;
            }
        });
    </script>
@endpush