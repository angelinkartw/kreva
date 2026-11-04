<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SiSehat - Buat Akun Karyawan</title>
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
        .content-area { flex-grow: 1; padding: 40px; overflow-y: auto; }
        .step-badge {
            display: inline-flex; align-items: center; gap: 6px; background-color: #eef2ff; 
            padding: 6px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; color: #0d47a1; margin-bottom: 15px;
        }
        .step-badge::before { content: ""; width: 6px; height: 6px; border-radius: 50%; background-color: #0d47a1; }
        .page-header { margin-bottom: 30px; }
        .page-header h1 { font-size: 26px; color: #1e293b; font-weight: 800; margin-bottom: 8px; }
        .page-header p { color: #64748b; font-size: 14px; }

        /* ================= GRID LAYOUT ================= */
        .main-grid { display: grid; grid-template-columns: 1.2fr 1fr; gap: 25px; align-items: flex-start; }

        .form-card {
            background-color: #ffffff; border-radius: 12px; padding: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03); border: 1px solid #f1f5f9;
        }
        .form-group { margin-bottom: 20px; }
        .form-row { display: flex; gap: 20px; margin-bottom: 20px; }
        .form-row .form-group { flex: 1; margin-bottom: 0; }
        
        label, .label-title { display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 8px; }
        input[type="text"], input[type="password"], input[type="number"] {
            width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none;
        }
        input:focus { border-color: #0d47a1; }

        .field-error-msg {
            display: none;
            color: #dc2626;
            font-size: 12px;
            margin-top: 8px;
            line-height: 1.4;
        }
        .field-error-msg.visible { display: block; }
        input.input-invalid { border-color: #dc2626; }

        .radio-group { display: flex; gap: 20px; align-items: center; height: 45px; }
        .radio-option { display: flex; align-items: center; gap: 8px; font-size: 14px; color: #475569; cursor: pointer; }
        .radio-option input[type="radio"] { accent-color: #2563eb; }

        .btn-submit { 
            background-color: #2563eb; color: white; border: none; padding: 12px 30px; 
            border-radius: 8px; font-weight: 600; cursor: pointer; float: right; transition: 0.2s;
        }
        .btn-submit:hover { background-color: #1d4ed8; }

        /* --- INFO SECTION (KANAN) --- */
        .info-card-main {
            background: linear-gradient(rgba(0,0,0,0.2), rgba(0,0,0,0.8)), url('{{ asset("images/Akses_Tersentralisasi_2.jpg") }}');
            background-size: cover; background-position: center;
            height: 350px; border-radius: 20px; padding: 30px; color: white;
            display: flex; flex-direction: column; justify-content: flex-end;
            margin-bottom: 20px;
        }
        .icon-shield-large { width: 32px; height: 32px; background-color: rgba(255,255,255,0.2); border-radius: 50%; display: flex; justify-content: center; align-items: center; margin-bottom: 15px; }
        .info-content h3 { font-size: 18px; font-weight: 700; margin-bottom: 10px; }
        .info-content p { font-size: 12px; line-height: 1.6; color: #cbd5e1; }

        .security-card {
            background-color: #ffffff; border-radius: 12px; padding: 20px; border: 1px solid #f1f5f9;
            display: flex; gap: 15px; align-items: center;
        }
        .icon-shield-small { width: 36px; height: 36px; border-radius: 50%; background-color: #eef2ff; color: #10b981; display: flex; justify-content: center; align-items: center; flex-shrink: 0; }
        .sec-text h5 { font-size: 13px; font-weight: 700; margin-bottom: 4px; }
        .sec-text p { font-size: 11px; color: #64748b; }

        /* ================= POP-UP MODAL ================= */
        .modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.4); backdrop-filter: blur(4px);
            display: flex; justify-content: center; align-items: center;
            z-index: 9999; opacity: 0; visibility: hidden; transition: all 0.3s;
        }
        .modal-overlay.active { opacity: 1; visibility: visible; }
        .modal-box {
            background: #ffffff; padding: 40px 30px; border-radius: 16px;
            width: 90%; max-width: 380px; text-align: center;
            transform: translateY(-30px); transition: 0.3s;
        }
        .modal-overlay.active .modal-box { transform: translateY(0); }
        .modal-icon { width: 70px; height: 70px; background-color: #eef2ff; color: #2563eb; border-radius: 50%; display: flex; justify-content: center; align-items: center; margin: 0 auto 20px; }
        .btn-modal-ok { background-color: #2563eb; color: white; border: none; padding: 12px; width: 100%; border-radius: 8px; font-weight: 600; cursor: pointer; margin-top: 20px; }

        /* Password Show/Hide Styles */
        .password-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .password-wrapper input {
            padding-right: 45px;
        }

        .toggle-password {
            position: absolute;
            right: 12px;
            background: none;
            border: none;
            padding: 0;
            margin: 0;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #64748b;
            transition: color 0.2s;
            width: 24px;
            height: 24px;
            z-index: 10;
        }

        .toggle-password:hover {
            color: #2563eb;
            background: transparent !important;
        }
        
        .toggle-password svg {
            width: 20px;
            height: 20px;
            fill: none;
        }

        .hidden {
            display: none !important;
        }
    </style>
    <link href="{{ asset('css/kreva-theme.css') }}" rel="stylesheet">
</head>
<body class="kreva-analytics">

    <div class="sidebar">
        <div class="logo-box">
            <img src="{{ asset('images/kreva_logo.svg') }}" alt="KREVA" class="logo-main">
        </div>
        @if(auth()->user()->role !== 'employee')
        <button class="btn-primary-custom" onclick="window.location='{{ route('dashboard.tambah-umkm') }}'">+ Tambahkan UMKM Anda</button>
        @endif
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
            @if(auth()->user()->role !== 'employee')
            <li class="nav-item" onclick="window.location='{{ route('dashboard.tambah-umkm') }}'">
                <img src="{{ asset('images/Tambah_Data_UMKM_logo.svg') }}" class="nav-icon">
                <span>Tambah Data UMKM</span>
            </li>
            @endif
              <li class="nav-item active">
                <img src="{{ asset('images/Tambah_Data_Karyawan_logo.svg') }}" class="nav-icon">
                <span>Tambah Data Karyawan</span>
            </li>
            @if(auth()->user()->role !== 'employee')
            <li class="nav-item" onclick="window.location='{{ route('dashboard.manajemen-umkm') }}'">
                <img src="{{ asset('images/Manajemen_UMKM_logo.svg') }}" class="nav-icon" onerror="this.src='https://cdn-icons-png.flaticon.com/512/104/104646.png'">
                <span>Manajemen UMKM</span>
            </li>
            @endif
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
        <div class="step-badge">LANGKAH 2: DATA KARYAWAN</div>

        <div class="page-header">
            <h1>Buat Akun Karyawan</h1>
            <p>Akun ini memungkinkan karyawan untuk login dan mengisi asesmen kesehatan organisasi secara langsung.</p>
        </div>

        <div class="main-grid">
            <div class="form-card">
                <form id="employeeForm" novalidate>
                    <div class="form-group">
                        <label for="nama_user">Nama Lengkap</label>
                        <input type="text" id="nama_user" name="nama_user" placeholder="Masukkan nama lengkap sesuai identitas" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <span class="label-title">Jenis Kelamin</span>
                            <div class="radio-group">
                                <label class="radio-option"><input type="radio" name="gender" value="2" required> Laki-laki</label>
                                <label class="radio-option"><input type="radio" name="gender" value="1"> Perempuan</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="age">Usia</label>
                            <input type="number" id="age" name="age" placeholder="Contoh: 28" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="employeePassword">Password</label>
                        <div class="password-wrapper">
                            <input type="password" id="employeePassword" name="password" placeholder="Buat password" required minlength="8" autocomplete="new-password">
                            <button type="button" class="toggle-password" id="togglePasswordBtn">
                                <!-- Eye Icon -->
                                <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                                <!-- Eye-off Icon -->
                                <svg id="eyeOffIcon" class="hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.822 7.822L21 21m-2.278-2.278-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                </svg>
                            </button>
                        </div>
                        <p style="font-size: 11px; color: #94a3b8; margin-top: 8px;">Minimal 8 karakter, mencakup huruf dan angka.</p>
                        <span id="passwordError" class="field-error-msg" role="alert"></span>
                    </div>

                    <button type="submit" class="btn-submit">Buat Akun</button>
                </form>
            </div>

            <div class="info-section">
                <div class="info-card-main">
                    <div class="info-content">
                        <div class="icon-shield-large">
                             <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                        </div>
                        <h3>Akses Tersentralisasi</h3>
                        <p>Membangun ekosistem data yang terstruktur. Pastikan data profil karyawan diisi dengan akurat untuk keperluan pelaporan kesehatan klinis.</p>
                    </div>
                </div>

                <div class="security-card">
                    <div class="icon-shield-small">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                            <path d="M9 12l2 2 4-4"></path>
                        </svg>
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
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
            </div>
            <h3 style="font-size: 20px; font-weight: 800; color: #1e293b; margin-bottom: 12px;">Akun Berhasil Dibuat!</h3>
            <p style="font-size: 14px; color: #64748b; line-height: 1.6;">Data karyawan telah tersimpan. Karyawan kini dapat login menggunakan password yang telah didaftarkan.</p>
            <button class="btn-modal-ok" id="btnOkModal">Kembali ke Beranda</button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('employeeForm');
            const modal = document.getElementById('successModal');
            const btnOk = document.getElementById('btnOkModal');
            const passwordInput = document.getElementById('employeePassword');
            const passwordError = document.getElementById('passwordError');
            const togglePasswordBtn = document.getElementById('togglePasswordBtn');
            const eyeIcon = document.getElementById('eyeIcon');
            const eyeOffIcon = document.getElementById('eyeOffIcon');

            togglePasswordBtn.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                eyeIcon.classList.toggle('hidden');
                eyeOffIcon.classList.toggle('hidden');
            });

            function clearPasswordError() {
                passwordError.textContent = '';
                passwordError.classList.remove('visible');
                passwordInput.classList.remove('input-invalid');
            }

            function showPasswordError(message) {
                passwordError.textContent = message;
                passwordError.classList.add('visible');
                passwordInput.classList.add('input-invalid');
            }

            passwordInput.addEventListener('input', function() {
                if (passwordInput.value.length >= 8) {
                    clearPasswordError();
                }
            });

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                clearPasswordError();

                const pwd = passwordInput.value;
                if (pwd.length < 8) {
                    showPasswordError('Kata sandi minimal harus 8 karakter.');
                    passwordInput.focus();
                    return;
                }

                const formData = new FormData(form);
                const data = {};
                formData.forEach((value, key) => data[key] = value);

                fetch("{{ route('dashboard.simpan-karyawan') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(data)
                })
                .then(async function(response) {
                    let result = {};
                    try {
                        result = await response.json();
                    } catch (err) {
                        throw new Error('Respons tidak valid');
                    }

                    if (!response.ok) {
                        const pwdMsg = result.errors && result.errors.password && result.errors.password[0];
                        showPasswordError(pwdMsg || result.message || 'Validasi gagal. Periksa kembali data Anda.');
                        return;
                    }

                    if (result.success) {
                        modal.classList.add('active');
                    } else {
                        alert('Gagal membuat akun: ' + (result.message || 'Terjadi kesalahan'));
                    }
                })
                .catch(function(error) {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan sistem.');
                });
            });

            btnOk.addEventListener('click', function() {
                modal.classList.remove('active');
                window.location.href = "{{ route('dashboard') }}";
            });
        });
    </script>
    <script src="{{ asset('js/responsive-sidebar.js') }}"></script>
</body>
</html>