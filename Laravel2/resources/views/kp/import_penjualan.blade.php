<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sinkronisasi Penjualan (Flashdisk) - CV Panca Indra Keemasan</title>
    <style>
        :root {
            --color-primary: #0f296b;
            --color-bg: #f3f4f6;
            --color-sidebar: #2b2b2b;
            --color-card: #ffffff;
            --color-muted: #6b7280;
            --radius-lg: 18px;
            --shadow-soft: 0 18px 40px rgba(15, 23, 42, 0.12);
            --font-heading: "Poppins", system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
            --font-body: "Inter", system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: var(--font-body);
            background-color: var(--color-bg);
            color: #111827;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        .layout {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            background: linear-gradient(180deg, #333333, #1e1e1e);
            color: #f5f5f5;
            padding: 1.6rem 1.6rem 2rem;
            width: 260px;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            overflow-y: auto;
        }

        .sidebar-brand {
            font-family: var(--font-heading);
            font-weight: 600;
            font-size: 1.15rem;
            margin-bottom: 2rem;
        }

        .sidebar-nav {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
        }

        .sidebar-nav li a {
            display: block;
            padding: 0.75rem 1rem;
            border-radius: 999px;
            font-size: 0.95rem;
            color: #f5f5f5;
            opacity: 0.7;
        }

        .sidebar-nav li a.active {
            background: rgba(255, 255, 255, 0.1);
            opacity: 1;
        }

        .sidebar-nav li a:hover {
            background: rgba(255, 255, 255, 0.12);
            opacity: 1;
        }

        .main {
            padding: 1.6rem 2.4rem 2.4rem;
            margin-left: 260px;
            width: calc(100% - 260px);
            min-height: 100vh;
        }

        .header-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.2rem;
        }

        .header-title {
            font-family: var(--font-heading);
            font-size: 1.4rem;
            font-weight: 600;
        }

        .btn-logout {
            padding: 0.55rem 1.4rem;
            border-radius: 999px;
            border: 1px solid var(--color-primary);
            background: transparent;
            color: var(--color-primary);
            font-weight: 500;
            cursor: pointer;
        }

        .btn-logout:hover {
            background: var(--color-primary);
            color: #fff;
        }

        .card {
            background-color: var(--color-card);
            border-radius: var(--radius-lg);
            padding: 1.8rem 2rem 2rem;
            box-shadow: var(--shadow-soft);
            max-width: 720px;
        }

        .section-heading {
            font-size: 1rem;
            font-weight: 600;
            margin: 0 0 0.3rem;
        }

        .section-subtext {
            font-size: 0.85rem;
            color: var(--color-muted);
            margin: 0 0 1.2rem;
        }

        .upload-field {
            border-radius: 999px;
            border: 1px solid #e5e7eb;
            padding: 0.8rem 1.1rem;
            background: #f9fafb;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.9rem;
            margin-bottom: 1.4rem;
            cursor: pointer;
        }

        .upload-field span {
            color: var(--color-muted);
        }

        .upload-field .btn-browse {
            border-radius: 999px;
            border: 1px solid #d1d5db;
            padding: 0.35rem 1rem;
            background: #ffffff;
            font-size: 0.85rem;
        }

        .file-input {
            display: none;
        }

        .btn-upload {
            display: inline-block;
            margin-top: 0.4rem;
            border-radius: 999px;
            border: none;
            padding: 0.65rem 2.4rem;
            background: #2563eb;
            color: #ffffff;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
        }

        .btn-upload:hover {
            filter: brightness(1.05);
        }

        .alert-info {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            color: #0369a1;
            line-height: 1.5;
        }

        .error-list {
            margin-top: 1rem;
            padding: 1rem;
            background: #fee2e2;
            border: 1px solid #fecaca;
            border-radius: 12px;
            font-size: 0.85rem;
            color: #b91c1c;
        }

        .error-list ul {
            margin: 0.5rem 0 0;
            padding-left: 1.5rem;
        }
    </style>
</head>

