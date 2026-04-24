@extends('layouts.main')

@section('title', 'Profil Kami | KBIHU Aswaja')

@section('content')

    <!-- 1. Page Header -->
    <div class="py-5 text-center" style="background-color: #f0f4f2;">
        <div class="container">
            <h1 class="display-4 fw-bold">Profil KBIHU ASWAJA</h1>
            <p class="lead text-muted col-lg-8 mx-auto">Mengenal lebih dekat lembaga bimbingan haji Anda yang amanah,
                profesional, dan berlandaskan Ahlussunnah wal Jama'ah.</p>
        </div>
    </div>

    <!-- 2. Profil Lembaga -->
    <!-- OPSI 2: Accordion (Hemat ruang, interaktif) -->
    <section class="py-5 my-5">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-5">
                    <img src="images/foto-pengurus.jpg"
                        class="img-fluid rounded-4 shadow-lg card-hover-effect" alt="Profil KBIHU Aswaja">
                    
                    <div class="card border-success mt-4">
                        <div class="card-body text-center">
                            <h6 class="text-success mb-2">Izin Operasional Resmi</h6>
                            <h4 class="fw-bold text-dark mb-1">2587/Kw.13.05/2024</h4>
                            <small class="text-muted">Kementerian Agama RI<br>Provinsi Jawa Timur</small>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-7">
                    <h6 class="text-success text-uppercase">Profil Kami</h6>
                    <h2 class="display-5 fw-bold mb-3">Sejarah & Legalitas</h2>
                    <p class="text-muted mb-4">Perjalanan panjang kami dalam melayani tamu Allah dengan dedikasi dan profesionalisme.</p>
                    
                    <div class="accordion" id="sejarahAccordion">
                        <div class="accordion-item border-0 shadow-sm mb-3">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#awalMula">
                                    <strong>🌱 Awal Mula - Program CSR</strong>
                                </button>
                            </h2>
                            <div id="awalMula" class="accordion-collapse collapse show" data-bs-parent="#sejarahAccordion">
                                <div class="accordion-body">
                                    Keberadaan KBIHU ASWAJA bermula dari kegiatan bimbingan ibadah haji yang
                                    diselenggarakan oleh PT Semen Indonesia (Persero) Tbk. yang sebenarnya
                                    merupakan program CSR perusahaan. Pada awalnya, kegiatan bimbingan ini
                                    hanyalah melayani karyawan PT. Semen Gresik waktu itu yang akan berangkat
                                    menunaikan ibadah haji. Pelayanan pembimbingan haji menjadi semakin kuat karena
                                    perusahaan sendiri miliki program memberangkatkan haji bagi karyawan yang masuk
                                    kategori Pegawai Teladan. Seiring dengan perkembangan waktu, peserta dari
                                    kegiatan pembimbingan haji ini berkembang melayani para calon jamaah haji yang
                                    berasal dari masyarakat di sekitar pabrik Semen Gresik. Bahkan berikutnya
                                    berkembang sampai pada calon jamaah haji yang berada di wilayah kabupaten
                                    Gresik dan sekitarnya.
                                </div>
                            </div>
                        </div>
    
                        <div class="accordion-item border-0 shadow-sm mb-3">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#transisi">
                                    <strong>🔄 Transisi (2020-2021)</strong>
                                </button>
                            </h2>
                            <div id="transisi" class="accordion-collapse collapse" data-bs-parent="#sejarahAccordion">
                                <div class="accordion-body">
                                    Sejalan dengan kebijakan perusahaan, terutama ketika PT. Semen Gresik (Persero)
                                    Tbk. berubah menjadi holding dan berganti nama menjadi PT Semen Indonesia
                                    (Persero) Tbk (SIG) , hal ini sangat berpengaruh pada keberadaan kegiatan
                                    penyelenggaraan haji yang selama ini ditangani oleh CSR. Di samping itu, adanya
                                    regulasi pemerintah (khususnya yang menyangkut BUMN) yang menyatakan
                                    pelarangan bagi BUMN untuk menyelenggarakan kegiatan semacam pembimbingan
                                    haji ini. Oleh karena itu, sejak tahun 2020, perusahaan tidak lagi terlibat secara
                                    langsung dalam penyelenggraan kegiatan perhajian. Sebagai gantinya, perusahaan
                                    menunjuk Yayasan Warga Islam Semen Indonesia (YWISI) Aswaja untuk mengelola
                                    kegiatan perhajian dengan subsidi dari dana CSR PT. Semen Indonesia (Persero) Tbk
                                    (SIG).
                                </div>
                            </div>
                        </div>
    
                        <div class="accordion-item border-0 shadow-sm">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#legalitas">
                                    <strong>✅ Legalitas Resmi (2024)</strong>
                                </button>
                            </h2>
                            <div id="legalitas" class="accordion-collapse collapse" data-bs-parent="#sejarahAccordion">
                                <div class="accordion-body">
                                    Pada tahun 2024, terbentuk KBIHU ASWAJA dengan izin operasional resmi dari Kementerian Agama RI Kanwil Kemenag Provinsi Jawa Timur <strong>No. 2587/Kw.13.05/2024</strong>. Kini KBIHU ASWAJA dapat menjalankan tugas Bimbingan Ibadah Haji dan Umrah secara profesional dan legal.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. Visi & Misi -->
    <section class="py-5" style="background-color: #f0f4f2;">
        <div class="container">
            <div class="text-center">
                <h2 class="fw-bold">Visi & Misi Kami</h2>
                <p class="lead text-muted">Landasan dan tujuan kami dalam melayani jamaah.</p>
            </div>
            <div class="row mt-5 g-4">
                <div class="col-lg-6">
                    <div class="bg-white p-5 rounded-custom shadow-sm h-100 card-hover-effect">
                        <div class="d-flex align-items-center mb-3">
                            <div class="fs-1 text-success me-4"><i class="fa-solid fa-eye"></i></div>
                            <h3 class="fw-bold">Visi</h3>
                        </div>
                        <p class="text-muted">Mewujudkan jamaah haji yang MANDIRI, MABRUR BERSAMA dan BERAKHLAQUL KARIMAH
                            menurut akidah dan ibadah Ahlussunnah wal Jama’ah.</p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="bg-white p-5 rounded-custom shadow-sm h-100 card-hover-effect">
                        <div class="d-flex align-items-center mb-3">
                            <div class="fs-1 text-success me-4"><i class="fa-solid fa-bullseye"></i></div>
                            <h3 class="fw-bold">Misi</h3>
                        </div>
                        <ul class="text-muted" style="list-style-position: inside; padding-left: 0;">
                            <li class="mb-2 d-flex">
                                <i class="fa-solid fa-check text-success mt-1 me-3"></i>
                                <span>Memberikan bimbingan dan pelatihan manasik haji dan umroh sebelum dan selama melaksanakan ibadah haji secara optimal.</span>
                            </li>
                            <li class="mb-2 d-flex">
                                <i class="fa-solid fa-check text-success mt-1 me-3"></i>
                                <span>Menyediakan sarana informasi dan pembelajaran bagi jamaah agar dapat memaksimalkan persiapan pelaksanaan ibadah haji.</span>
                            </li>
                            <li class="mb-2 d-flex">
                                <i class="fa-solid fa-check text-success mt-1 me-3"></i>
                                <span>Membangun persaudaraan, kekompakan, kebersamaan dan keakraban dalam pembimbingan ibadah haji dan umrah.</span>
                            </li>
                            <li class="mb-2 d-flex">
                                <i class="fa-solid fa-check text-success mt-1 me-3"></i>
                                <span>Mendorong terwujudnya jamaah haji yang yang memiliki pribadi istiqomah, sabar, tawadlu', peduli dan Ikhlas.</span>
                            </li>
                            <li class="mb-2 d-flex">
                                <i class="fa-solid fa-check text-success mt-1 me-3"></i>
                                <span>Menyelenggarakan kegiatan ke-Islaman pasca ibadah haji untuk mempererat ikatan silaturahmi alumni KBIH ASWAJA.</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. Tim Pengurus -->
    <section class="py-5 my-5">
        <div class="container">
            <div class="text-center">
                <h2 class="fw-bold">Tim Pembimbing & Pengurus</h2>
                <p class="lead text-muted">Dipandu oleh para asatidz yang ahli dan berpengalaman.</p>
            </div>

            <div class="row mt-5 g-5 justify-content-center">
                <!-- Kartu Ketua -->
                <div class="col-lg-3 col-md-5">
                    <div class="profile-card">
                        <img src="{{ asset('images/foto-ketua.jpg') }}" class="profile-card-img" alt="Ketua KBIHU Aswaja">
                        <div class="profile-card-body">
                            <h5 class="fw-bold mb-0">Nixo Armadani, ST.</h5>
                            <p class="mb-0 small" style="color: rgba(255,255,255,0.8);">Ketua KBIHU ASWAJA</p>
                        </div>
                    </div>
                </div>

                <!-- Kartu Pembimbing 1 -->
                <div class="col-lg-3 col-md-5">
                    <div class="profile-card">
                        <img src="{{ asset('images/foto-pembimbing-1.jpg') }}" class="profile-card-img"
                            alt="Pembimbing KBIHU Aswaja">
                        <div class="profile-card-body">
                            <h5 class="fw-bold mb-0">K.H. MUH. SUHAFIK, MA</h5>
                            <p class="mb-0 small" style="color: rgba(255,255,255,0.8);">Pembimbing</p>
                        </div>
                    </div>
                </div>

                <!-- Kartu Pembimbing 2 -->
                <div class="col-lg-3 col-md-5">
                    <div class="profile-card">
                        <img src="{{ asset('images/foto-pembimbing-2.jpg') }}" class="profile-card-img"
                            alt="Pembimbing KBIHU Aswaja">
                        <div class="profile-card-body">
                            <h5 class="fw-bold mb-0">K.H. AKHMAD KHAMDANI, MA.</h5>
                            <p class="mb-0 small" style="color: rgba(255,255,255,0.8);">Pembimbing</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection