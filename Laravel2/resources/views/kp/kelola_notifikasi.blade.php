<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Kelola Notifikasi Stok - CV Panca Indra Keemasan</title>
    <style>
        :root {
            --color-primary: #0f296b;
            --color-primary-dark: #1d3a8a;
            --color-bg: #f3f4f6;
            --color-dark: #111827;
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
            color: var(--color-dark);
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

        .page-title {
            font-family: var(--font-heading);
            font-size: 1.7rem;
            margin: 0.6rem 0 0.4rem;
        }

        .section-subtext {
            font-size: 0.9rem;
            color: var(--color-muted);
            margin: 0;
        }

        .highlight-card {
            background: linear-gradient(135deg, var(--color-primary), var(--color-primary-dark));
            color: #ffffff;
            border-radius: var(--radius-lg);
            padding: 1.6rem 2rem;
            display: flex;
            align-items: center;
            gap: 1.2rem;
            box-shadow: var(--shadow-soft);
            margin-bottom: 1.8rem;
        }

        .highlight-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.18);
            display: grid;
            place-items: center;
            font-size: 1.8rem;
            flex-shrink: 0;
        }

        .highlight-text {
            flex: 1;
            min-width: 0;
        }

        .highlight-text h2 {
            font-family: var(--font-heading);
            font-size: 1.3rem;
            margin: 0 0 0.25rem;
        }

        .highlight-text p {
            margin: 0;
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
        }

        .btn-primary {
            background: rgba(255, 255, 255, 0.18);
            border: 1px solid rgba(255, 255, 255, 0.45);
            border-radius: 999px;
            padding: 0.55rem 1.4rem;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            color: #ffffff;
            transition: background 0.2s ease;
        }

        .btn-primary:hover {
            background: rgba(255, 255, 255, 0.28);
        }

        .card {
            background-color: var(--color-card);
            border-radius: var(--radius-lg);
            padding: 1.8rem 2rem 2rem;
            box-shadow: var(--shadow-soft);
            margin-bottom: 1.8rem;
        }

        .card-heading {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.2rem;
        }

        .card-heading h3 {
            font-family: var(--font-heading);
            font-size: 1.1rem;
            margin: 0;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
        }

        .table thead {
            background: #f8fafc;
        }

        .table th,
        .table td {
            text-align: left;
            padding: 0.75rem 0.9rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .table tbody tr:last-child td {
            border-bottom: none;
        }

        .toggle {
            width: 46px;
            height: 26px;
            border-radius: 999px;
            background: #d1d5db;
            position: relative;
            display: inline-flex;
            align-items: center;
            padding: 0 4px;
            transition: background 0.2s ease;
            cursor: pointer;
        }

        .toggle::after {
            content: "";
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: #ffffff;
            position: absolute;
            left: 4px;
            transition: transform 0.2s ease;
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.15);
        }

        .toggle.active {
            background: var(--color-primary);
        }

        .toggle.active::after {
            transform: translateX(20px);
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            padding: 0.35rem 0.75rem;
            font-size: 0.8rem;
            border-radius: 999px;
            font-weight: 500;
        }

        .status-warning {
            background: rgba(245, 158, 11, 0.16);
            color: #b45309;
        }

        .status-danger {
            background: rgba(239, 68, 68, 0.16);
            color: #b91c1c;
        }

        /* Form Tambah Styles */
        .form-tambah {
            display: none;
        }

        .form-tambah.active {
            display: block;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.4rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .form-group label {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--color-dark);
        }

        .form-group label .required {
            color: #ef4444;
        }

        .form-group input,
        .form-group select {
            padding: 0.7rem 1rem;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 0.9rem;
            font-family: var(--font-body);
            color: var(--color-dark);
            background: #ffffff;
            transition: all 0.2s ease;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--color-primary);
            box-shadow: 0 0 0 3px rgba(15, 41, 107, 0.1);
        }

        .form-group select {
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.7rem center;
            background-size: 1.2rem;
            padding-right: 2.5rem;
        }

        .toggle-wrapper {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .toggle-label {
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--color-dark);
        }

        .form-actions {
            display: flex;
            gap: 0.8rem;
            margin-top: 1.8rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e5e7eb;
        }

        .btn {
            padding: 0.65rem 1.6rem;
            border-radius: 999px;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
        }

        .btn-submit {
            background: var(--color-primary);
            color: #ffffff;
        }

        .btn-submit:hover {
            background: var(--color-primary-dark);
        }

        .btn-secondary {
            background: transparent;
            color: var(--color-dark);
            border: 1px solid #d1d5db;
        }

        .btn-secondary:hover {
            background: #f9fafb;
        }

        .list-view {
            display: block;
        }

        .list-view.hidden {
            display: none;
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

            .form-grid {
                grid-template-columns: 1fr;
            }

            .highlight-text h2 {
                font-size: 1.1rem;
            }

            .highlight-text p {
                font-size: 0.85rem;
            }
        }

        @media (max-width: 640px) {
            .highlight-card {
                flex-direction: column;
                align-items: flex-start;
            }

            .btn-primary {
                align-self: stretch;
                text-align: center;
            }
        }
    </style>
