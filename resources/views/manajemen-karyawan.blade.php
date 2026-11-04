<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SiSehat - Daftar Karyawan</title>
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
        .avatar-sidebar { width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; overflow: hidden; }
        .avatar-sidebar img { width: 100%; height: 100%; object-fit: cover; }
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
        
        .page-header-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .page-header h1 { font-size: 26px; color: #1e293b; font-weight: 800; margin-bottom: 8px; }
        .page-header p { color: #64748b; font-size: 14px; line-height: 1.5; }
        
        .btn-add-karyawan {
            background-color: #2563eb; color: white; border: none; padding: 12px 20px; 
            border-radius: 8px; font-weight: 600; font-size: 14px; cursor: pointer; transition: 0.2s;
            box-shadow: 0 2px 4px rgba(37, 99, 235, 0.2); display: flex; align-items: center; gap: 8px; text-decoration: none;
        }
        .btn-add-karyawan:hover { background-color: #1d4ed8; }

        /* ================= TABLE CARD ================= */
        .table-card {
            background-color: #ffffff;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            border: 1px solid #f1f5f9;
        }

        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .data-table th { 
            background-color: #f8fafc; color: #475569; font-size: 12px; font-weight: 700; 
            text-align: left; padding: 15px; border-radius: 4px;
        }
        .data-table td { padding: 15px; border-bottom: 1px solid #e2e8f0; font-size: 14px; color: #1e293b; font-weight: 500; vertical-align: middle; }
        .data-table tr:last-child td { border-bottom: none; }

        .karyawan-name-cell { display: flex; align-items: center; gap: 12px; }
        .karyawan-avatar { 
            width: 32px; height: 32px; border-radius: 50%; display: flex; justify-content: center; align-items: center; 
            color: #1e293b; font-size: 11px; font-weight: 700; flex-shrink: 0;
        }
        
        .avatar-purple { background-color: #e0e7ff; color: #4338ca; }
        .avatar-blue { background-color: #dbeafe; color: #1d4ed8; }
        .avatar-green { background-color: #dcfce7; color: #15803d; }
        .avatar-lightblue { background-color: #e0f2fe; color: #0369a1; }
        .avatar-lightpurple { background-color: #ede9fe; color: #6d28d9; }

        .table-footer { font-size: 12px; color: #94a3b8; }

    </style>
    <link href="{{ asset('css/kreva-theme.css') }}" rel="stylesheet">
</head>
<body class="kreva-analytics">

    <div class="sidebar">
        <div class="logo-box">
            <img src="{{ asset('images/kreva_logo.svg') }}" alt="KREVA" class="logo-main" onerror="this.style.display='none'; this.insertAdjacentHTML('afterend', '<h2 style=\'color:#0d47a1; font-weight:900;\'>KREVA</h2>');">
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
                <img src="{{ asset('images/Tambah_Data_UMKM_logo.svg') }}" class="nav-icon" onerror="this.src='https://cdn-icons-png.flaticon.com/512/2990/2990315.png'">
                <span>Tambah Data UMKM</span>
            </li>
            @endif
             <li class="nav-item" onclick="window.location='{{ route('dashboard.tambah-karyawan') }}'">
                <img src="{{ asset('images/Tambah_Data_Karyawan_logo.svg') }}" class="nav-icon" onerror="this.src='https://cdn-icons-png.flaticon.com/512/3135/3135715.png'">
                <span>Tambah Data Karyawan</span>
            </li>
            @if(auth()->user()->role !== 'employee')
            <li class="nav-item active" onclick="window.location='{{ route('dashboard.manajemen-umkm') }}'">
                <img src="{{ asset('images/Manajemen_UMKM_logo.svg') }}" class="nav-icon" onerror="this.src='https://cdn-icons-png.flaticon.com/512/104/104646.png'">
                <span>Manajemen UMKM</span>
            </li>
            @endif
        </ul>

        <div class="profile-card">
            <div class="avatar-sidebar">
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
        <div class="page-header-flex">
            <div class="page-header" style="margin-bottom: 0;">
                <h1>Daftar Karyawan: {{ $umkm->nama_umkm }}</h1>
                <p>Manajemen profil dan akses karyawan untuk unit usaha ini.</p>
            </div>
            <a href="{{ route('dashboard.tambah-karyawan') }}" class="btn-add-karyawan">
                + Tambah Karyawan Baru
            </a>
        </div>

        <div class="table-card">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 35%">NAMA LENGKAP</th>
                        <th style="width: 25%; text-align: center;">PASSWORD</th>
                        <th style="width: 25%; text-align: center;">JENIS KELAMIN</th>
                        <th style="width: 15%; text-align: center;">KATEGORI USIA</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($karyawan as $k)
                    <tr>
                        <td>
                            <div class="karyawan-name-cell">
                                <div class="karyawan-avatar {{ ['avatar-purple', 'avatar-blue', 'avatar-green', 'avatar-lightblue', 'avatar-lightpurple'][($loop->index % 5)] }}">
                                    {{ strtoupper(substr($k->nama_user, 0, 2)) }}
                                </div>
                                {{ $k->nama_user }}
                            </div>
                        </td>
                        <td style="text-align: center; color: #94a3b8; font-size: 12px;">******** (Encrypted)</td>
                        <td style="text-align: center; color: #475569; font-weight: 500;">
                            {{ $k->gender == 2 ? 'Laki-laki' : ($k->gender == 1 ? 'Perempuan' : '-') }}
                        </td>
                        <td style="text-align: center; color: #475569; font-weight: 500;">
                            @if($k->age == 1) < 30 Tahun
                            @elseif($k->age == 2) 30 - 40 Tahun
                            @elseif($k->age == 3) > 40 Tahun
                            @else - @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 40px; color: #94a3b8;">
                            Belum ada karyawan terdaftar di UMKM ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="table-footer">
                Menampilkan {{ $karyawan->count() }} karyawan
            </div>
        </div>

    </div>
    <script src="{{ asset('js/responsive-sidebar.js') }}"></script>
</body>
</html>
