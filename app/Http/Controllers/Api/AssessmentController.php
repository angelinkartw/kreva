<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Factor;
use App\Models\Umkm;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;

class AssessmentController extends Controller
{
    /**
     * Teks pertanyaan tampilan bahasa Indonesia.
     */
    protected function questionTextTranslations(): array
    {
        return [
            'OH1' => 'Apakah usaha Anda memiliki Nomor Induk Berusaha (NIB)?',
            'OH2' => 'Apakah Anda pernah menerima bantuan pendanaan dari pemerintah?',
            'OH3' => 'Apakah Anda pernah mengikuti kegiatan pemerintah (pelatihan, workshop, pameran)?',
            'OH4' => 'Apakah Anda bekerja sama dengan pedagang atau usaha lain untuk mengembangkan bisnis?',
            'OH5' => 'Apakah Anda menggunakan pemasaran digital (media sosial, web, iklan) untuk promosi?',
            'OH6' => 'Apa sumber modal utama keuangan usaha Anda?',
            'OH10' => 'Apakah pemimpin Anda terlibat langsung dalam pekerjaan sehari-hari?',
            'OH11' => 'Apakah pemimpin Anda aktif berpartisipasi dalam melatih karyawan baru?',
            'OH12' => 'Apakah pemimpin memperlakukan karyawan dengan adil saat memberi sanksi atau penghargaan?',
            'OH7' => 'Apakah pemimpin mengenal karyawan secara personal?',
            'OH8' => 'Apakah pemimpin menjadi panutan bagi karyawan?',
            'OH9' => 'Apakah pemimpin terbuka dalam menerapkan ide atau saran dari karyawan?',
            'OH13' => 'Apakah tempat kerja bebas dari kecelakaan kerja?',
            'OH14' => 'Apakah karyawan puas dengan jam kerja yang ditetapkan perusahaan?',
            'OH15' => 'Apakah beban kerja karyawan dapat dikelola dengan baik?',
            'OH16' => 'Apakah kondisi fisik tempat kerja (suara, cahaya, suhu) terasa nyaman?',
            'OH17' => 'Apakah sebagian besar karyawan bertanggung jawab dan jarang absen tanpa alasan?',
            'OH18' => 'Apakah karyawan merasa bebas mendiskusikan kesulitan kerja dengan pemimpin atau rekan?',
            'OH19' => 'Apakah suasana di tempat kerja terasa positif dan mendukung?',
            'OH20' => 'Apakah semangat kerja karyawan secara umum tinggi?',
            'OH21' => 'Apakah karyawan saling membantu saat dibutuhkan?',
            'OH22' => 'Apakah tingkat kepercayaan antar karyawan sangat kuat?',
            'OH23' => 'Apakah kerjasama tim berjalan efektif?',
            'OH24' => 'Apakah karyawan saling menghormati satu sama lain?',
            'OH25' => 'Apakah karyawan memandang pekerjaan mereka sebagai bentuk pengabdian atau ibadah?',
            'OH26' => 'Apakah modal kerja mencukupi untuk operasional harian?',
            'OH27' => 'Apakah perusahaan mampu membayar gaji karyawan tepat waktu?',
            'OH28' => 'Apakah peralatan atau mesin dalam kondisi baik dan berfungsi optimal?',
            'OH29' => 'Apakah rantai pasok usaha berjalan lancar tanpa gangguan berarti?',
            'OH30' => 'Apakah permintaan produk atau jasa dapat diprediksi dengan baik?',
            'OH31' => 'Apakah kualitas hubungan jangka panjang dengan pelanggan sudah baik?',
            'OH32' => 'Apakah kemampuan usaha menjangkau pasar atau pelanggan baru sudah sangat baik?',
            'OH33' => 'Apakah pertumbuhan penjualan dalam setahun terakhir sangat memuaskan?',
            'OH34' => 'Apakah kewajiban usaha (pinjaman atau hutang) sangat terkendali?',
            'OH35' => 'Apakah kondisi arus kas usaha sangat sehat dan stabil?',
        ];
    }

