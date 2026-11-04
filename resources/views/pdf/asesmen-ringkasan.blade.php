<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Ringkasan Asesmen</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1e293b; }
        h1 { font-size: 16px; margin: 0 0 4px 0; color: #0f172a; }
        .meta { font-size: 9px; color: #64748b; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th { background: #e2e8f0; color: #0f172a; text-align: left; padding: 6px 8px; border: 1px solid #cbd5e1; font-size: 9px; }
        td { padding: 6px 8px; border: 1px solid #e2e8f0; vertical-align: top; }
        td.q { width: 62%; }
        td.a { width: 38%; }
        tr:nth-child(even) td { background: #f8fafc; }
        .footer { margin-top: 14px; font-size: 8px; color: #94a3b8; }
    </style>
</head>
<body>
    <h1>Ringkasan Jawaban Asesmen Organisasi</h1>
    <div class="meta">
        Nama: <strong>{{ $nama }}</strong> &nbsp;|&nbsp; Peran: <strong>{{ $peran }}</strong><br>
        Tanggal cetak: {{ $tanggalCetak }} &nbsp;|&nbsp; Status asesmen: <strong>{{ $statusAsesmen }}</strong>
    </div>

    <table>
        <thead>
            <tr>
                <th class="q">Pertanyaan</th>
                <th class="a">Jawaban</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $r)
            <tr>
                <td class="q">{{ $r['id'] }} — {{ $r['pertanyaan'] }}</td>
                <td class="a">{{ $r['jawaban'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <p class="footer">Dokumen ini dihasilkan otomatis oleh SiSehat. Jawaban mencerminkan data terakhir yang tersimpan pada asesmen UMKM terkait.</p>
</body>
</html>
