# Arsitektur Alternatif: Ekosistem 3-Aplikasi (Local/Offline)

Dokumen ini merinci profil aplikasi, distribusi peran, dan pilihan model arsitektur untuk ekosistem aplikasi lokal berbasis transfer data CSV.

---

## Profil Aplikasi Ekosistem

### 1. Aplikasi 1: Manajemen Stok & Inventori (The Core)
*   **Peran Utama:** Sebagai pusat data tunggal (Single Source of Truth) untuk seluruh inventori perusahaan.
*   **Target Pengguna:** Admin Gudang, Kepala Logistik, atau Bagian Pengadaan (Purchasing).
*   **Tujuan utama:** Menjamin akurasi jumlah fisik barang, standarisasi kode barang, dan pengelolaan harga modal/jual pusat.
*   **Karakteristik Data:** Data Master yang menjadi acuan bagi aplikasi lainnya.

### 2. Aplikasi 2: Pengelolaan Customer & Penjualan (The Terminal)
*   **Peran Utama:** Sebagai pintu transaksi harian dan mesin pengumpul data pelanggan.
*   **Target Pengguna:** Kasir, Staf Penjualan (Sales), dan Administrasi Toko.
*   **Tujuan Utama:** Mempercepat proses pelayanan customer, mencatat loyalitas member, dan menghitung omzet penjualan harian.
*   **Karakteristik Data:** Data Transaksional yang berubah sangat cepat setiap menit.

### 3. Aplikasi 3: Monitoring & Executive Dashboard (The Intelligence)
*   **Peran Utama:** Sebagai alat analisis data gabungan untuk pengambilan keputusan bisnis.
*   **Target Pengguna:** Pemilik Bisnis (Owner), Manajer Toko, atau Investor.
*   **Tujuan Utama:** Melihat pergerakan tren stok vs penjualan secara visual, memonitor margin keuntungan harian, dan segmentasi customer paling aktif.
*   **Karakteristik Data:** Data Analitik (Hasil gabungan data stok dari App 1 dan data penjualan dari App 2).

---

## Model 1: Pemisahan Operasi (Gudang vs Toko)
Model ini cocok jika bagian gudang pembelian memiliki tim yang berbeda dengan bagian kasir toko.

### Distribusi Peran:
- **Aplikasi 1 (Inventory Center):** Fokus pada "Input" (Pembelian dari Vendor, Import SID).
- **Aplikasi 2 (Sales Center):** Fokus pada "Output" (Transaksi ke Customer, Pendataan Member).
- **Aplikasi 3 (Audit Hub):** Fokus pada "Rekonsiliasi" (Mencocokkan barang keluar di App 2 dengan stok berkurang di App 1).

### Alur Data Flashdisk:
1. `Master_Stok.csv` dari App 1 -> App 2.
2. `Laporan_Harian.csv` dari App 2 -> App 3.
3. `Riwayat_Stok.csv` dari App 1 -> App 3.

---

## Model 2: CRM Focused (Pusat Kendali Customer)
Model ini memprioritaskan CRM (Customer Relationship Management) sebagai pendorong bisnis.

### Distribusi Peran:
- **Aplikasi 1 (Inventory Driver):** Hanya penyedia database barang murni.
- **Aplikasi 2 (Customer Core):** Pusat data customer, poin loyalitas, riwayat hutang/piutang, dan manajemen promo.
- **Aplikasi 3 (Business Intelligent):** Menganalisis kaitan antara kategori barang dengan perilaku belanja pelanggan.

---

## Model 3: Penyatuan Operasional (Sistem Terintegrasi Lokal)
Model yang paling efisien jika server lokal bisa diakses oleh beberapa komputer melalui jaringan kabel (LAN) atau WiFi lokal tanpa internet.

### Distribusi Peran:
- **Aplikasi 1 & 2 (Unified System):** Stok dan Customer berada dalam satu server lokal yang sama. Tim stok dan Kasir mengakses aplikasi yang sama dengan hak akses berbeda (RBAC).
- **Aplikasi 3 (Executive Mirror):** Aplikasi terpisah yang dipegang pemilik untuk menarik rekap mingguan/bulanan dari sistem pusat.

---

## Matriks Perbandingan Fitur & Tanggung Jawab

| Dimensi | Aplikasi 1 (Stok) | Aplikasi 2 (Customer) | Aplikasi 3 (Monitoring) |
| :--- | :--- | :--- | :--- |
| **Fokus Utama** | Akurasi & Inventori | Transaksi & Relasi | Strategi & Visualisasi |
| **Input Utama** | CSV SID / Opname | Data Customer / Scan Barcode | CSV dari App 1 & App 2 |
| **Output Utama** | Master_Stok.csv | Data_Penjualan.csv | Dashboard & Laporan Owner |
| **Tipe Pengguna** | Admin Gudang / Logistik | Kasir / Sales | Pemilik / Manajemen |
| **Tugas Kritis** | Validasi stok masuk | Pencatatan detail pembeli | Analisis tren & profit |

---

## Perbedaan Karakteristik Lintas Aplikasi

| Perbedaan | Aplikasi 1 | Aplikasi 2 | Aplikasi 3 |
| :--- | :--- | :--- | :--- |
| **Sumber Data** | Eksternal (SID) & Internal | Interaksi Real-time | Gabungan (Konsolidasi) |
| **Sifat Data** | Master (Lambat Berubah) | Transaksional (Cepat Berubah) | Analitik (Historis) |
| **Tingkat Detail** | Sangat detail per SKU barang | Sangat detail per customer | Global (Ringkasan/Agregat) |
| **Ketergantungan** | Mandiri | Tergantung data App 1 | Tergantung data App 1 & 2 |
| **Penyimpanan Lokal** | Katalog Barang & Log | Profil & Riwayat Belanja | Metrik & KPI Performa |

---

## Matriks Perbandingan Model Arsitektur

| Kriteria | Sistem Awal (Gudang Data) | Alternatif 1 (Gudang vs Toko) | Alternatif 2 (CRM Focused) | Alternatif 3 (Penyatuan Lokal) |
| :--- | :--- | :--- | :--- | :--- |
| **Kelebihan** | Sesuai permintaan awal | Audit sangat kuat | Marketing akurat | Sangat efisien |
| **Kekurangan** | Manual Sync sering | Ribet Ekspor-Impor | Butuh data detil | Butuh jaringan LAN |
| **Cocok Untuk** | Toko Retail Umum | Toko & Gudang Pisah | Toko Membership | Toko Satu Lokasi |
