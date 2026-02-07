<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Import CSV SID - CV Panca Indra Keemasan</title>
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

        @media (max-width: 900px) {
            .layout {
                grid-template-columns: 70px minmax(0, 1fr);
            }

            .sidebar-brand {
                font-size: 0.9rem;
            }

            .sidebar-nav li a {
                font-size: 0.8rem;
                padding-inline: 0.6rem;
            }

            .card {
                max-width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="layout">
        <aside class="sidebar">
            <div class="sidebar-brand">CV Panca Indra Keemasan</div>

            @if(auth()->check() && (auth()->user()->role === 'admin' || auth()->user()->role === 'staff') && (!auth()->user()->has_imported || !auth()->user()->has_viewed_details))
                <!-- Onboarding mode: show progress indicator only -->
                <div style="margin-top:2rem;padding:1rem;background:rgba(255,255,255,0.05);border-radius:12px;">
                    <div style="font-size:0.85rem;color:#9ca3af;margin-bottom:0.8rem;">Setup Awal</div>
                    <div style="display:flex;flex-direction:column;gap:0.6rem;">
                        <div style="display:flex;align-items:center;gap:0.6rem;">
                            <div
                                style="width:24px;height:24px;border-radius:50%;background:{{ auth()->user()->has_imported ? '#10b981' : '#3b82f6' }};display:flex;align-items:center;justify-content:center;font-size:0.75rem;">
                                @if(auth()->user()->has_imported)
                                    ✓
                                @else
                                    1
                                @endif
                            </div>
                            <span style="font-size:0.9rem;opacity:{{ auth()->user()->has_imported ? '0.7' : '1' }};">Import
                                CSV SID</span>
                        </div>
                        <div style="display:flex;align-items:center;gap:0.6rem;">
                            <div
                                style="width:24px;height:24px;border-radius:50%;background:{{ auth()->user()->has_viewed_details ? '#10b981' : (auth()->user()->has_imported ? '#3b82f6' : 'rgba(255,255,255,0.1)') }};display:flex;align-items:center;justify-content:center;font-size:0.75rem;">
                                @if(auth()->user()->has_viewed_details)
                                    ✓
                                @else
                                    2
                                @endif
                            </div>
                            <span style="font-size:0.9rem;opacity:{{ auth()->user()->has_imported ? '1' : '0.5' }};">Lihat
                                Detail Barang</span>
                        </div>
                        <div style="display:flex;align-items:center;gap:0.6rem;">
                            <div
                                style="width:24px;height:24px;border-radius:50%;background:{{ auth()->user()->has_viewed_details ? '#3b82f6' : 'rgba(255,255,255,0.1)' }};display:flex;align-items:center;justify-content:center;font-size:0.75rem;">
                                3</div>
                            <span
                                style="font-size:0.9rem;opacity:{{ auth()->user()->has_viewed_details ? '1' : '0.5' }};">Dashboard</span>
                        </div>
                    </div>
                </div>
            @else
                <!-- Normal mode: show all menu items -->
                <ul class="sidebar-nav">
                    <li><a href="{{ route('kp.dashboard') }}" class="{{ request()->routeIs('kp.dashboard') ? 'active' : '' }}">Dashboard</a></li>
                    <li><a href="{{ route('kp.detail_barang') }}" class="{{ request()->routeIs('kp.detail_barang') ? 'active' : '' }}">Detail Barang</a></li>
                    <li><a href="{{ route('kp.daftar_stok') }}" class="{{ request()->routeIs('kp.daftar_stok') ? 'active' : '' }}">Daftar Stok</a></li>
                    @if(auth()->check() && auth()->user()->role === 'admin')
                        <li><a href="{{ route('kp.koreksi_stok') }}" class="{{ request()->routeIs('kp.koreksi_stok') ? 'active' : '' }}">Koreksi Stok</a></li>
                    @endif
                    <li><a href="{{ route('kp.import') }}" class="{{ request()->routeIs('kp.import') ? 'active' : '' }}">Import CSV SID</a></li>
                    <li><a href="{{ route('kp.import_penjualan') }}" class="{{ request()->routeIs('kp.import_penjualan') ? 'active' : '' }}">Impor Penjualan App 2</a></li>

                    @if(auth()->check() && auth()->user()->role === 'admin')
                        <li><a href="{{ route('kp.kelola_notifikasi') }}" class="{{ request()->routeIs('kp.kelola_notifikasi') ? 'active' : '' }}">Kelola Notifikasi Stok</a></li>
                    @endif

                    <li><a href="{{ route('kp.riwayat_stok') }}" class="{{ request()->routeIs('kp.riwayat_stok') ? 'active' : '' }}">Riwayat Import Stok</a></li>

                    @if(auth()->check() && auth()->user()->role === 'admin')
                        <li><a href="{{ route('kp.kategori_barang') }}" class="{{ request()->routeIs('kp.kategori_barang') ? 'active' : '' }}">Kategori Barang</a></li>
                    @endif

                    <li><a href="{{ route('kp.export_laporan') }}" class="{{ request()->routeIs('kp.export_laporan') ? 'active' : '' }}">Export Laporan</a></li>
                </ul>
            @endif
        </aside>

        <main class="main">
            <div class="header-bar">
                <div class="header-title">Import CSV SID</div>
                <button class="btn-logout" type="button">Logout</button>
            </div>
            <form id="logoutForm" method="post" action="{{ route('kp.logout') }}" style="display:none;">@csrf</form>

            <section class="card">
                <h1 class="section-heading">Import CSV SID</h1>
                <p class="section-subtext">Unggah file CSV atau XLS dari SID untuk memperbarui data barang. Format kolom
                    yang didukung: kode, nama, harga_beli, harga_jual, stok, min_stok, kategori, notif.</p>

                @if (session('success'))
                    <div
                        style="margin-bottom:1rem;padding:0.6rem 0.9rem;border-radius:12px;background:#dcfce7;color:#166534;">
                        {{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div
                        style="margin-bottom:1rem;padding:0.6rem 0.9rem;border-radius:12px;background:#fee2e2;color:#b91c1c;">
                        {{ session('error') }}</div>
                @endif
                @if ($errors->any())
                    <div
                        style="margin-bottom:1rem;padding:0.6rem 0.9rem;border-radius:12px;background:#fee2e2;color:#b91c1c;">
                        {{ $errors->first() }}</div>
                @endif

                <form action="{{ route('kp.import.post') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div style="margin-bottom:1.4rem;">
                        <label for="import_date" style="display:block;margin-bottom:0.5rem;font-weight:500;">Tanggal Import</label>
                        <input type="datetime-local" id="import_date" name="import_date"
                            value="{{ now()->format('Y-m-d\TH:i') }}"
                            style="width:100%;padding:0.75rem;border:1px solid #e5e7eb;border-radius:12px;font-family:inherit;font-size:0.95rem;">
                        <div style="font-size:0.85rem;color:#6b7280;margin-top:0.4rem;">
                            Tanggal ini akan tercatat sebagai waktu masuknya data stok.
                        </div>
                    </div>

                    <label class="upload-field">
                        <span id="file-label">Pilih file CSV</span>
                        <span class="btn-browse">Browse</span>
                        <input class="file-input" type="file" name="sid_file" id="sid_file"
                            accept=".csv,.xls,.xlsx,text/csv" required />
                    </label>

                    <div style="display:flex;gap:0.6rem;flex-wrap:wrap">
                        <button class="btn-upload" type="submit">UPLOAD</button>
                        <button class="btn-upload" type="submit" formaction="{{ route('kp.import.preview') }}"
                            formmethod="post" style="background:#10b981">PREVIEW</button>
                    </div>
                </form>

                @if(auth()->check() && (auth()->user()->role === 'admin' || auth()->user()->role === 'staff') && !auth()->user()->has_imported && \App\Models\Item::count() > 0)
                    <div style="margin-top:1.5rem;padding:1.2rem;background:#f0f9ff;border:1px solid #bae6fd;border-radius:12px;">
                        <p style="margin:0 0 0.8rem;color:#0c4a6e;font-weight:600;font-size:0.95rem;">
                            💡 Tidak perlu import sekarang?
                        </p>
                        <p style="margin:0 0 1rem;color:#075985;font-size:0.9rem;line-height:1.5;">
                            Anda dapat melewati proses import dan langsung ke dashboard. Import dapat dilakukan kapan saja nanti dari menu Import CSV SID.
                        </p>
                        <form method="post" action="{{ route('kp.import.skip') }}" style="margin:0;">
                            @csrf
                            <button type="submit" style="background:#fff;border:1px solid #0ea5e9;color:#0369a1;padding:0.65rem 1.5rem;border-radius:999px;cursor:pointer;font-weight:500;font-size:0.9rem;">
                                Lewati & Langsung ke Dashboard →
                            </button>
                        </form>
                    </div>
                @endif

                @isset($preview)
                    <div style="margin-top:1rem">
                        <div style="margin-bottom:0.5rem;color:#374151">Preview data (maks 50 baris)</div>
                        <div style="overflow:auto;border:1px solid #e5e7eb;border-radius:12px;max-height:420px">
                            <table style="width:100%;border-collapse:collapse">
                                <thead style="position:sticky;top:0;background:#f9fafb">
                                    <tr>
                                        @foreach(($preview['headers'] ?? []) as $h)
                                            <th
                                                style="text-align:left;padding:8px;border-bottom:1px solid #e5e7eb;font-weight:600">
                                                {{ $h }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(($preview['rows'] ?? []) as $r)
                                        <tr>
                                            @foreach($r as $cell)
                                                <td style="padding:8px;border-bottom:1px solid #f3f4f6">
                                                    {{ is_scalar($cell) ? $cell : '' }}</td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endisset
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
        var fileInput = document.getElementById('sid_file');
        var fileLabel = document.getElementById('file-label');
        if (fileInput && fileLabel) {
            fileInput.addEventListener('change', function () {
                var name = this.files && this.files.length ? this.files[0].name : 'Pilih file CSV';
                fileLabel.textContent = name;
            });
        }
    })();
</script>

</html>