    /**
     * Mengambil daftar pertanyaan asesmen berdasarkan role.
     */
    public function getQuestions(Request $request)
    {
        $user = $request->user();
        $role = $user->role;
        $id_umkm = $user->id_umkm;

        if (!$id_umkm) {
            return response()->json([
                'success' => false,
                'message' => 'Pengguna belum terdaftar di UMKM manapun.'
            ], 400);
        }

        // Ambil asesmen aktif terbaru
        $activeAssessment = Assessment::where('id_umkm', $id_umkm)
            ->latest()
            ->first();

        $alreadySubmitted = false;
        if ($activeAssessment) {
            $alreadySubmitted = DB::table('responses')
                ->where('id_assessment', $activeAssessment->id_assessment)
                ->where('id_user', $user->id_user)
                ->exists();
        }

        // Filter pertanyaan berdasarkan role
        $questions = DB::table('questions')
            ->where(function($query) use ($role) {
                $query->where('target_role', $role)
                      ->orWhere('target_role', 'both');
            })
            ->orderBy('id_factor')
            ->orderBy('id_question')
            ->get();

        $factorMeta = [
            1 => ['title' => 'Nilai Organisasi', 'desc' => 'Evaluasi nilai-nilai inti dan budaya operasional usaha anda.'],
            2 => ['title' => 'Keterlibatan Pemimpin', 'desc' => 'Evaluasi peran aktif pemimpin dalam operasional UMKM.'],
            3 => ['title' => 'Sumber Daya Institusi', 'desc' => 'Evaluasi ketersediaan dan kualitas sumber daya pendukung usaha.'],
            4 => ['title' => 'Stabilitas Operasional', 'desc' => 'Evaluasi kelancaran prosedur dan konsistensi operasional.'],
            5 => ['title' => 'Kualitas Tempat Kerja', 'desc' => 'Evaluasi kenyamanan dan keamanan lingkungan kerja.'],
            6 => ['title' => 'Kinerja Ekonomi', 'desc' => 'Evaluasi aspek finansial dan pertumbuhan ekonomi usaha.'],
        ];

        $translations = $this->questionTextTranslations();

        $sections = [];
        foreach ($questions as $q) {
            $fId = $q->id_factor;
            if (!isset($sections[$fId])) {
                $sections[$fId] = [
                    'factor_id' => $fId,
                    'title' => $factorMeta[$fId]['title'] ?? 'Faktor ' . $fId,
                    'desc' => $factorMeta[$fId]['desc'] ?? '',
                    'questions' => []
                ];
            }

            // Opsi jawaban
            $options = [];
            $qIdNum = (int) str_replace('OH', '', $q->id_question);
            if ($qIdNum <= 6) {
                if ($q->id_question === 'OH6') {
                    $options = ['Modal Sendiri', 'Keluarga', 'Bank / Kredit'];
                } else {
                    $options = ['Tidak', 'Sedang Proses', 'Ya'];
                }
            } else {
                $options = ['Sangat Tidak Setuju', 'Tidak Setuju', 'Ragu-ragu', 'Setuju', 'Sangat Setuju'];
            }

            $sections[$fId]['questions'][] = [
                'id' => $q->id_question,
                'text' => $translations[$q->id_question] ?? $q->teks_pertanyaan,
                'options' => $options
            ];
        }

        // Re-index to numeric array
        $sections = array_values($sections);

        $totalEmployees = User::where('id_umkm', $id_umkm)->where('role', 'employee')->count();
        $employeesFinishedCount = 0;
        if ($activeAssessment) {
            $employeesFinishedCount = DB::table('responses')
                ->join('users', 'responses.id_user', '=', 'users.id_user')
                ->where('responses.id_assessment', $activeAssessment->id_assessment)
                ->where('users.role', 'employee')
                ->distinct('responses.id_user')
                ->count('responses.id_user');
        }

        return response()->json([
            'success' => true,
            'message' => 'Daftar pertanyaan berhasil diambil',
            'data' => [
                'already_submitted' => $alreadySubmitted,
                'owner_finished' => $activeAssessment ? (bool)$activeAssessment->owner_finished : false,
                'employee_finished' => $activeAssessment ? (bool)$activeAssessment->employee_finished : false,
                'total_employees' => $totalEmployees,
                'employees_finished_count' => $employeesFinishedCount,
                'sections' => $sections
            ]
        ]);
    }