<body>
    <div class="layout">
        <aside class="sidebar">
            <div class="sidebar-brand">CV Panca Indra Keemasan</div>
            <ul class="sidebar-nav">
                <li><a href="{{ route('kp.dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route('kp.detail_barang') }}">Detail Barang</a></li>
                <li><a href="{{ route('kp.daftar_stok') }}">Daftar Stok</a></li>
                @if(auth()->check() && auth()->user()->role === 'admin')
                    <li><a href="{{ route('kp.koreksi_stok') }}">Koreksi Stok</a></li>
                @endif
                <li><a href="{{ route('kp.import') }}">Import CSV SID</a></li>
                <li><a href="{{ route('kp.import_penjualan') }}" class="active">Impor Penjualan App 2</a></li>

                @if(auth()->check() && auth()->user()->role === 'admin')
                    <li><a href="{{ route('kp.kelola_notifikasi') }}">Kelola Notifikasi Stok</a></li>
                @endif

                <li><a href="{{ route('kp.riwayat_stok') }}">Riwayat Import Stok</a></li>

                @if(auth()->check() && auth()->user()->role === 'admin')
                    <li><a href="{{ route('kp.kategori_barang') }}">Kategori Barang</a></li>
                @endif

                <li><a href="{{ route('kp.export_laporan') }}">Export Laporan</a></li>
            </ul>
        </aside>

        <main class="main">
            <div class="header-bar">
                <div class="header-title">Sinkronisasi Penjualan</div>
                <button class="btn-logout" type="button">Logout</button>
            </div>
            <form id="logoutForm" method="post" action="{{ route('kp.logout') }}" style="display:none;">@csrf</form>

            <section class="card">
                <h1 class="section-heading">Update Stok via Data Penjualan</h1>
                <p class="section-subtext">Gunakan modul ini untuk mengurangi stok secara otomatis berdasarkan file
                    laporan penjualan dari Aplikasi 2 (Kasir).</p>

                <div class="alert-info">
                    <strong>Cara Kerja:</strong>
                    <br>1. Ambil file CSV hasil penjualan dari **Flashdisk** (App 2).
                    <br>2. Upload file tersebut ke sistem ini (App 1).
                    <br>3. Sistem akan mencocokkan kode barang dan memotong stok sesuai jumlah yang terjual.
                </div>

                @if (session('success'))
                    <div
                        style="margin-bottom:1rem;padding:0.6rem 0.9rem;border-radius:12px;background:#dcfce7;color:#166534;">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('warning'))
                    <div
                        style="margin-bottom:1rem;padding:0.6rem 0.9rem;border-radius:12px;background:#fef9c3;color:#854d0e;border: 1px solid #fde047;">
                        {{ session('warning') }}
                    </div>
                @endif
                @if (session('error'))
                    <div
                        style="margin-bottom:1rem;padding:0.6rem 0.9rem;border-radius:12px;background:#fee2e2;color:#b91c1c;">
                        {{ session('error') }}
                    </div>
                @endif

                @if (session('import_errors') && count(session('import_errors')) > 0)
                    <div class="error-list">
                        <strong>Ditemukan kesalahan pada beberapa baris data:</strong>
                        <ul>
                            @foreach (session('import_errors') as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('kp.import_penjualan.post') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <label class="upload-field">
                        <span id="file-label">Pilih file CSV Penjualan</span>
                        <span class="btn-browse">Browse</span>
                        <input class="file-input" type="file" name="csv_file" id="csv_file" accept=".csv,text/csv"
                            required />
                    </label>

                    <button class="btn-upload" type="submit">SINKRONISASI STOK SEKARANG</button>
                </form>

                <div
                    style="margin-top:2rem; font-size: 0.85rem; color: #6b7280; border-top: 1px solid #eee; padding-top: 1rem;">
                    *Pastikan file CSV dari App 2 memiliki struktur: <strong>Kolom D = Kode Barang</strong> dan
                    <strong>Kolom F = Jumlah Terjual</strong>.
                </div>
            </section>
        </main>
    </div>
</body>
<script>
    (function () {
        var btns = document.querySelectorAll('.btn-logout');
        btns.forEach(function (btn) {
            btn.addEventListener('click', function () {
                if (confirm('Yakin ingin logout?')) {
                    document.getElementById('logoutForm').submit();
                }
            });
        });
        var fileInput = document.getElementById('csv_file');
        var fileLabel = document.getElementById('file-label');
        if (fileInput && fileLabel) {
            fileInput.addEventListener('change', function () {
                var name = this.files && this.files.length ? this.files[0].name : 'Pilih file CSV Penjualan';
                fileLabel.textContent = name;
            });
        }
    })();
</script>

</html>