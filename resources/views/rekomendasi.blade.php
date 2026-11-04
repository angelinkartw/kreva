<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard SiSehat - Rekomendasi</title>
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
        .page-header h1 { font-size: 26px; color: #1e293b; font-weight: 800; margin-bottom: 8px; }
        .page-header p { color: #64748b; font-size: 14px; line-height: 1.5; }

        /* HERO SECTION (Analisis Keseluruhan) */
        /* HERO SECTION (Analisis Keseluruhan) */
        .hero-section {
            background-color: #eef2ff;
            border-radius: 12px;
            padding: 30px;
            display: flex;
            align-items: center;
            gap: 30px;
            margin-bottom: 40px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(0,0,0,0.25);
        }
        
        .circle-wrap { width: 140px; height: 140px; border-radius: 50%; background: conic-gradient(#0d47a1 63%, #d1d5db 0deg); display: flex; justify-content: center; align-items: center; flex-shrink: 0; }
        .circle-inner { width: 110px; height: 110px; border-radius: 50%; background-color: #ffffff; display: flex; flex-direction: column; justify-content: center; align-items: center; box-shadow: inset 0 2px 5px rgba(0,0,0,0.05); }
        .circle-inner h2 { font-size: 32px; font-weight: 800; color: #0f172a; line-height: 1; margin-bottom: 4px; }
        .circle-inner span { font-size: 10px; color: #64748b; font-weight: 600; text-transform: uppercase; }

        .hero-content { flex-grow: 1; z-index: 2; }
        .badge-status { display: inline-flex; align-items: center; gap: 6px; background-color: #ffffff; padding: 4px 10px; border-radius: 20px; font-size: 10px; font-weight: 700; color: #0d47a1; margin-bottom: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .badge-status::before { content: ""; width: 6px; height: 6px; border-radius: 50%; background-color: #0d47a1; }
        
        .hero-content h2 { font-size: 22px; font-weight: 800; color: #0f172a; margin-bottom: 10px; }
        .hero-content p { font-size: 13px; color: #475569; line-height: 1.6; margin-bottom: 20px; max-width: 600px; }
        
        .hero-buttons { display: flex; gap: 12px; }
        .btn-primary { background-color: #0d47a1; color: white; border: none; padding: 10px 20px; border-radius: 6px; font-weight: 600; font-size: 13px; cursor: pointer; transition: 0.2s; }
        .btn-primary:hover { background-color: #08367a; }
        .btn-secondary { background-color: #dbeafe; color: #0d47a1; border: none; padding: 10px 20px; border-radius: 6px; font-weight: 600; font-size: 13px; cursor: pointer; transition: 0.2s; }
        .btn-secondary:hover { background-color: #bfdbfe; }

        .hero-bg-graphic { position: absolute; right: 0; bottom: 0; height: 100%; width: 250px; opacity: 0.1; background-image: repeating-linear-gradient(0deg, transparent, transparent 19px, #000 19px, #000 20px); mask-image: linear-gradient(to right, transparent, black); -webkit-mask-image: linear-gradient(to right, transparent, black); z-index: 1; }

        /* PRIORITAS TINDAKAN SECTION */
        .section-title { font-size: 18px; font-weight: 800; color: #0f172a; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
        .section-title::before { content: ""; display: block; width: 4px; height: 20px; background-color: #0d47a1; border-radius: 4px; }

        .action-grid { 
            display: flex; 
            gap: 25px; 
            margin-bottom: 40px; 
            align-items: stretch; /* Container sejajar sempurna */
        }

        .perf-box { 
            flex: 1; 
            background-color: #f8fafc; 
            border-radius: 12px; 
            padding: 25px; 
            border: 1px solid #e2e8f0; 
            display: flex;
            flex-direction: column;
        }
        .perf-box.success { background-color: #EEF4FF; border-color: #d1e3ff; }

        .perf-box-title { 
            font-size: 14px; font-weight: 700; color: #0f172a; margin-bottom: 20px; 
            display: flex; align-items: center; gap: 8px; flex-shrink: 0; 
        }
        
        /* CARD ITEM */
        .item-card { 
            background: #ffffff; border-radius: 8px; padding: 16px 16px 16px 24px; margin-bottom: 15px; 
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            position: relative; overflow: hidden;
            flex: 1; 
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .item-card::before {
            content: "";
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 8px;
        }
        .item-card:last-child { margin-bottom: 0; }
        
        /* Rank Colors */
        .rank-1::before { background-color: #FFD700; } /* Gold */
        .rank-2::before { background-color: #C0C0C0; } /* Silver */
        .rank-3::before { background-color: #CD7F32; } /* Bronze */
        .rank-danger::before { background-color: #ef4444; } /* Red for bottom performers */        
        .item-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px; }
        .item-tag { font-size: 9px; font-weight: 800; text-transform: uppercase; padding: 3px 6px; border-radius: 4px; margin-bottom: 6px; display: inline-block; }
        .item-tag.tag-gold { background-color: #fef3c7; color: #b45309; }
        .item-tag.tag-gray { background-color: #f1f5f9; color: #64748b; }
        
        .item-title h4 { font-size: 14px; font-weight: 800; color: #0f172a; }
        .item-score { text-align: right; }
        .item-score h3 { font-size: 16px; font-weight: 800; color: #0f172a; }
        .item-score span { font-size: 9px; color: #94a3b8; font-weight: 600; }
        .item-desc { font-size: 12px; color: #475569; line-height: 1.5; }

        .perf-box.danger { background-color: #fef2f2; border-color: #fee2e2; }
        .perf-box-title.danger { color: #dc2626; }
        .item-tag.tag-red { background-color: #fee2e2; color: #dc2626; }
        .item-score.danger h3 { color: #dc2626; }
        .item-score.danger span { color: #f87171; }

        /* FOOTER BANNER */
        .footer-banner { background-color: #D4E4FA; border-radius: 12px; padding: 15px 30px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .fb-content { display: flex; align-items: center; gap: 20px; }
        .fb-image-icon { width: 65px; height: auto; object-fit: contain; }
        .fb-text h4 { font-size: 16px; font-weight: 800; color: #0f172a; margin-bottom: 4px; }
        .fb-text p { font-size: 13px; color: #475569; }

        .btn-image-custom { background: none; border: none; cursor: pointer; padding: 0; display: inline-flex; transition: transform 0.2s; }
        .btn-image-custom:hover { transform: scale(1.02); }
        .btn-image-custom img { height: 48px; width: auto; object-fit: contain; }

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
            <li class="nav-item active" onclick="window.location='{{ route('dashboard.rekomendasi') }}'">
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
            <h1>Rekomendasi Peningkatan Kesehatan Organisasi</h1>
            <p>Berdasarkan hasil analisis, berikut adalah prioritas tindakan untuk meningkatkan vitalitas dan performa UMKM Anda.</p>
        </div>

        <div class="hero-section">
            <div class="circle-wrap" style="background: conic-gradient(#0d47a1 {{ $data['skor_kesehatan'] }}%, #d1d5db 0deg);">
                <div class="circle-inner">
                    <h2>{{ $data['skor_kesehatan'] }}</h2>
                    <span>DARI 100</span>
                </div>
            </div>
            
            <div class="hero-content">
                <div class="badge-status">{{ $data['status'] }}</div>
                <h2>Analisis Kesehatan Keseluruhan</h2>
                <p>Berdasarkan skor rata-rata {{ $data['skor_kesehatan'] }}%, organisasi Anda saat ini berada dalam {{ $data['status'] }}. Fokuslah pada perbaikan faktor-faktor dengan skor terendah untuk meningkatkan efisiensi operasional secara keseluruhan.</p>
            </div>
            
            <div class="hero-bg-graphic"></div>
        </div>

        <h3 class="section-title">Prioritas Tindakan</h3>

        <div class="action-grid">
            
            <div class="perf-box success">
                <div class="perf-box-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px;"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"></path><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"></path><path d="M4 22h16"></path><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"></path><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"></path><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"></path></svg>
                    Top Performers (Best Practice)
                </div>
                
                @foreach(array_slice($data['sorted_factors'], 0, 3) as $index => $factor)
                <div class="item-card rank-{{ $index + 1 }}">
                    <div class="item-header">
                        <div>
                            <span class="item-tag tag-{{ $index == 0 ? 'gold' : 'gray' }}">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 4px;"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"></path><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"></path><path d="M4 22h16"></path><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"></path><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"></path><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"></path></svg>
                                {{ $index == 0 ? 'BEST PRACTICE' : 'HIGH PERFORMANCE' }}
                            </span>
                            <div class="item-title"><h4>{{ $factor['name'] }}</h4></div>
                        </div>
                        <div class="item-score">
                            <h3>{{ number_format($factor['score'], 2) }}</h3>
                            <span>SKOR</span>
                        </div>
                    </div>
                    <p class="item-desc">{{ $factor['desc'] }}</p>
                </div>
                @endforeach
            </div>

            <div class="perf-box danger">
                <div class="perf-box-title danger">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px;"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path><path d="M12 9v4"></path><path d="M12 17h.01"></path></svg>
                    Bottom Performers (Critical Priority)
                </div>

                @foreach(array_slice(array_reverse($data['sorted_factors']), 0, 3) as $index => $factor)
                <div class="item-card rank-danger">
                    <div class="item-header">
                        <div>
                            <span class="item-tag tag-red">
                                CRITICAL PRIORITY
                            </span>
                            <div class="item-title"><h4>{{ $factor['name'] }}</h4></div>
                        </div>
                        <div class="item-score danger">
                            <h3>{{ number_format($factor['score'], 2) }}</h3>
                            <span>SKOR</span>
                        </div>
                    </div>
                    <p class="item-desc">{{ $factor['desc'] }}</p>
                </div>
                @endforeach
            </div>

        </div>

        
        <div style="height: 40px;"></div>
    </div>

    <script src="{{ asset('js/responsive-sidebar.js') }}"></script>
</body>
</html>