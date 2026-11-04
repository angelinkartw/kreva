<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SiSehat - Manajemen UMKM</title>
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
        .avatar { width: 45px; height: 45px; border-radius: 50%;  display: flex; align-items: center; justify-content: center; font-weight: bold; overflow: hidden; }
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
        
        .page-header { margin-bottom: 30px; }
        .page-header h1 { font-size: 26px; color: #1e293b; font-weight: 800; margin-bottom: 8px; }
        .page-header p { color: #64748b; font-size: 14px; line-height: 1.5; }

        /* ================= TABLE CARD ================= */
        .table-card {
            background-color: #ffffff;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            border: 1px solid #f1f5f9;
        }

        .table-header-box { margin-bottom: 25px; border-bottom: 2px solid #3b82f6; padding-bottom: 10px; }
        .table-title { display: flex; align-items: center; gap: 10px; font-size: 16px; font-weight: 700; color: #1e293b; margin-bottom: 5px; }
        .table-subtitle { font-size: 12px; color: #64748b; }

        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th { 
            background-color: #f8fafc; color: #475569; font-size: 12px; font-weight: 700; 
            text-align: left; padding: 15px; border-radius: 4px;
        }
        .data-table td { padding: 15px; border-bottom: 1px solid #e2e8f0; font-size: 14px; color: #1e293b; font-weight: 500; vertical-align: middle; }
        .data-table tr:last-child td { border-bottom: none; }

        .umkm-name-cell { display: flex; align-items: center; gap: 12px; }
        .umkm-icon { width: 32px; height: 32px; background: linear-gradient(135deg, #3b82f6 50%, #facc15 50%); border-radius: 6px; display: flex; justify-content: center; align-items: center; color: white; font-size: 16px; font-weight: bold; }
        
        .action-link { color: #2563eb; font-weight: 600; text-decoration: none; font-size: 13px; display: flex; align-items: center; gap: 5px; }
        .action-link:hover { text-decoration: underline; }

        .expand-btn { display: flex; justify-content: center; padding-top: 15px; margin-top: 10px; cursor: pointer; color: #94a3b8; }
        .expand-btn:hover { color: #475569; }

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
        <div class="page-header">
            <h1>Manajemen Data UMKM & Karyawan</h1>
            <p>Overview komprehensif data unit usaha dan sumber daya manusia terdaftar dalam sistem Klinik</p>
        </div>

        <div class="table-card">
            <div class="table-header-box">
                <div class="table-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="3" y1="9" x2="21" y2="9"></line>
                        <line x1="9" y1="21" x2="9" y2="9"></line>
                    </svg>
                    Daftar UMKM Terdaftar
                </div>
                <div class="table-subtitle">Data operasional entitas bisnis</div>
            </div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 30%">NAMA UMKM</th>
                        <th style="width: 30%; text-align: center;">INDUSTRI</th>
                        <th style="width: 25%; text-align: center;">USIA UMKM (TAHUN)</th>
                        <th style="width: 15%; text-align: center;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($umkm as $item)
                    <tr>
                        <td>
                            <div class="umkm-name-cell">
                                <div class="umkm-icon">
                                    <div style="display: flex; flex-direction: column; width: 100%; height: 100%;">
                                        <div style="background-color: #3b82f6; height: 50%; border-top-left-radius: 6px; border-top-right-radius: 6px;"></div>
                                        <div style="background-color: #facc15; height: 50%; border-bottom-left-radius: 6px; border-bottom-right-radius: 6px;"></div>
                                    </div>
                                </div>
                                {{ $item->nama_umkm }}
                            </div>
                        </td>
                        <td style="text-align: center; color: #475569; font-weight: 500;">
                            @if($item->industry == 1) Makanan dan Minuman (F&B)
                            @elseif($item->industry == 2) Fashion
                            @elseif($item->industry == 3) Wholesale & Retail
                            @else {{ $item->industry }} @endif
                        </td>
                        <td style="text-align: center;">
                            <div style="color: #2563eb; font-weight: 700; background-color: #f8fafc; border-radius: 8px; padding: 4px 12px; display: inline-flex; justify-content: center; align-items: center;">
                                @if($item->usia_usaha == 1) < 1 Tahun
                                @elseif($item->usia_usaha == 2) 1 - 3 Tahun
                                @elseif($item->usia_usaha == 3) > 3 Tahun
                                @else {{ $item->usia_usaha }} @endif
                            </div>
                        </td>
                        <td style="text-align: center;">
                            <a href="{{ route('dashboard.manajemen-karyawan', $item->id_umkm) }}" class="action-link" style="justify-content: center;">Lihat Karyawan &gt;</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 40px; color: #94a3b8;">
                            Belum ada data UMKM terdaftar.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

        </div>

    </div>
    <script src="{{ asset('js/responsive-sidebar.js') }}"></script>
</body>
</html>