    /**
     * Menyimpan jawaban asesmen.
     */
    public function submitAnswers(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'answers' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = $request->user();
            $newAnswers = $request->input('answers');
            $id_umkm = $user->id_umkm;

            if (!$id_umkm) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengguna belum terdaftar di UMKM.'
                ], 400);
            }

            // Cari asesmen yang sedang berjalan (status Menunggu) untuk UMKM ini
            $assessment = Assessment::where('id_umkm', $id_umkm)
                ->where('status', 'Menunggu')
                ->first();

            if (!$assessment) {
                // Jika belum ada, buat record baru
                $assessment = new Assessment();
                $assessment->id_umkm = $id_umkm;
                $assessment->id_user = $user->id_user; // Tetap simpan id_user utama
                $assessment->status = 'Menunggu';
                $assessment->started_at = now();
                $assessment->answers = json_encode($newAnswers);
            } else {
                // Jika sudah ada, gabungkan jawaban baru dengan yang lama
                $existingAnswers = json_decode($assessment->answers, true) ?? [];
                $mergedAnswers = array_merge($existingAnswers, $newAnswers);
                $assessment->answers = json_encode($mergedAnswers);
            }

            // Tandai siapa yang menyelesaikan bagiannya
            if ($user->role === 'owner') {
                $assessment->owner_finished = true;
                $assessment->id_owner = $user->id_user;
            } else {
                $assessment->employee_finished = true;
                $assessment->id_employee = $user->id_user;
            }

            $assessment->save();

            // SIMPAN DATA KE TABEL RESPONSES (Individual)
            $scoreMap3 = [
                'Tidak' => 1, 'Sedang Proses' => 2, 'Ya' => 3,
                'Modal Sendiri' => 1, 'Keluarga' => 2, 'Bank / Kredit' => 3
            ];
            $scoreMap5 = [
                'Sangat Tidak Setuju' => 1, 'Tidak Setuju' => 2, 'Ragu-ragu' => 3, 'Setuju' => 4, 'Sangat Setuju' => 5
            ];

            foreach ($newAnswers as $qId => $val) {
                $qIdNum = (int) str_replace('OH', '', $qId);
                $nilai = ($qIdNum <= 6) ? ($scoreMap3[$val] ?? 1) : ($scoreMap5[$val] ?? 1);

                DB::table('responses')->updateOrInsert(
                    ['id_assessment' => $assessment->id_assessment, 'id_user' => $user->id_user, 'id_question' => $qId],
                    ['nilai' => $nilai, 'created_at' => now()]
                );
            }

            // Hitung skor akhir dari seluruh partisipan (Rata-rata)
            $this->calculateAndSaveScores($assessment);

            // Cek apakah seluruh anggota UMKM (Owner + Karyawan) sudah mengisi
            $totalAnggota = User::where('id_umkm', $id_umkm)->count();
            $totalResponden = DB::table('responses')
                ->where('id_assessment', $assessment->id_assessment)
                ->distinct('id_user')
                ->count('id_user');

            if ($totalResponden >= $totalAnggota) {
                $assessment->status = 'Selesai';
                $assessment->finished_at = now();
                $assessment->save();
            }

            // Hitung progres karyawan
            $totalEmployees = User::where('id_umkm', $id_umkm)->where('role', 'employee')->count();
            $employeesFinishedCount = DB::table('responses')
                ->join('users', 'responses.id_user', '=', 'users.id_user')
                ->where('responses.id_assessment', $assessment->id_assessment)
                ->where('users.role', 'employee')
                ->distinct('responses.id_user')
                ->count('responses.id_user');

            // Invalidate cache
            Cache::forget("umkm_latest_assessment_{$id_umkm}");
            Cache::forget("umkm_avg_factors_{$id_umkm}");
            Cache::forget("umkm_avg_total_score_{$id_umkm}");
            Cache::forget("umkm_insight_{$id_umkm}_{$assessment->id_assessment}");
            Cache::forget("umkm_insight_empty_{$id_umkm}");
            Cache::forget("umkm_factors_details_{$id_umkm}_{$assessment->id_assessment}");
            Cache::forget("umkm_factors_details_empty_{$id_umkm}");
            Cache::forget('global_stats_total_responden');
            Cache::forget('global_top_umkm');
            Cache::forget('global_bottom_umkm');
            Cache::forget('global_avg_total_score');
            Cache::forget('global_avg_factors');
            Cache::forget('global_avg_factors_raw');

            return response()->json([
                'success' => true,
                'message' => 'Asesmen berhasil disimpan',
                'data' => [
                    'assessment_id' => $assessment->id_assessment,
                    'owner_finished' => (bool) $assessment->owner_finished,
                    'employee_finished' => (bool) $assessment->employee_finished,
                    'total_employees' => $totalEmployees,
                    'employees_finished_count' => $employeesFinishedCount,
                    'assessment_status' => $assessment->status,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan asesmen: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mengambil ringkasan hasil asesmen (Dashboard data).
     */
    public function getDashboard(Request $request)
    {
        $user = $request->user();
        $id_umkm = $user->id_umkm;

        if (!$id_umkm) {
            return response()->json([
                'success' => false,
                'message' => 'Pengguna belum terdaftar di UMKM.'
            ], 400);
        }

        // Ambil asesmen terbaru yang sudah selesai
        $latestAssessment = Cache::remember("umkm_latest_assessment_{$id_umkm}", 300, function() use ($id_umkm) {
            return Assessment::where('id_umkm', $id_umkm)
                ->whereIn('status', ['Selesai', 'finished'])
                ->latest()
                ->first();
        });

        // Base Query untuk Assessments Global
        $assessmentQuery = Assessment::query();

        // Statistik
        $stats = [
            'total_umkm' => Cache::remember('global_stats_total_umkm', 600, fn() => Umkm::count()),
            'total_responden' => Cache::remember('global_stats_total_responden', 600, fn() => Assessment::count()),
            'total_kesehatan' => $latestAssessment 
                ? round($latestAssessment->total_score * 20) 
                : Cache::remember("global_avg_total_score", 300, function() use ($assessmentQuery) {
                    return round(($assessmentQuery->avg('total_score') ?? 0) * 20);
                }),
        ];

        // Data Radar Chart
        if ($latestAssessment) {
            $data_factors = [
                round($latestAssessment->score_ov * 20, 2),
                round($latestAssessment->score_ldi * 20, 2),
                round($latestAssessment->score_ins * 20, 2),
                round($latestAssessment->score_ops * 20, 2),
                round($latestAssessment->score_weq * 20, 2),
                round($latestAssessment->score_ect * 20, 2),
            ];
            
            $currentFactors = (object) [
                'ov' => $latestAssessment->score_ov,
                'ldi' => $latestAssessment->score_ldi,
                'ins' => $latestAssessment->score_ins,
                'ops' => $latestAssessment->score_ops,
                'weq' => $latestAssessment->score_weq,
                'ect' => $latestAssessment->score_ect,
            ];
        } else {
            $avgFactors = Cache::remember("global_avg_factors_raw", 300, function() {
                return Assessment::select(
                    DB::raw('AVG(score_ov) as ov'),
                    DB::raw('AVG(score_ldi) as ldi'),
                    DB::raw('AVG(score_ins) as ins'),
                    DB::raw('AVG(score_ops) as ops'),
                    DB::raw('AVG(score_weq) as weq'),
                    DB::raw('AVG(score_ect) as ect')
                )->first();
            });

            $data_factors = [
                round(($avgFactors->ov ?? 0) * 20, 2),
                round(($avgFactors->ldi ?? 0) * 20, 2),
                round(($avgFactors->ins ?? 0) * 20, 2),
                round(($avgFactors->ops ?? 0) * 20, 2),
                round(($avgFactors->weq ?? 0) * 20, 2),
                round(($avgFactors->ect ?? 0) * 20, 2),
            ];
            
            $currentFactors = (object) [
                'ov' => $avgFactors->ov ?? 0,
                'ldi' => $avgFactors->ldi ?? 0,
                'ins' => $avgFactors->ins ?? 0,
                'ops' => $avgFactors->ops ?? 0,
                'weq' => $avgFactors->weq ?? 0,
                'ect' => $avgFactors->ect ?? 0,
            ];
        }

        // Tentukan status kesehatan berdasarkan skor
        $status_kesehatan = 'KURANG';
        if ($stats['total_kesehatan'] >= 75) {
            $status_kesehatan = 'BAIK';
        } elseif ($stats['total_kesehatan'] >= 50) {
            $status_kesehatan = 'CUKUP';
        }

        // Ambil Insight Dinamis
        $cacheInsightKey = $latestAssessment 
            ? "umkm_insight_{$id_umkm}_{$latestAssessment->id_assessment}"
            : "umkm_insight_empty_{$id_umkm}";

        $insightText = Cache::remember($cacheInsightKey, 300, function() use ($currentFactors) {
            $factorAverages = [
                1 => $currentFactors->ov,
                2 => $currentFactors->ldi,
                3 => $currentFactors->ins,
                4 => $currentFactors->ops,
                5 => $currentFactors->weq,
                6 => $currentFactors->ect,
            ];

            asort($factorAverages);
            $weakestFactorId = key($factorAverages);
            $weakestScore = current($factorAverages);

            $recommendation = DB::table('recommendations')
                ->where('id_factor', $weakestFactorId)
                ->where('min_score', '<=', $weakestScore)
                ->where('max_score', '>=', $weakestScore)
                ->first();

            return $recommendation ? $recommendation->insight_text : 'Fokuslah pada perbaikan faktor-faktor dengan skor terendah untuk meningkatkan efisiensi operasional.';
        });

        // Hitung weakest factor name untuk label
        $factorAverages = [
            1 => $currentFactors->ov,
            2 => $currentFactors->ldi,
            3 => $currentFactors->ins,
            4 => $currentFactors->ops,
            5 => $currentFactors->weq,
            6 => $currentFactors->ect,
        ];
        asort($factorAverages);
        $weakestFactorId = key($factorAverages);

        $factorNames = [
            1 => 'Nilai Organisasi',
            2 => 'Keterlibatan Pemimpin',
            3 => 'Sumber Daya Institusi',
            4 => 'Stabilitas Operasional',
            5 => 'Kualitas Tempat Kerja',
            6 => 'Kinerja Ekonomi',
        ];
        $weakestFactorName = $factorNames[$weakestFactorId] ?? 'Faktor Utama';
        $weakestScore = current($factorAverages);

        // Ranking UMKM Global
        $top_umkm = Cache::remember('global_top_umkm', 600, function() {
            return Assessment::join('umkm', 'assessments.id_umkm', '=', 'umkm.id_umkm')
                ->select('umkm.nama_umkm', DB::raw('AVG(total_score) * 20 as total_score'))
                ->groupBy('umkm.id_umkm', 'umkm.nama_umkm')
                ->orderByDesc('total_score')
                ->limit(3)
                ->get();
        });

        $bottom_umkm = Cache::remember('global_bottom_umkm', 600, function() {
            return Assessment::join('umkm', 'assessments.id_umkm', '=', 'umkm.id_umkm')
                ->select('umkm.nama_umkm', DB::raw('AVG(total_score) * 20 as total_score'))
                ->groupBy('umkm.id_umkm', 'umkm.nama_umkm')
                ->orderBy('total_score')
                ->limit(3)
                ->get();
        });

        // 6 Factors Breakdown Details
        $factorMeta = [
            1 => [
                'title' => 'Nilai Organisasi', 
                'col' => 'score_ov', 
                'desc' => 'Faktor ini mengevaluasi nilai-nilai inti dan budaya operasional yang mendasari praktik etika dan interaksi profesional di dalam organisasi.'
            ],
            2 => [
                'title' => 'Keterlibatan Pemimpin', 
                'col' => 'score_ldi', 
                'desc' => 'Mengevaluasi peran aktif pemimpin dalam operasional dan bimbingan karyawan.'
            ],
            3 => [
                'title' => 'Sumber Daya Institusi', 
                'col' => 'score_ins', 
                'desc' => 'Mengevaluasi ketersediaan dan kualitas sumber daya pendukung usaha.'
            ],
            4 => [
                'title' => 'Stabilitas Operasional', 
                'col' => 'score_ops', 
                'desc' => 'Mengevaluasi kelancaran prosedur dan konsistensi operasional.'
            ],
            5 => [
                'title' => 'Kualitas Tempat Kerja', 
                'col' => 'score_weq', 
                'desc' => 'Mengevaluasi kenyamanan dan keamanan lingkungan kerja.'
            ],
            6 => [
                'title' => 'Kinerja Ekonomi', 
                'col' => 'score_ect', 
                'desc' => 'Mengevaluasi aspek finansial dan pertumbuhan ekonomi usaha.'
            ],
        ];

        $translations = $this->questionTextTranslations();
        
        $factorsDetailsCacheKey = $latestAssessment 
            ? "umkm_factors_details_{$id_umkm}_{$latestAssessment->id_assessment}"
            : "umkm_factors_details_empty_{$id_umkm}";

        $factorsDetails = Cache::remember($factorsDetailsCacheKey, 300, function() use ($latestAssessment, $factorMeta, $currentFactors, $translations) {
            $details = [];
            foreach ($factorMeta as $id => $meta) {
                $col = $meta['col'];
                $score = $latestAssessment ? (float) $latestAssessment->$col : ($currentFactors->$col ?? 1.0);
                
                $subIndicators = [];
                if ($latestAssessment) {
                    $questions = DB::table('questions')->where('id_factor', $id)->get();
                    $responses = DB::table('responses')
                        ->where('id_assessment', $latestAssessment->id_assessment)
                        ->get()
                        ->groupBy('id_question');

                    foreach ($questions as $q) {
                        $qId = $q->id_question;
                        $qIdNum = (int) str_replace('OH', '', $qId);
                        $userResponses = $responses->get($qId);
                        
                        $s = 1.0;
                        if ($userResponses && $userResponses->isNotEmpty()) {
                            $avgRaw = $userResponses->avg('nilai');
                            if ($qIdNum <= 6) {
                                $s = (($avgRaw - 1) / 2.0) * 4 + 1;
                            } else {
                                $s = $avgRaw;
                            }
                        }
                        
                        $cat = ($s >= 3.75) ? 'Tinggi' : (($s >= 2.5) ? 'Sedang' : 'Rendah');
                        $subIndicators[] = [
                            'question_id' => $qId,
                            'question_text' => $translations[$qId] ?? $q->teks_pertanyaan,
                            'score_raw' => round($s, 2),
                            'score_percentage' => round($s * 20, 2),
                            'category' => $cat
                        ];
                    }
                }

                $details[] = [
                    'id' => $id,
                    'title' => $meta['title'],
                    'description' => $meta['desc'],
                    'score_raw' => round($score, 2),
                    'score_percentage' => round($score * 20, 2),
                    'category_label' => ($score >= 3.67) ? 'Baik' : (($score >= 2.34) ? 'Sedang' : 'Kurang'),
                    'sub_indicators' => $subIndicators
                ];
            }
            return $details;
        });

        // Active Assessment Progress Info (Not cached because progress changes frequently when respondents fill)
        $activeAssessment = Assessment::where('id_umkm', $id_umkm)->latest()->first();
        $totalEmployees = User::where('id_umkm', $id_umkm)->where('role', 'employee')->count();
        $employeesFinishedCount = 0;
        
        if ($activeAssessment) {
            $employeesFinishedCount = DB::table('responses')
                ->join('users', 'responses.id_user', '=', 'users.id_user')
                ->where('responses.id_assessment', $activeAssessment->id_assessment)
                ->where('users.role', 'employee')
                ->distinct('responses.id_user')
                ->count('responses.id_user');
        }

        return response()->json([
            'success' => true,
            'message' => 'Data ringkasan asesmen berhasil diambil',
            'data' => [
                'has_assessment' => (bool)$latestAssessment,
                'skor_kesehatan' => $stats['total_kesehatan'],
                'status_kesehatan' => $status_kesehatan,
                'weakest_factor' => [
                    'id' => $weakestFactorId,
                    'name' => $weakestFactorName,
                    'score' => round($weakestScore * 20, 2)
                ],
                'insight_kritis' => $insightText,
                'total_responden' => $stats['total_responden'],
                'total_umkm' => $stats['total_umkm'],
                'radar_chart' => [
                    'labels' => array_values($factorNames),
                    'values' => $data_factors
                ],
                'factors_details' => $factorsDetails,
                'top_umkm_ranking' => $top_umkm,
                'bottom_umkm_ranking' => $bottom_umkm,
                'active_assessment_progress' => [
                    'assessment_id' => $activeAssessment ? $activeAssessment->id_assessment : null,
                    'status' => $activeAssessment ? $activeAssessment->status : 'Belum Ada',
                    'owner_finished' => $activeAssessment ? (bool)$activeAssessment->owner_finished : false,
                    'employee_finished' => $activeAssessment ? (bool)$activeAssessment->employee_finished : false,
                    'total_employees' => $totalEmployees,
                    'employees_finished_count' => $employeesFinishedCount
                ]
            ]
        ]);
    }

    /**
     * Helper untuk menghitung dan menyimpan skor assessment.
     */
    private function calculateAndSaveScores($assessment)
    {
        $responses = DB::table('responses')
            ->where('id_assessment', $assessment->id_assessment)
            ->get();

        if ($responses->isEmpty()) return;

        $questionValues = [];
        foreach ($responses as $r) {
            $questionValues[$r->id_question][] = $r->nilai;
        }

        $averagedAnswers = [];
        foreach ($questionValues as $qId => $values) {
            $avgRaw = array_sum($values) / count($values);
            $qIdNum = (int) str_replace('OH', '', $qId);

            if ($qIdNum <= 6) {
                $averagedAnswers[$qId] = (($avgRaw - 1) / 2.0) * 4 + 1;
            } else {
                $averagedAnswers[$qId] = $avgRaw;
            }
        }

        $allQuestions = DB::table('questions')->get();
        $factorScores = [1 => [], 2 => [], 3 => [], 4 => [], 5 => [], 6 => []];

        foreach ($allQuestions as $q) {
            if (isset($averagedAnswers[$q->id_question])) {
                $factorScores[$q->id_factor][] = $averagedAnswers[$q->id_question];
            }
        }

        $getAvg = function($scores) {
            return count($scores) > 0 ? array_sum($scores) / count($scores) : 1;
        };

        $assessment->score_ov = $getAvg($factorScores[1]);
        $assessment->score_ldi = $getAvg($factorScores[2]);
        $assessment->score_ins = $getAvg($factorScores[3]);
        $assessment->score_ops = $getAvg($factorScores[4]);
        $assessment->score_weq = $getAvg($factorScores[5]);
        $assessment->score_ect = $getAvg($factorScores[6]);

        $assessment->total_score = ($assessment->score_ov + $assessment->score_ldi + $assessment->score_ins + $assessment->score_ops + $assessment->score_weq + $assessment->score_ect) / 6;
        
        if ($assessment->total_score <= 2.33) {
            $assessment->kategori_kuartil = 'Kurang';
        } elseif ($assessment->total_score <= 3.66) {
            $assessment->kategori_kuartil = 'Cukup';
        } else {
            $assessment->kategori_kuartil = 'Baik';
        }
        
        $assessment->save();
    }
}
