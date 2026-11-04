<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard SiSehat - Tambah Data UMKM</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="{{ asset('css/responsive-layout.css') }}" rel="stylesheet">

    <style>
        /* Reset & Base */
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { display: flex; height: 100vh; background-color: #f4f7f6; overflow: hidden; }

        /* ================= SIDEBAR ================= */
        .sidebar {
            width: 280px; background-color: #ffffff; border-right: 1px solid #f1f5f9;
            display: flex; flex-direction: column; padding: 25px 15px; flex-shrink: 0; z-index: 10;
        }
        .logo-box { display: flex; justify-content: center; align-items: center; margin-bottom: 25px; width: 100%; }
        .logo-main { width: 150px; height: auto; display: block; }

        .btn-primary-custom {
            background-color: #0a4ebd; color: white; border: none; padding: 12px; border-radius: 8px;
            cursor: pointer; font-weight: 700; font-size: 13px; margin-bottom: 20px; width: 100%; transition: 0.3s;
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .btn-primary-custom:hover { background-color: #083ba1; transform: translateY(-1px); }
                .sidebar-divider { border: none; border-top: 1.5px solid #e5e7eb; margin-top: 10px; margin-bottom: 15px; width: 100%; }

        .nav-list { list-style: none; flex-grow: 1; margin-top: 10px; }
        .nav-item { 
            display: flex; align-items: center; gap: 12px; padding: 12px 15px; 
            margin-bottom: 8px; border-radius: 10px; cursor: pointer; 
            color: #718096; transition: all 0.3s ease; 
            font-weight: 500;
        }
        .nav-icon {
            width: 20px; height: 20px; object-fit: contain;
            filter: grayscale(100%); opacity: 0.6; transition: 0.3s;
        }
        .nav-item span { font-size: 14px; line-height: normal; }
        
        .nav-item.active { 
            background-color: #eef2ff; 
            color: #1d4ed8; 
            font-weight: 700;
        }
        .nav-item.active .nav-icon { 
            filter: none; 
            opacity: 1; 
            filter: brightness(0) saturate(100%) invert(26%) sepia(89%) saturate(1966%) hue-rotate(211deg) brightness(97%) contrast(101%);
        }
        
        .nav-item:hover:not(.active) { background-color: #f8fafc; color: #1e293b; }
        .nav-item:hover .nav-icon { opacity: 0.9; filter: grayscale(0%); }

        .profile-card { 
            margin-top: auto; 
            padding: 12px 15px; 
            background-color: #eef2ff; 
            border-radius: 12px; 
            display: flex; 
            align-items: center; 
            gap: 12px;
            cursor: pointer;
            transition: 0.3s;
        }
        .profile-card:hover { background-color: #e0e7ff; }
        .avatar { width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; overflow: hidden; }
        .avatar img { width: 100%; height: 100%; object-fit: cover; }
        .profile-info { flex-grow: 1; text-align: left; }
        .profile-info h4 { font-size: 14px; color: #1e293b; font-weight: 800; line-height: 1.2; }
        .profile-info p { font-size: 11px; color: #64748b; font-weight: 600; }
        
        .logout-btn-small {
            background: none;
            border: none;
            color: #dc3545;
            cursor: pointer;
            padding: 8px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: 0.2s;
        }
        .logout-btn-small:hover {
            background-color: #fee2e2;
        }
        .logout-btn-small svg {
            width: 20px;
            height: 20px;
        }

        /* ================= MAIN CONTENT ================= */
        .content-area { flex-grow: 1; padding: 40px; overflow-y: auto; scroll-behavior: smooth; }
        
        .step-badge {
            display: inline-flex; align-items: center; gap: 6px; background-color: #eef2ff; 
            padding: 6px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; color: #0d47a1; margin-bottom: 15px;
        }
        .step-badge::before { content: ""; width: 6px; height: 6px; border-radius: 50%; background-color: #0d47a1; }

        .page-header { margin-bottom: 30px; }
        .page-header h1 { font-size: 26px; color: #1e293b; font-weight: 800; margin-bottom: 8px; }
        .page-header p { color: #64748b; font-size: 14px; line-height: 1.5; }

        /* ================= LAYOUT 2 KOLOM ================= */
        .main-grid {
            display: grid;
            grid-template-columns: 1.2fr 1fr; /* Form sedikit lebih lebar dari info card */
            gap: 25px;
            align-items: flex-start;
        }

        /* --- FORM SECTION (KIRI) --- */
        .form-card {
            background-color: #ffffff;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            border: 1px solid #f1f5f9;
        }

        .form-group { margin-bottom: 20px; }
        .form-row { display: flex; gap: 20px; margin-bottom: 20px; }
        .form-row .form-group { flex: 1; margin-bottom: 0; }

        label { display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 8px; }
        
        input[type="text"], input[type="number"] {
            width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 8px;
            font-size: 14px; color: #334155; outline: none; transition: border-color 0.2s;
        }

        select {
            width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 8px;
            font-size: 14px; color: #334155; outline: none; transition: border-color 0.2s;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23475569' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-size: 16px;
            background-position: right 15px center;
        }
        input::placeholder { color: #94a3b8; }
        input:focus, select:focus { border-color: #0d47a1; box-shadow: 0 0 0 3px rgba(13, 71, 161, 0.1); }

        /* Banner Verifikasi Otomatis */
        .verification-banner {
            background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px;
            display: flex; gap: 15px; align-items: center; margin-bottom: 30px;
        }
        /* [PERUBAHAN 2 - CSS PARTIAL] Wadah ikon baru (Placeholder) */
        .veri-icon {
            width: 80px; height: 50px; border-radius: 8px; flex-shrink: 0;
            display: flex; justify-content: center; align-items: center;
            overflow: hidden;
        }
        /* CSS untuk gambar di dalam wadah ikon */
        .veri-icon img {
            width: 100%; height: 100%; object-fit: cover;
            display: block; 
            border-radius: 8px;
        }
        .veri-text h5 { font-size: 13px; font-weight: 700; color: #0f172a; margin-bottom: 4px; }
        .veri-text p { font-size: 11px; color: #64748b; line-height: 1.5; }

        /* Action Buttons */
        .form-actions { display: flex; justify-content: flex-end; gap: 15px; align-items: center; }
        .btn-cancel { background: transparent; border: none; color: #475569; font-weight: 600; font-size: 14px; cursor: pointer; padding: 10px 15px; transition: 0.2s; }
        .btn-cancel:hover { color: #0f172a; }
        .btn-submit { background-color: #2563eb; color: white; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 600; font-size: 14px; cursor: pointer; transition: 0.2s; box-shadow: 0 2px 4px rgba(37, 99, 235, 0.2); }
        .btn-submit:hover { background-color: #1d4ed8; }

        /* --- INFO SECTION (KANAN) --- */
        .info-section { display: flex; flex-direction: column; gap: 20px; }

        /* Card Akses Tersentralisasi */
        /* [PERUBAHAN 1 - CSS FULL] Gunakan nama file yang dikonfirmasi pengguna */
        .info-card-main {
            background: linear-gradient(rgba(0,0,0,0.1), rgba(0,0,0,0.7)), url('{{ asset('images/Akses_Tersentralisasi.jpeg') }}');
            background-size: cover; background-position: center;
            height: 350px; border-radius: 20px; padding: 30px; color: white;
            display: flex; flex-direction: column; justify-content: flex-end;
        }
        /* Hapus atau komentari pseudo-elemen gradien kotak asli karena tidak lagi relevan */
        /* .info-card-main::before { ... } */
        
        .info-content { position: relative; z-index: 2; }
        .icon-shield-large { width: 32px; height: 32px; background-color: rgba(255,255,255,0.2); border-radius: 50%; display: flex; justify-content: center; align-items: center; margin-bottom: 15px; }
        .info-content h3 { font-size: 18px; font-weight: 700; margin-bottom: 10px; }
        .info-content p { font-size: 12px; line-height: 1.6; color: #cbd5e1; }

        /* Card Status Keamanan */
        .security-card {
            background-color: #ffffff; border-radius: 12px; padding: 20px; border: 1px solid #f1f5f9; box-shadow: 0 2px 8px rgba(0,0,0,0.02);
            display: flex; gap: 15px; align-items: center;
        }
        .icon-shield-small { width: 36px; height: 36px; border-radius: 50%; background-color: #eef2ff; color: #10b981; display: flex; justify-content: center; align-items: center; font-size: 18px; flex-shrink: 0; }
        .sec-text h5 { font-size: 13px; font-weight: 700; color: #0f172a; margin-bottom: 4px; }
        .sec-text p { font-size: 11px; color: #64748b; line-height: 1.5; }

        /* ================= POP-UP MODAL ================= */
        .modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.4); backdrop-filter: blur(4px);
            display: flex; justify-content: center; align-items: center;
            z-index: 9999; opacity: 0; visibility: hidden; transition: all 0.3s ease-in-out;
        }
        
        .modal-overlay.active {
            opacity: 1; visibility: visible;
        }
        
        .modal-box {
            background: #ffffff; padding: 40px 30px; border-radius: 16px;
            width: 90%; max-width: 380px; text-align: center;
            transform: translateY(-30px); transition: all 0.3s ease-in-out;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        
        .modal-overlay.active .modal-box {
            transform: translateY(0);
        }
        
        .modal-icon {
            width: 70px; height: 70px; background-color: #eef2ff; color: #2563eb;
            border-radius: 50%; display: flex; justify-content: center; align-items: center;
            margin: 0 auto 20px auto;
        }
        
        .modal-title { font-size: 20px; font-weight: 800; color: #1e293b; margin-bottom: 12px; }
        .modal-desc { font-size: 14px; color: #64748b; line-height: 1.6; margin-bottom: 25px; }
        
        .btn-modal-ok {
            background-color: #2563eb; color: white; border: none; padding: 12px; width: 100%;
            border-radius: 8px; font-weight: 600; font-size: 15px; cursor: pointer;
            transition: 0.2s;
        }
        .btn-modal-ok:hover { background-color: #1d4ed8; }

    </style>
    <link href="{{ asset('css/kreva-theme.css') }}" rel="stylesheet">
</head>
<body class="kreva-analytics">

    <div class="sidebar">
        <div class="logo-box">
            <img src="{{ asset('images/kreva_logo.svg') }}" alt="KREVA" class="logo-main" onerror="this.style.display='none'; this.insertAdjacentHTML('afterend', '<h2 style=\'color:#0d47a1; font-weight:900;\'>KREVA</h2>');">
        </div>
        
        <button class="btn-primary-custom" onclick="window.location='{{ route('dashboard.tambah-umkm') }}'">+ Tambahkan UMKM Anda</button>
        <div class="sidebar-divider"></div>
        
        <ul class="nav-list">
            <li class="nav-item" onclick="window.location='{{ route('dashboard') }}'">
                <img src="{{ asset('images/Beranda_logo.svg') }}" class="nav-icon" onerror="this.src='https://cdn-icons-png.flaticon.com/512/25/25694.png'">
                <span>Beranda</span>
            </li>
            <li class="nav-item" onclick="window.location='{{ route('dashboard.asesmen') }}'">
                <img src="{{ asset('images/Asesmen_Organisasi_logo.svg') }}" class="nav-icon" onerror="this.src='https://cdn-icons-png.flaticon.com/512/1161/1161388.png'">
                <span>Asesmen Organisasi</span>
            </li>
            <li class="nav-item" onclick="window.location='{{ route('dashboard.faktor') }}'">
                <img src="{{ asset('images/6_Faktor_Penilaian_logo.svg') }}" class="nav-icon" onerror="this.src='https://cdn-icons-png.flaticon.com/512/138/138439.png'">
                <span>6 Faktor Penilaian</span>
            </li>
            <li class="nav-item" onclick="window.location='{{ route('dashboard.rekomendasi') }}'">
                <img src="{{ asset('images/Rekomendasi_logo.svg') }}" class="nav-icon" onerror="this.src='https://cdn-icons-png.flaticon.com/512/151/151300.png'">
                <span>Rekomendasi</span>
            </li>
            <li class="nav-item active" onclick="window.location='{{ route('dashboard.tambah-umkm') }}'">
                <img src="{{ asset('images/Tambah_Data_UMKM_logo.svg') }}" class="nav-icon" onerror="this.src='https://cdn-icons-png.flaticon.com/512/2990/2990315.png'">
                <span>Tambah Data UMKM</span>
            </li>
               <li class="nav-item" onclick="window.location='{{ route('dashboard.tambah-karyawan') }}'">
                <img src="{{ asset('images/Tambah_Data_Karyawan_logo.svg') }}" class="nav-icon" onerror="this.src='https://cdn-icons-png.flaticon.com/512/3135/3135715.png'">
                <span>Tambah Data Karyawan</span>
            </li>
            <li class="nav-item" onclick="window.location='{{ route('dashboard.manajemen-umkm') }}'">
                <img src="{{ asset('images/Manajemen_UMKM_logo.svg') }}" class="nav-icon" onerror="this.src='https://cdn-icons-png.flaticon.com/512/104/104646.png'">
                <span>Manajemen UMKM</span>
            </li>
        </ul>

        <div class="profile-card">
            <div class="avatar">
                <img src="{{ asset('images/profil.svg') }}" class="nav-icon">
            </div>
            <div class="profile-info">
                <h4>{{ auth()->user()->name ?? 'Riski' }}</h4>
                <x-sidebar-role-subtitle />
            </div>
            <button class="logout-btn-small" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" title="Keluar">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
            </button>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    </div>

    <div class="content-area">
        
        <div class="step-badge">LANGKAH 1: INFORMASI BISNIS</div>

        <div class="page-header">
            <h1>Tambah Data UMKM</h1>
            <p>Masukkan informasi dasar mengenai entitas usaha untuk memulai proses kurasi data.</p>
        </div>

        <div class="main-grid">
            
            <div class="form-card">
                <form id="umkmForm" action="{{ route('dashboard.simpan-umkm') }}" method="POST">
                    @csrf
                    
                    <div class="form-group">
                        <label for="nama_umkm">Nama UMKM</label>
                        <input type="text" id="nama_umkm" name="nama_umkm" placeholder="Contoh: PT Sehat Selalu">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="industry">Industri</label>
                            <select id="industry" name="industry">
                                <option value="" disabled selected>Pilih Kategori Industri</option>
                                <option value="1">Makanan dan Minuman (F&B)</option>
                                <option value="2">Fashion</option>
                                <option value="3">Wholesale & Retail</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="usia_usaha">Usia UMKM</label>
                            <select id="usia_usaha" name="usia_usaha">
                                <option value="" disabled selected>Pilih Usia Usaha</option>
                                <option value="1">< 1 Tahun</option>
                                <option value="2">1 - 3 Tahun</option>
                                <option value="3">> 3 Tahun</option>
                            </select>
                        </div>
                    </div>

                    <div class="verification-banner">
                        <div class="veri-icon">
                            <img src="{{ asset('images/Verifikasi_Otomatis.jpg') }}" alt="Ikon Verifikasi">
                        </div>
                        <div class="veri-text">
                            <h5>Verifikasi Otomatis</h5>
                            <p>Data yang Anda masukkan akan diproses melalui sistem kurasi klinis kami untuk memastikan validitas entitas sebelum masuk ke dalam database utama.</p>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn-cancel">Batal</button>
                        <button type="submit" class="btn-submit">Simpan Data</button>
                    </div>
                </form>
            </div>

            <div class="info-section">
                
                <div class="info-card-main">
                    <div class="info-content">
                        <div class="icon-shield-large">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                        </div>
                        <h3>Akses Tersentralisasi</h3>
                        <p>Semua data UMKM Anda dikelola dalam satu sistem terpusat yang terstruktur dan terintegrasi. Dengan akses yang terkendali dan real-time, setiap informasi dapat dipantau, diperbarui, dan dianalisis secara efisien untuk mendukung pengambilan keputusan yang lebih akurat.</p>
                    </div>
                </div>

                <div class="security-card">
                    <div class="icon-shield-small">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><path d="M9 12l2 2 4-4"></path></svg>
                    </div>
                    <div class="sec-text">
                        <h5>Status Keamanan Data</h5>
                        <p>Seluruh data yang diinput terenkripsi end-to-end sesuai dengan standar kepatuhan medis.</p>
                    </div>
                </div>

            </div>

        </div>

    </div>
    <div class="modal-overlay" id="successModal">
        <div class="modal-box">
            <div class="modal-icon">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
            </div>
            <h3 class="modal-title">Data Berhasil Disimpan!</h3>
            <p class="modal-desc">Data UMKM Anda telah masuk ke dalam sistem dan sedang dalam proses verifikasi.</p>
            <button class="btn-modal-ok" id="btnOkModal">Kembali ke Beranda</button>
        </div>
    </div>

    <!-- Modal Limit UMKM -->
    <div class="modal-overlay" id="limitModal">
        <div class="modal-box">
            <div class="modal-icon" style="background-color: #fee2e2; color: #dc3545;">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
            </div>
            <h3 class="modal-title">Batas Tercapai</h3>
            <p class="modal-desc">Anda sudah memiliki UMKM terdaftar. Saat ini sistem hanya mengizinkan satu UMKM per akun pemilik.</p>
            <button class="btn-modal-ok" id="btnLimitOk" style="background-color: #dc3545;">Lihat Manajemen UMKM</button>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Ambil elemen form dan modal menggunakan ID yang spesifik
            const form = document.getElementById('umkmForm');
            const modal = document.getElementById('successModal');
            const btnOk = document.getElementById('btnOkModal');
            const limitModal = document.getElementById('limitModal');
            const btnLimitOk = document.getElementById('btnLimitOk');

            // Logika saat form disubmit (klik tombol Simpan Data)
            form.addEventListener('submit', function(e) {
                e.preventDefault(); 
                
                const formData = new FormData(form);
                const data = {};
                formData.forEach((value, key) => data[key] = value);

                fetch("{{ route('dashboard.simpan-umkm') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        modal.classList.add('active');
                    } else {
                        // Jika pesan error menunjukkan batas tercapai, tampilkan limitModal
                        if (result.message && result.message.includes('sudah memiliki satu UMKM')) {
                            limitModal.classList.add('active');
                        } else {
                            alert('Gagal menyimpan data: ' + (result.message || 'Terjadi kesalahan'));
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menghubungi server.');
                });
            });

            // Logika saat tombol "Kembali ke Beranda" di pop-up diklik
            btnOk.addEventListener('click', function() {
                // Sembunyikan pop-up (opsional)
                modal.classList.remove('active');
                
                // Arahkan user kembali ke halaman dashboard
                window.location.href = "{{ route('dashboard') }}"; 
            });

            // Logika Limit UMKM
            btnLimitOk.addEventListener('click', function() {
                window.location.href = "{{ route('dashboard.manajemen-umkm') }}";
            });
        });
    </script>
    <script src="{{ asset('js/responsive-sidebar.js') }}"></script>
</body>
</html>