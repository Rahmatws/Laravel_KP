# Hasil Analisis Fitur & Fungsionalitas Aplikasi

Dokumen ini menyajikan analisis mendalam mengenai fungsionalitas aplikasi manajemen stok berbasis Laravel sebagai pusat data utama (Single Source of Truth).

## 1. Modul Manajemen Inventori
*   **Pusat Pengendali Stok (SSOT):** Sistem secara ketat mengontrol setiap pergerakan stok. Tidak ada perubahan stok yang terjadi tanpa validasi internal.
*   **Identifikasi Barang Unik:** Menggunakan `kode_barang` sebagai kunci unik yang terhubung dengan kategori, harga (beli/jual), dan riwayat perubahan.
*   **Sistem Satuan:** Mendukung pengelolaan satuan barang (misal: pcs, box, kg) yang terbawa dari proses import maupun input manual.

## 2. Fitur Onboarding Kontrol Admin
*   **Gerbang Akses Bersyarat:** Menggunakan middleware khusus untuk mendeteksi status pengguna baru. 
*   **Alur Kerja Wajib:** Memaksa Admin melewati urutan: *Import Data* -> *Verifikasi Detail* -> *Verifikasi Daftar Stok* sebelum dashboard terbuka penuh. Hal ini mencegah kesalahan data di awal penggunaan.

## 3. Sistem Notifikasi Stok Menipis (Dynamic Alerts)
*   **Mode Pintar (Global):** Peringatan seragam berdasarkan satu nilai baku dari pengaturan sistem.
*   **Mode Kategori:** Memungkinkan perbedaan ambang batas antar jenis barang (misal: stok minimal bahan baku berbeda dengan produk jadi).
*   **Mode Spesifik (Per Item):** Memberikan kontrol presisi pada level barang individu, namun tetap memiliki fallback ke nilai default jika data tidak diisi.

## 4. Sistem Log & Riwayat (Audit Trail)
*   **Tabel `stock_changes`:** Setiap aksi (Import SID, API Order, Edit Manual, Koreksi) menghasilkan satu entri log.
*   **Metadata Transaksi:** Mencatat siapa yang melakukan perubahan, kapan, jumlahnya, dan catatan teknis (dalam format JSON) untuk transparansi data.

## 5. Integrasi Ekosistem via Transfer Flashdisk (CSV)
*   **Aplikasi 1 (Sumber Data):** Menghasilkan `Master_Stok.csv` (Input: SID Retail Pro, Output: CSV untuk App 2 & 3).
*   **Aplikasi 2 (Operasional):** Menerima `Master_Stok.csv` sebagai database barang dan menghasilkan `Data_Penjualan.csv` (Input: CSV App 1, Output: CSV untuk App 3).
*   **Aplikasi 3 (Monitor):** Menerima data dari kedua aplikasi lainnya untuk divisualisasikan menjadi dashboard keputusan (Input: CSV App 1 & App 2).
*   **Keamanan Terpadu:** Tidak diperlukan sistem keamanan online (Sanctum/API) karena jalur data bersifat fisik (Offline Flashdisk).

## 6. Modul Pelaporan & Export
*   **Fleksibilitas Format:** Mendukung PDF untuk arsip siap cetak, serta Excel/CSV untuk pengolahan data lanjutan.
*   **Sinkronisasi Laporan:** Laporan "Barang Menipis" dan "Stok Akhir" menggunakan logika kalkulasi yang sama dengan yang ditampilkan di Dashboard, memastikan keseragaman informasi.

## 7. Penanganan File & Impor Data
*   **Detektor Cerdas:** Mampu mengenali delimiter CSV secara otomatis (koma, titik koma, tab) dan melakukan pembersihan data (*normalization*) pada nama kolom untuk meningkatkan toleransi terhadap kesalahan file input.

---

## 8. Kelebihan (Strengths)
1.  **Single Source of Truth (SSOT):** Keakuratan stok terjamin karena semua pergerakan wajib melalui validasi sistem dan tercatat dalam riwayat.
2.  **Aman dari Race Condition:** Penggunaan `lockForUpdate()` pada API Order melindungi database dari kesalahan hitung saat banyak transaksi masuk bersamaan.
3.  **Fleksibilitas Alert Tinggi:** Sistem notifikasi tidak kaku, dapat menyesuaikan dengan karakteristik tiap kategori atau barang unik.
4.  **Alur Kerja Terjamin:** Middleware onboarding memastikan integritas data awal (seperti impor data) tidak terlewatkan oleh pengguna.

## 9. Kekurangan (Weaknesses) & Peningkatan
1.  **Keamanan API (Prioritas):** Endpoint API belum menggunakan autentikasi token (Laravel Sanctum). Akses masih bersifat terbuka bagi yang mengetahui URL.
2.  **Skalabilitas Impor:** Proses impor data dalam jumlah sangat besar (misal: >50.000 baris) berpotensi menyebabkan timeout karena masih diproses secara sinkron.
3.  **Visualisasi Data:** Dashboard saat ini sangat informatif, namun penambahan grafik tren stok masuk/keluar di masa depan akan sangat membantu pengambilan keputusan.
4.  **Audit Trail Lanjutan:** Meskipun log stok sudah ada, penambahan log aktivitas user (siapa mengubah setting, siapa menghapus kategori) akan memperkuat sisi keamanan.
5.  **Penyimpanan Gambar:** Belum tersedia fitur untuk menyimpan foto fisik barang di database.
