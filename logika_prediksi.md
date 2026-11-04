# Daftar Logika Prediksi dan Analisis Hasil Asesmen SiSehat

Dokumen ini berisi daftar dan lokasi logika perhitungan, klasifikasi (kategori), serta penentuan rekomendasi (analisis/prediksi) kesehatan UMKM pada proyek **SiSehat**.

---

## 1. Logika Perhitungan Skor & Kategori Kuartil (Scoring Engine)

Logika ini digunakan untuk menghitung rata-rata nilai dari jawaban responden, mengonversi skala pertanyaan tertentu, menghitung skor masing-masing faktor, dan mengklasifikasikan tingkat kesehatan UMKM ke dalam 3 kategori kuartil (*Kurang*, *Cukup*, *Baik*).

* **Lokasi File & Baris Kode:**
  * **Web Controller:** [`DashboardController.php`](file:///c:/laragon/www/sisehat/app/Http/Controllers/DashboardController.php#L821-L880) (Metode `calculateAndSaveScores`)
  * **API Controller:** [`AssessmentController.php`](file:///c:/laragon/www/sisehat/app/Http/Controllers/Api/AssessmentController.php#L568-L624) (Metode `calculateAndSaveScores`)
* **Detail Logika:**
  * Pertanyaan OH1 s.d OH6 (skala 1-3) dikonversi ke skala 1-5 menggunakan rumus:
    $$\text{Nilai Konversi} = \frac{\text{Skor Raw} - 1}{2.0} \times 4 + 1$$
  * Pertanyaan OH7 s.d OH35 tetap menggunakan nilai aslinya (skala 1-5).
  * Menghitung nilai rata-rata dari seluruh responden untuk setiap pertanyaan.
  * Menghitung nilai rata-rata untuk masing-masing dari 6 Faktor (Nilai Organisasi, Keterlibatan Pemimpin, Sumber Daya Institusi, Stabilitas Operasional, Kualitas Tempat Kerja, Kinerja Ekonomi).
  * Nilai akhir (`total_score`) didapat dari rata-rata nilai ke-6 faktor tersebut.
  * Klasifikasi tingkat kesehatan UMKM berdasarkan `total_score` (skala 1-5):
    * `total_score <= 2.33` $\rightarrow$ **Kurang**
    * `total_score <= 3.66` $\rightarrow$ **Cukup**
    * `total_score > 3.66` $\rightarrow$ **Baik**

---

## 2. Klasifikasi Status Kesehatan Umum (Skala 0-100)

Logika ini memetakan nilai rata-rata kesehatan ke dalam persentase skala 0-100 dan menentukan label kondisi kesehatan akhir.

* **Lokasi File & Baris Kode:**
  * **Dashboard Utama:** [`DashboardController.php`](file:///c:/laragon/www/sisehat/app/Http/Controllers/DashboardController.php#L185-L191) (Metode `index`)
  * **Rekomendasi Page:** [`DashboardController.php`](file:///c:/laragon/www/sisehat/app/Http/Controllers/DashboardController.php#L345-L349) (Metode `rekomendasi`)
  * **API Dashboard:** [`AssessmentController.php`](file:///c:/laragon/www/sisehat/app/Http/Controllers/Api/AssessmentController.php#L375-L381) (Metode `getDashboard`)
* **Detail Logika:**
  * Mengalikan total skor rata-rata dengan 20 untuk menghasilkan skala 0-100.
  * Menentukan status kesehatan berdasarkan skor persentase tersebut:
    * `skor >= 75` $\rightarrow$ **BAIK** / **KONDISI BAIK**
    * `skor >= 50` $\rightarrow$ **CUKUP** / **KONDISI CUKUP**
    * `skor < 50` $\rightarrow$ **KURANG** / **KONDISI KURANG**

---

## 3. Logika Analisis Faktor Terlemah & Rekomendasi Dinamis

Logika ini mengidentifikasi area operasional bisnis (dari 6 faktor) yang memiliki skor terendah, lalu secara dinamis menarik teks analisis dan solusi rekomendasi yang sesuai dari database.

* **Lokasi File & Baris Kode:**
  * **Web Controller:** [`DashboardController.php`](file:///c:/laragon/www/sisehat/app/Http/Controllers/DashboardController.php#L193-L228) (Metode `index`)
  * **API Controller:** [`AssessmentController.php`](file:///c:/laragon/www/sisehat/app/Http/Controllers/Api/AssessmentController.php#L383-L414) (Metode `getDashboard`)
* **Detail Logika:**
  * Mengumpulkan skor rata-rata dari ke-6 faktor.
  * Mengurutkan skor secara *ascending* (dari kecil ke besar) untuk mendeteksi faktor dengan skor terkecil (`weakestFactorId`).
  * Melakukan *query* ke tabel `recommendations` menggunakan filter:
    * `id_factor = weakestFactorId`
    * `min_score <= weakestScore`
    * `max_score >= weakestScore`
  * Menampilkan `insight_text` dari data yang ditemukan sebagai rekomendasi utama di dashboard.

---

## 4. Klasifikasi Kategori Faktor Utama dan Sub-Indikator (API Detail)

Logika ini memetakan nilai individu dari sub-indikator pertanyaan serta faktor utama ke dalam label kategori tertentu (Rendah/Sedang/Tinggi atau Kurang/Sedang/Baik) pada response API.

* **Lokasi File & Baris Kode:**
  * **Kategori Sub-Indikator:** [`AssessmentController.php`](file:///c:/laragon/www/sisehat/app/Http/Controllers/Api/AssessmentController.php#L495-L502) (Metode `getDashboard`)
    * `skor_sub_indikator >= 3.75` $\rightarrow$ **Tinggi**
    * `skor_sub_indikator >= 2.50` $\rightarrow$ **Sedang**
    * `skor_sub_indikator < 2.50` $\rightarrow$ **Rendah**
  * **Kategori Faktor Utama:** [`AssessmentController.php`](file:///c:/laragon/www/sisehat/app/Http/Controllers/Api/AssessmentController.php#L512) (Metode `getDashboard`)
    * `skor_faktor >= 3.67` $\rightarrow$ **Baik**
    * `skor_faktor >= 2.34` $\rightarrow$ **Sedang**
    * `skor_faktor < 2.34` $\rightarrow$ **Kurang**