</head>

<body>
    <div class="layout">
        <aside class="sidebar">
            <div class="sidebar-brand">CV Panca Indra Keemasan</div>
            <ul class="sidebar-nav">
                <li><a href="{{ route('kp.dashboard') }}"
                        class="{{ request()->routeIs('kp.dashboard') ? 'active' : '' }}">Dashboard</a></li>
                <li><a href="{{ route('kp.detail_barang') }}"
                        class="{{ request()->routeIs('kp.detail_barang') ? 'active' : '' }}">Detail Barang</a></li>
                <li><a href="{{ route('kp.daftar_stok') }}"
                        class="{{ request()->routeIs('kp.daftar_stok') ? 'active' : '' }}">Daftar Stok</a></li>
                @if(auth()->check() && auth()->user()->role === 'admin')
                    <li><a href="{{ route('kp.koreksi_stok') }}"
                            class="{{ request()->routeIs('kp.koreksi_stok') ? 'active' : '' }}">Koreksi Stok</a></li>
                @endif
                <li><a href="{{ route('kp.import') }}"
                        class="{{ request()->routeIs('kp.import') ? 'active' : '' }}">Import CSV SID</a></li>
                <li><a href="{{ route('kp.import_penjualan') }}"
                        class="{{ request()->routeIs('kp.import_penjualan') ? 'active' : '' }}">Impor Penjualan App
                        2</a></li>

                @if(auth()->check() && auth()->user()->role === 'admin')
                    <li><a href="{{ route('kp.kelola_notifikasi') }}"
                            class="{{ request()->routeIs('kp.kelola_notifikasi') ? 'active' : '' }}">Kelola Notifikasi
                            Stok</a></li>
                @endif

                <li><a href="{{ route('kp.riwayat_stok') }}"
                        class="{{ request()->routeIs('kp.riwayat_stok') ? 'active' : '' }}">Riwayat Import Stok</a></li>

                @if(auth()->check() && auth()->user()->role === 'admin')
                    <li><a href="{{ route('kp.kategori_barang') }}"
                            class="{{ request()->routeIs('kp.kategori_barang') ? 'active' : '' }}">Kategori Barang</a></li>
                @endif

                <li><a href="{{ route('kp.export_laporan') }}"
                        class="{{ request()->routeIs('kp.export_laporan') ? 'active' : '' }}">Export Laporan</a></li>
            </ul>
        </aside>

        <main class="main">
            <div class="header-bar">
                <div class="header-title">Kelola Notifikasi Stok</div>
                <button class="btn-logout" type="button">Logout</button>
            </div>

            @if (session('success'))
                <div style="margin-bottom:1rem;padding:0.8rem 1rem;border-radius:12px;background:#dcfce7;color:#166534">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div style="margin-bottom:1rem;padding:0.8rem 1rem;border-radius:12px;background:#fee2e2;color:#b91c1c">
                    {{ session('error') }}
                </div>
            @endif

            @if(isset($critical) && $critical->count() > 0)
                <div style="margin-bottom:1rem;padding:0.8rem 1rem;border-radius:12px;background:#fee2e2;color:#b91c1c">
                    Terdapat {{ $critical->count() }} barang dengan stok di bawah minimum dan notifikasi aktif.
                </div>
            @endif

            <!-- List View (Default) -->
            <div id="listView" class="list-view">
                <section class="highlight-card">
                    <div class="highlight-icon">🔔</div>
                    <div class="highlight-text">
                        <h2>Notifikasi untuk kondisi stok tertentu</h2>
                        <p>Atur batas minimum stok untuk menerima peringatan otomatis.</p>
                    </div>
                    <button class="btn-primary" type="button" onclick="showFormTambah()">Tambah</button>
                </section>

                <section class="card">
                    <div class="card-heading">
                        <h3>Daftar Notifikasi Barang</h3>
                        <p class="section-subtext">Data masih statis dan dapat disesuaikan nanti.</p>
                    </div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nama Barang</th>
                                <th>Kode Barang</th>
                                <th>Batas Minimum</th>
                                <th>Aktif</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php($mode = isset($settings['mode']) ? (string) $settings['mode'] : 'per_item')
                            @php($defaultMin = isset($settings['default_min']) ? (int) $settings['default_min'] : 10)
                            @forelse($items as $item)
                            @php($catName = $item->category->name ?? null)
                            @php($catDefault = ($mode === 'category' && $catName !== null && isset($categoryDefaults[$catName])) ? (int) $categoryDefaults[$catName] : $defaultMin)
                            @php($threshold = $mode === 'global' ? $defaultMin : ($mode === 'category' ? max(($item->min_stock ?? 0), $catDefault) : max(($item->min_stock ?? 0), $defaultMin)))
                            <tr>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->code }}</td>
                                <td>{{ number_format($threshold) }}</td>
                                <td><span class="toggle {{ $item->notif_active ? 'active' : '' }}"
                                        data-code="{{ $item->code }}"
                                        data-active="{{ $item->notif_active ? 1 : 0 }}"></span></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4">Belum ada data barang.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </section>

                <section class="card">
                    <div class="card-heading">
                        <h3>Stok Kritis Hari Ini</h3>
                        <p class="section-subtext">Ringkasan barang yang berada di bawah batas minimum.</p>
                    </div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nama Barang</th>
                                <th>Kode Barang</th>
                                <th>Stok Saat Ini</th>
                                <th>Batas Minimum</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $item)
                            @php($mode = isset($settings['mode']) ? (string) $settings['mode'] : 'per_item')
                            @php($defaultMin = isset($settings['default_min']) ? (int) $settings['default_min'] : 10)
                            @php($catName = $item->category->name ?? null)
                            @php($catDefault = ($mode === 'category' && $catName !== null && isset($categoryDefaults[$catName])) ? (int) $categoryDefaults[$catName] : $defaultMin)
                            @php($threshold = $mode === 'global' ? $defaultMin : ($mode === 'category' ? max(($item->min_stock ?? 0), $catDefault) : max(($item->min_stock ?? 0), $defaultMin)))
                            @php($status = $item->stock <= 0 ? 'habis' : ($item->stock < $threshold ? 'menipis' : 'aman'))
                            @if($status !== 'aman')
                                <tr>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->code }}</td>
                                    <td>{{ number_format($item->stock) }}</td>
                                    <td>{{ number_format($threshold) }}</td>
                                    <td>
                                        @if($status === 'habis')
                                            <span class="status-pill status-danger">Stok Habis</span>
                                        @else
                                            <span class="status-pill status-warning">Menipis</span>
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            @empty
                            <tr>
                                <td colspan="5">Tidak ada data.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </section>
            </div>

            <!-- Form Tambah View -->
            <div id="formTambahView" class="form-tambah">
                <section class="highlight-card">
                    <div class="highlight-icon">🔔</div>
                    <div class="highlight-text">
                        <h2>Konfigurasi Notifikasi</h2>
                        <p>Atur preferensi notifikasi stok secara global.</p>
                    </div>
                </section>

                <section class="card">
                    <div class="card-heading">
                        <h3>Pengaturan Notifikasi Stok</h3>
                        <p class="section-subtext">Sesuaikan cara sistem menentukan stok kritis dan status notifikasi.
                        </p>
                    </div>

                    <form id="notificationForm" method="post" action="{{ route('kp.notif.save') }}">
                        @csrf
                        <div class="form-grid">
                            <div class="form-group">
                                <label>
                                    Notifikasi Stok
                                </label>
                                <div class="toggle-wrapper">
                                    @php($on = isset($settings['notif_on']) ? (int) $settings['notif_on'] === 1 : 1)
                                    <span class="toggle {{ $on ? 'active' : '' }}" id="toggleStatus"
                                        onclick="toggleSwitch()"></span>
                                    <span class="toggle-label" id="statusText">{{ $on ? 'ON' : 'OFF' }}</span>
                                    <input type="hidden" name="notif_on" id="notifOnInput"
                                        value="{{ $on ? 'on' : 'off' }}" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="default_min">
                                    Default Minimum Stok
                                </label>
                                <input type="number" id="default_min" name="default_min" placeholder="Contoh: 10"
                                    min="0"
                                    value="{{ isset($settings['default_min']) ? (int) $settings['default_min'] : 10 }}"
                                    required>
                            </div>

                            <div class="form-group">
                                <label>
                                    Mode
                                </label>
                                @php($mode = isset($settings['mode']) ? (string) $settings['mode'] : 'per_item')
                                <div style="display:flex;flex-direction:column;gap:0.6rem">
                                    <label style="display:flex;align-items:center;gap:0.6rem">
                                        <input type="radio" name="mode" value="per_item" {{ $mode === 'per_item' ? 'checked' : '' }}>
                                        <span>Gunakan min_stok per barang</span>
                                    </label>
                                    <label style="display:flex;align-items:center;gap:0.6rem">
                                        <input type="radio" name="mode" value="global" {{ $mode === 'global' ? 'checked' : '' }}>
                                        <span>Gunakan default global</span>
                                    </label>
                                    <label style="display:flex;align-items:center;gap:0.6rem">
                                        <input type="radio" name="mode" value="category" {{ $mode === 'category' ? 'checked' : '' }}>
                                        <span>Gunakan Default Kategori</span>
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="date">
                                    Pilih Tanggal
                                </label>
                                <input type="text" id="date" name="date" placeholder="dd/mm/yyyy"
                                    value="{{ isset($settings['date']) ? $settings['date'] : '' }}">
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-submit">Simpan</button>
                            <button type="button" class="btn btn-secondary" onclick="hideFormTambah()">Batal</button>
                        </div>
                    </form>
                </section>
            </div>
        </main>
    </div>

    <script>
        const csrfToken = "{{ csrf_token() }}";
        // Toggle antara List View dan Form Tambah View
        function showFormTambah() {
            document.getElementById('listView').classList.add('hidden');
            document.getElementById('formTambahView').classList.add('active');
        }

        function hideFormTambah() {
            document.getElementById('formTambahView').classList.remove('active');
            document.getElementById('listView').classList.remove('hidden');
            document.getElementById('notificationForm').reset();
            // Reset toggle to active
            document.getElementById('toggleStatus').classList.add('active');
            document.getElementById('statusText').textContent = 'Aktif';
        }

        // Toggle switch untuk status aktif
        function toggleSwitch() {
            const toggle = document.getElementById('toggleStatus');
            const statusText = document.getElementById('statusText');
            const notifOnInput = document.getElementById('notifOnInput');

            toggle.classList.toggle('active');

            if (toggle.classList.contains('active')) {
                statusText.textContent = 'ON';
                notifOnInput.value = 'on';
            } else {
                statusText.textContent = 'OFF';
                notifOnInput.value = 'off';
            }
        }

        // Toggle pada list untuk barang yang ada
        document.querySelectorAll('.table .toggle[data-code]').forEach(function (el) {
            el.addEventListener('click', function () {
                const code = this.getAttribute('data-code');
                const current = this.classList.contains('active');
                const nextActive = current ? 0 : 1;
                fetch("{{ route('kp.notif.toggle') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": csrfToken
                    },
                    body: JSON.stringify({ code: code, active: !!nextActive })
                }).then(function (r) { return r.json(); })
                    .then(function (data) {
                        if (data && data.ok) {
                            if (nextActive) el.classList.add('active'); else el.classList.remove('active');
                        } else {
                            alert(data && data.message ? data.message : 'Gagal mengubah status notifikasi.');
                        }
                    }).catch(function () {
                        alert('Gagal mengubah status notifikasi.');
                    });
            });
        });

        // Validasi angka positif
        document.getElementById('default_min').addEventListener('input', function (e) {
            if (this.value < 0) this.value = 0;
        });

        // Konfirmasi logout
        document.querySelector('.btn-logout').addEventListener('click', function () {
            if (confirm('Yakin ingin logout?')) {
                // Submit logout form atau redirect
                alert('Logout berhasil!');
                // window.location.href = '/logout';
            }
        });
    </script>
</body>

</html>