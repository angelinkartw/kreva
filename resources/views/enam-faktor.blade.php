<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard SiSehat - 6 Faktor Penilaian</title>
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
        
        .page-header { margin-bottom: 30px; }
        .page-header h1 { font-size: 24px; color: #1e293b; font-weight: 800; margin-bottom: 8px; }
        .page-header p { color: #64748b; font-size: 14px; line-height: 1.5; }

        .content-grid {
            display: grid;
            grid-template-columns: 320px 1fr;
            gap: 25px;
            align-items: start;
        }

        .left-column { display: flex; flex-direction: column; gap: 20px; }

        .factors-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        /* ================= KARTU & KOMPONEN ================= */
        .card { 
            background: #ffffff; 
            border-radius: 12px; 
            padding: 25px; 
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2); 
            border: none;
        }

        /* Status Kesehatan: Tanpa shadow, pakai stroke 1px */
        .status-card {
            box-shadow: none;
            border: 1px solid #cbd5e1;
        }

        .status-badge-container { display: flex; align-items: center; gap: 15px; margin-bottom: 20px; }
        .icon-circle { 
            width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        .icon-circle.warning { background-color: #f59e0b; color: white; }
        
        .status-text h5 { font-size: 12px; color: #64748b; font-weight: 600; margin-bottom: 2px; letter-spacing: 0.3px; }
        .status-text h3 { font-size: 18px; font-weight: 700; line-height: 1.2; }
        .status-text h3.warning { color: #f59e0b; }
        
        .insight-summary h6 { font-size: 14px; color: #0f172a; font-weight: 600; margin-bottom: 10px; }
        .insight-summary p { font-size: 14px; color: #475569; line-height: 1.6; margin-bottom: 0; font-weight: 400; }
        
        .btn-blue { background-color: #1d4ed8; color: white; border: none; border-radius: 6px; padding: 12px; width: 100%; font-size: 13px; font-weight: 600; cursor: pointer; transition: 0.2s; }
        .btn-blue:hover { background-color: #1e40af; }

        /* Rekomendasi Tindakan: Latar Biru Muda #f0f7ff */
        .recommendation-list {
            background-color: #EEF4FF;
            border: none;
            padding: 30px !important;
            box-shadow: none;
        }
        .recommendation-list h4 { font-size: 16px; font-weight: 700; color: #0f172a; margin-bottom: 25px; display: flex; align-items: center; gap: 10px; }
        .bulb-icon { 
            width: 15px; height: 20px; display: flex; align-items: center; justify-content: center; color: #064e3b;
        }
        .recommendation-list ul { list-style: none; display: flex; flex-direction: column; gap: 15px; }
        .recommendation-list li { font-size: 14px; color: #4b5563; display: flex; align-items: flex-start; gap: 12px; line-height: 1.5; font-weight: 400; }
        .recommendation-list li .check-icon { 
            color: #2563eb; font-weight: bold; background: transparent;
            width: 20px; height: 20px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 12px; 
            border: 2px solid #2563eb;
        }
        .recommendation-list li b { color: #111827; font-weight: 700; }

        /* Faktor Card Layout */
        .card-factor { display: flex; flex-direction: column; justify-content: space-between; position: relative; }
        .cf-header { 
            display: flex; 
            align-items: center; 
            gap: 12px; 
            margin-bottom: 15px; 
            padding-right: 75px; /* Ruang agar tidak tertimpa badge skor */
        }
        .cf-header .icon-square { width: 38px; height: 38px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; }
        .cf-header .icon-square.warning { background-color: #fef3c7; color: #d97706; }
        .cf-header .icon-square.danger { background-color: #fee2e2; color: #dc2626; }
        .cf-header .icon-square.success { background-color: #d1fae5; color: #059669; }
        .cf-header h3 { font-size: 15px; font-weight: 800; color: #0f172a; line-height: 1.4; }
        
        .score-badge { 
            position: absolute; top: 25px; right: 25px; 
            font-size: 12px; font-weight: 800; padding: 4px 10px; border-radius: 20px; 
            display: flex; align-items: center; gap: 4px; 
        }
        .score-badge.warning { background-color: #fef3c7; color: #d97706; }
        .score-badge.danger { background-color: #fee2e2; color: #dc2626; }
        .score-badge.success { background-color: #d1fae5; color: #059669; }

        .cf-desc { font-size: 12px; color: #64748b; line-height: 1.6; margin-bottom: 25px; min-height: 60px; }
        .cf-desc.danger-text { color: #dc2626; }

        .cf-progress-area { margin-bottom: 20px; }
        .cf-labels { display: flex; justify-content: space-between; font-size: 11px; margin-bottom: 6px; }
        .cf-labels span:first-child { color: #64748b; }
        .cf-labels span:last-child { font-weight: 700; color: #0f172a; }
        .progress-track { width: 100%; height: 6px; background-color: #e2e8f0; border-radius: 4px; overflow: hidden; }
        .progress-fill { height: 100%; border-radius: 4px; }
        .progress-fill.warning { background-color: #f59e0b; }
        .progress-fill.danger { background-color: #ef4444; }
        .progress-fill.success { background-color: #10b981; }

        .btn-outline { background: transparent; border: 1px solid #e2e8f0; color: #0f172a; border-radius: 6px; padding: 10px; width: 100%; font-size: 12px; font-weight: 600; cursor: pointer; transition: 0.2s; }
        .btn-outline:hover { background: #f8fafc; border-color: #cbd5e1; }

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
            <li class="nav-item active" onclick="window.location='{{ route('dashboard.faktor') }}'">
                <img src="{{ asset('images/6_Faktor_Penilaian_logo.svg') }}" class="nav-icon" onerror="this.src='https://cdn-icons-png.flaticon.com/512/138/138439.png'">
                <span>6 Faktor Penilaian</span>
            </li>
            <li class="nav-item" onclick="window.location='{{ route('dashboard.rekomendasi') }}'">
                <img src="{{ asset('images/Rekomendasi_logo.svg') }}" class="nav-icon" onerror="this.src='https://cdn-icons-png.flaticon.com/512/151/151300.png'">
                <span>Rekomendasi</span>
            </li>
            <li class="nav-item" onclick="window.location='{{ route('dashboard.tambah-umkm') }}'">
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
        <div class="page-header">
            <h1>6 Faktor Kesehatan Organisasi UMKM</h1>
            <p>Analisis mendalam terhadap enam pilar utama yang menentukan vitalitas dan keberlanjutan bisnis Anda.</p>
        </div>

        <div class="content-grid">
            
            <div class="left-column">
                <div class="card status-card">
                    <div class="status-badge-container">
                        <div class="icon-circle {{ $data['skor_kesehatan'] >= 75 ? 'success' : ($data['skor_kesehatan'] >= 50 ? 'warning' : 'danger') }}">
                            @if($data['skor_kesehatan'] >= 75)
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            @elseif($data['skor_kesehatan'] >= 50)
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                            @else
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                            @endif
                        </div>
                        <div class="status-text">
                            <h5>STATUS KESEHATAN</h5>
                            <h3 class="{{ $data['skor_kesehatan'] >= 75 ? 'success' : ($data['skor_kesehatan'] >= 50 ? 'warning' : 'danger') }}">
                                {{ $data['skor_kesehatan'] >= 75 ? 'Kondisi Baik' : ($data['skor_kesehatan'] >= 50 ? 'Perlu Peningkatan' : 'Kondisi Kritis') }}
                            </h3>
                        </div>
                    </div>
                    <div class="insight-summary">
                        <h6>Insight Summary</h6>
                        <p>Skor rata-rata kesehatan organisasi Anda adalah <b>{{ $data['skor_kesehatan'] }}%</b>. 
                        @if($data['skor_kesehatan'] < 50)
                            Banyak area yang membutuhkan perbaikan mendesak untuk menjaga keberlangsungan usaha.
                        @elseif($data['skor_kesehatan'] < 75)
                            Beberapa faktor sudah cukup baik, namun masih ada ruang untuk optimasi lebih lanjut.
                        @else
                            Pertahankan kinerja positif ini dan fokus pada inovasi berkelanjutan.
                        @endif
                        </p>
                    </div>
                </div>

                <div class="card recommendation-list">
                    <h4>
                        <div class="bulb-icon">
                            <svg width="15" height="20" viewBox="0 0 15 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7.5 1C4.46 1 2 3.46 2 6.5C2 8.38 2.94 10.04 4.38 11.05C5.07 11.53 5.5 12.31 5.5 13.15V14.5C5.5 15.05 5.95 15.5 6.5 15.5H8.5C9.05 15.5 9.5 15.05 9.5 14.5V13.15C9.5 12.31 9.93 11.53 10.62 11.05C12.06 10.04 13 8.38 13 6.5C13 3.46 10.54 1 7.5 1Z" stroke="#064e3b" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M5.5 18.5H9.5" stroke="#064e3b" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M6.5 21.5H8.5" stroke="#064e3b" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        Rekomendasi Tindakan
                    </h4>
                    <ul>
                        @foreach($data['factors'] as $f)
                            @if($f['score'] < 3)
                            <li>
                                <div class="check-icon" style="color: #ef4444; border-color: #ef4444;">!</div>
                                <span>Tingkatkan <b>{{ $f['title'] }}</b> yang saat ini masih rendah ({{ $f['score'] }}/5.0).</span>
                            </li>
                            @endif
                        @endforeach
                        <li>
                            <div class="check-icon">✓</div>
                            <span>Lakukan evaluasi rutin setiap bulan.</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="factors-grid">
                @foreach($data['factors'] as $f)
                <div class="card card-factor">
                    <div class="cf-header">
                        <div class="icon-square {{ $f['score'] >= 3.75 ? 'success' : ($f['score'] >= 2.5 ? 'warning' : 'danger') }}">
                            {!! $f['icon'] !!}
                        </div>
                        <h3>Faktor {{ $loop->iteration }}: {{ $f['title'] }}</h3>
                    </div>
                    <div class="score-badge {{ $f['score'] >= 3.75 ? 'success' : ($f['score'] >= 2.5 ? 'warning' : 'danger') }}">
                        {{ $f['score'] >= 3.75 ? '▲' : ($f['score'] >= 2.5 ? '▬' : '▼') }} {{ $f['score'] }}
                    </div>
                    <p class="cf-desc {{ $f['score'] < 2.5 ? 'danger-text' : '' }}">{{ $f['desc'] }}</p>
                    <div class="cf-progress-area">
                        <div class="cf-labels"><span>Skor {{ $f['title'] }}</span><span>{{ $f['score'] }} / 5.0</span></div>
                        <div class="progress-track">
                            <div class="progress-fill {{ $f['score'] >= 3.75 ? 'success' : ($f['score'] >= 2.5 ? 'warning' : 'danger') }}" 
                                 style="width: {{ $f['percentage'] }}%;"></div>
                        </div>
                    </div>
                    <button class="btn-outline" onclick="window.location='{{ route('dashboard.faktor-detail', $f['id']) }}'">Lihat Detail →</button>
                </div>
                @endforeach
            </div>

            </div>
        </div>
        <div style="height: 40px;"></div>
    </div>

    <script src="{{ asset('js/responsive-sidebar.js') }}"></script>
</body>
</html>