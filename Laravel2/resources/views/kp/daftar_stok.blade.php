<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Daftar Stok - CV Panca Indra Keemasan</title>
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
            padding: 1.4rem 1.6rem;
            box-shadow: var(--shadow-soft);
        }

        .section-heading {
            font-size: 1rem;
            font-weight: 600;
            margin: 0 0 0.3rem;
        }

        .section-subtext {
            font-size: 0.85rem;
            color: var(--color-muted);
            margin: 0 0 1rem;
        }

        .stock-search {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            border-radius: 999px;
            border: 1px solid #e5e7eb;
            padding: 0.45rem 0.9rem;
            background: #f9fafb;
            font-size: 0.85rem;
            margin-bottom: 1rem;
        }

        .stock-search input {
            border: none;
            outline: none;
            background: transparent;
            width: 100%;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
        }

        .table th,
        .table td {
            padding: 0.7rem 0.6rem;
            text-align: left;
        }

        .table thead th {
            font-size: 0.8rem;
            color: var(--color-muted);
            border-bottom: 1px solid #e3e3e3;
        }

        .table tbody tr:nth-child(even) {
            background: #f9fafb;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.2rem 0.6rem;
            border-radius: 999px;
            font-size: 0.78rem;
        }

        .status-warning {
            background: #fee2e2;
            color: #b91c1c;
        }

        .status-ok {
            background: #dcfce7;
            color: #166534;
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
            @if(auth()->check() && (auth()->user()->role === 'admin' || auth()->user()->role === 'staff') && auth()->user()->has_viewed_details && !auth()->user()->has_viewed_stock)
                <div
                    style="margin-bottom:1.5rem;padding:1.2rem 1.4rem;border-radius:12px;background:linear-gradient(135deg, #0f296b, #1e40af);color:#fff;box-shadow:0 8px 20px rgba(15, 41, 107, 0.25);">
                    <div style="font-weight:600;font-size:1.05rem;margin-bottom:0.4rem;">✅ Setup Hampir Selesai!</div>
                    <div style="font-size:0.9rem;opacity:0.95;margin-bottom:1rem;line-height:1.5;">
                        Anda telah melihat daftar stok. Klik tombol di bawah untuk menyelesaikan setup dan masuk ke
                        dashboard.
                    </div>
                    <form method="post" action="{{ route('kp.daftar_stok.complete') }}" style="margin:0;">
                        @csrf
                        <button type="submit"
                            style="background:#fff;color:#0f296b;border:none;border-radius:999px;padding:0.7rem 1.6rem;font-weight:600;cursor:pointer;font-size:0.9rem;box-shadow:0 4px 12px rgba(0,0,0,0.15);">
                            Selesai & Masuk Dashboard →
                        </button>
                    </form>
                </div>
            @endif

            <div class="header-bar">
                <div class="header-title">Daftar Stok</div>
                <button class="btn-logout" type="button">Logout</button>
            </div>
            <form id="logoutForm" method="post" action="{{ route('kp.logout') }}" style="display:none;">@csrf</form>

            <section class="card">
                <h1 class="section-heading">Daftar Stok</h1>
                <p class="section-subtext">Monitoring kondisi persediaan barang secara keseluruhan</p>
                @if(isset($criticalCount) && $criticalCount > 0)
                    <div id="criticalBanner"
                        style="margin-bottom:0.8rem;padding:0.6rem 0.9rem;border-radius:12px;background:#fee2e2;color:#b91c1c;cursor:pointer">
                        Terdapat {{ $criticalCount }} barang dengan stok di bawah minimum dan notifikasi aktif.
                    </div>
                @endif

                <form id="stockForm" action="{{ route('kp.daftar_stok') }}" method="get"
                    style="border:1px solid #e5e7eb;border-radius:14px;background:#f5f8ff;padding:1rem;margin-bottom:1rem">
                    <div
                        style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.6rem;color:#334155;font-weight:600">
                        <span
                            style="display:inline-flex;width:20px;height:20px;border-radius:999px;align-items:center;justify-content:center;background:#e0e7ff;color:#1e3a8a">i</span>
                        <span>Panel Filter Utama</span>
                    </div>
                    <div style="display:flex;gap:0.5rem;flex-wrap:wrap">
                        <button type="button" class="btn-filter-main" data-main="all"
                            style="border:{{ ($main ?? 'all') === 'all' ? 'none' : '1px solid #e5e7eb' }};border-radius:999px;padding:0.5rem 0.9rem;background:{{ ($main ?? 'all') === 'all' ? '#2563eb' : '#fff' }};color:{{ ($main ?? 'all') === 'all' ? '#fff' : '#111827' }}">Semua
                            Stok</button>
                        <button type="button" class="btn-filter-main" data-main="low"
                            style="border:{{ ($main ?? 'all') === 'low' ? 'none' : '1px solid #e5e7eb' }};border-radius:999px;padding:0.5rem 0.9rem;background:{{ ($main ?? 'all') === 'low' ? '#2563eb' : '#fff' }};color:{{ ($main ?? 'all') === 'low' ? '#fff' : '#111827' }}">Stok
                            Menipis</button>
                        <button type="button" class="btn-filter-main" data-main="zero"
                            style="border:{{ ($main ?? 'all') === 'zero' ? 'none' : '1px solid #e5e7eb' }};border-radius:999px;padding:0.5rem 0.9rem;background:{{ ($main ?? 'all') === 'zero' ? '#2563eb' : '#fff' }};color:{{ ($main ?? 'all') === 'zero' ? '#fff' : '#111827' }}">Stok
                            Habis</button>
                    </div>
                    <div style="margin-top:0.4rem;color:#6b7280;font-size:0.85rem">Default: Stok Menipis</div>
                    <input type="hidden" name="main" id="mainInput" value="{{ $main ?? 'all' }}" />

                    <div
                        style="border:1px solid #e5e7eb;border-radius:14px;background:#ffffff;padding:1rem;margin:1rem 0">
                        <div style="color:#374151;font-weight:600;margin-bottom:0.6rem">Filter Lanjutan</div>
                        <div
                            style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:0.8rem;margin-bottom:0.8rem">
                            <div>
                                <div style="font-size:0.85rem;color:#6b7280;margin-bottom:0.3rem">Kategori</div>
                                <select name="category" id="filter-category"
                                    style="width:100%;border:1px solid #e5e7eb;border-radius:10px;padding:0.55rem;background:#f9fafb">
                                    <option value="" {{ ($category ?? '') === '' ? 'selected' : '' }}>Semua Kategori
                                    </option>
                                    @foreach($categories as $c)
                                        <option value="{{ $c->name }}" {{ ($category ?? '') === $c->name ? 'selected' : '' }}>
                                            {{ $c->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <div style="font-size:0.85rem;color:#6b7280;margin-bottom:0.3rem">Urutkan</div>
                                <select name="sort" id="filter-sort"
                                    style="width:100%;border:1px solid #e5e7eb;border-radius:10px;padding:0.55rem;background:#f9fafb">
                                    <option value="stock_asc" {{ ($sort ?? 'stock_asc') === 'stock_asc' ? 'selected' : '' }}>
                                        Stok Terendah</option>
                                    <option value="stock_desc" {{ ($sort ?? 'stock_asc') === 'stock_desc' ? 'selected' : '' }}>Stok Tertinggi</option>
                                    <option value="name_asc" {{ ($sort ?? 'stock_asc') === 'name_asc' ? 'selected' : '' }}>
                                        Nama A-Z</option>
                                    <option value="name_desc" {{ ($sort ?? 'stock_asc') === 'name_desc' ? 'selected' : '' }}>
                                        Nama Z-A</option>
                                </select>
                            </div>
                        </div>
                        <div class="stock-search">
                            <span>🔍</span>
                            <input name="q" id="filter-search" value="{{ $q ?? '' }}" type="search"
                                placeholder="Cari Kode Barang : [ P00__ ]" />
                        </div>
                        <div style="font-size:0.75rem;color:#9ca3af;margin-top:-0.6rem">• search bukan fokus utama •
                            hanya bantu jika data sangat banyak</div>
                    </div>
                </form>

                <table class="table" id="stock-table">
                    <thead>
                        <tr>
                            <th>Kode Barang</th>
                            <th>Nama Barang</th>
                            <th>Stok</th>
                            <th>Minimum</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                        @php($catName = $item->category->name ?? null)
                        @php($catDefault = ((isset($mode) && $mode === 'category') && $catName !== null && isset($categoryDefaults[$catName])) ? (int) $categoryDefaults[$catName] : ($defaultMin ?? 0))
                        @php($threshold = (isset($mode) && $mode === 'global') ? ($defaultMin ?? 0) : ((isset($mode) && $mode === 'category') ? max(($item->min_stock ?? 0), $catDefault) : max(($item->min_stock ?? 0), ($defaultMin ?? 0))))
                        @php($status = $item->stock <= 0 ? 'Habis' : ($item->stock < $threshold ? 'Menipis' : 'Aman'))
                        @php($ket = $item->stock <= 0 ? 'Stok kosong' : ($item->stock < $threshold ? 'Segera lakukan restock' : '-'))
                        <tr>
                            <td>{{ $item->code }}</td>
                            <td>{{ $item->name }}</td>
                            <td
                                style="font-weight:600;color:{{ $item->stock <= 0 ? '#dc2626' : ($item->stock < $threshold ? '#d97706' : '#16a34a') }}">
                                {{ number_format($item->stock) }}
                            </td>
                            <td>{{ number_format($threshold) }}</td>
                            <td>
                                @if($item->stock <= 0)
                                    <span class="status-pill" style="background:#fee2e2;color:#b91c1c">🔴 Habis</span>
                                @elseif($item->stock < $threshold)
                                    <span class="status-pill" style="background:#fef3c7;color:#a16207">⚠️ Menipis</span>
                                @else
                                    <span class="status-pill" style="background:#dcfce7;color:#166534">✅ Aman</span>
                                @endif
                            </td>
                            <td>{{ $ket }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6">Belum ada data barang.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <div style="margin-top:0.8rem;font-size:0.9rem;color:#374151">
                    <div style="margin-bottom:0.4rem">Keterangan Status (Biar User Paham)</div>
                    <div style="display:flex;gap:0.8rem;flex-wrap:wrap">
                        <span class="status-pill" style="background:#dcfce7;color:#166534">✅ Aman • Stok di atas batas
                            minimum</span>
                        <span class="status-pill" style="background:#fef3c7;color:#a16207">⚠️ Menipis • Stok <
                                minimum</span>
                                <span class="status-pill" style="background:#fee2e2;color:#b91c1c">🔴 Habis • Stok =
                                    0</span>
                    </div>
                </div>

                <div style="display:flex;align-items:center;justify-content:flex-end;gap:0.8rem;margin-top:0.8rem">
                    <span style="color:#6b7280">Menampilkan {{ $items->count() }} dari {{ $items->total() }} data</span>
                    <div style="display:flex;gap:0.4rem;align-items:center">
                        @if ($items->onFirstPage())
                            <span
                                style="border:1px solid #e5e7eb;border-radius:999px;padding:0.4rem 0.8rem;background:#f3f4f6;color:#9ca3af">Sebelumnya</span>
                        @else
                            <a href="{{ $items->previousPageUrl() }}"
                                style="border:1px solid #e5e7eb;border-radius:999px;padding:0.4rem 0.8rem;background:#fff;color:#111827">Sebelumnya</a>
                        @endif
                        <span
                            style="border:1px solid #e5e7eb;border-radius:999px;padding:0.4rem 0.8rem;background:#fff">{{ $items->currentPage() }}</span>
                        @if ($items->hasMorePages())
                            <a href="{{ $items->nextPageUrl() }}"
                                style="border:1px solid #e5e7eb;border-radius:999px;padding:0.4rem 0.8rem;background:#fff;color:#111827">Berikutnya</a>
                        @else
                            <span
                                style="border:1px solid #e5e7eb;border-radius:999px;padding:0.4rem 0.8rem;background:#f3f4f6;color:#9ca3af">Berikutnya</span>
                        @endif
                    </div>
                </div>
            </section>
        </main>
    </div>
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
            document.querySelectorAll('.btn-filter-main').forEach(function (b) {
                b.addEventListener('click', function () {
                    var mainInput = document.getElementById('mainInput');
                    if (mainInput) mainInput.value = this.getAttribute('data-main');
                    var form = document.getElementById('stockForm');
                    if (form) form.submit();
                });
            });
            var selCat = document.getElementById('filter-category');
            if (selCat) selCat.addEventListener('change', function () { document.getElementById('stockForm').submit(); });
            var selSort = document.getElementById('filter-sort');
            if (selSort) selSort.addEventListener('change', function () { document.getElementById('stockForm').submit(); });
            var inp = document.getElementById('filter-search');
            if (inp) inp.addEventListener('keydown', function (e) { if (e.key === 'Enter') document.getElementById('stockForm').submit(); });
        })();
    </script>
    @if(($notifOn ?? 1) === 1 && ($criticalCount ?? 0) > 0)
        <script>
            (function () {
                var shown = sessionStorage.getItem('popup_stock_shown');
                if (!shown) {
                    var overlay = document.createElement('div');
                    overlay.style.position = 'fixed';
                    overlay.style.inset = '0';
                    overlay.style.background = 'rgba(0,0,0,0.35)';
                    overlay.style.display = 'flex';
                    overlay.style.alignItems = 'center';
                    overlay.style.justifyContent = 'center';
                    overlay.style.zIndex = '9999';
                    var modal = document.createElement('div');
                    modal.style.background = '#fff';
                    modal.style.borderRadius = '16px';
                    modal.style.boxShadow = '0 18px 40px rgba(15, 23, 42, 0.18)';
                    modal.style.maxWidth = '560px';
                    modal.style.width = '92%';
                    modal.style.padding = '1rem 1.2rem';
                    var title = document.createElement('div');
                    title.style.fontWeight = '600';
                    title.style.fontSize = '1rem';
                    title.textContent = 'Notifikasi Stok Menipis';
                    var desc = document.createElement('div');
                    desc.style.color = '#374151';
                    desc.style.margin = '0.4rem 0 0.8rem';
                    desc.textContent = 'Terdapat {{ $criticalCount }} barang di bawah batas minimum.';
                    var list = document.createElement('div');
                    list.style.maxHeight = '220px';
                    list.style.overflow = 'auto';
                    list.style.border = '1px solid #e5e7eb';
                    list.style.borderRadius = '12px';
                    list.style.padding = '0.4rem 0.6rem';
                    list.innerHTML = `{!! collect($criticalItems ?? [])->map(function ($i) {
            return '<div style="display:flex;justify-content:space-between;padding:6px 2px;border-bottom:1px solid #f3f4f6"><span>' . e($i->code) . ' • ' . e($i->name) . '</span><span style="font-weight:600;color:#b91c1c">' . number_format($i->stock) . '</span></div>';
        })->implode('') !!}`;
                    var actions = document.createElement('div');
                    actions.style.display = 'flex';
                    actions.style.justifyContent = 'flex-end';
                    actions.style.gap = '0.6rem';
                    actions.style.marginTop = '0.8rem';
                    var btnClose = document.createElement('button');
                    btnClose.textContent = 'Tutup';
                    btnClose.style.border = '1px solid #d1d5db';
                    btnClose.style.borderRadius = '999px';
                    btnClose.style.padding = '0.5rem 1rem';
                    btnClose.style.background = '#fff';
                    var btnGo = document.createElement('a');
                    btnGo.textContent = 'Lihat Stok Menipis';
                    btnGo.href = '{{ route('kp.daftar_stok', ['main' => 'low']) }}';
                    btnGo.style.background = '#2563eb';
                    btnGo.style.color = '#fff';
                    btnGo.style.borderRadius = '999px';
                    btnGo.style.padding = '0.5rem 1rem';
                    actions.appendChild(btnClose);
                    actions.appendChild(btnGo);
                    modal.appendChild(title);
                    modal.appendChild(desc);
                    modal.appendChild(list);
                    modal.appendChild(actions);
                    overlay.appendChild(modal);
                    document.body.appendChild(overlay);
                    btnClose.addEventListener('click', function () { overlay.remove(); sessionStorage.setItem('popup_stock_shown', '1'); });
                    overlay.addEventListener('click', function (e) { if (e.target === overlay) { overlay.remove(); sessionStorage.setItem('popup_stock_shown', '1'); } });
                    btnGo.addEventListener('click', function (e) {
                        e.preventDefault();
                        sessionStorage.setItem('popup_stock_shown', '1');
                        var mainInput = document.getElementById('mainInput');
                        var form = document.getElementById('stockForm');
                        if (mainInput && form) {
                            mainInput.value = 'low';
                            form.submit();
                        } else {
                            window.location.href = '{{ route('kp.daftar_stok', ['main' => 'low']) }}';
                        }
                    });
                }
            })();
        </script>
        @if(($notifOn ?? 1) === 1 && ($criticalCount ?? 0) > 0)
            <script>
                (function () {
                    var banner = document.getElementById('criticalBanner');
                    if (banner) {
                        banner.addEventListener('click', function () {
                            var overlay = document.createElement('div');
                            overlay.style.position = 'fixed';
                            overlay.style.inset = '0';
                            overlay.style.background = 'rgba(0,0,0,0.35)';
                            overlay.style.display = 'flex';
                            overlay.style.alignItems = 'center';
                            overlay.style.justifyContent = 'center';
                            overlay.style.zIndex = '9999';
                            var modal = document.createElement('div');
                            modal.style.background = '#fff';
                            modal.style.borderRadius = '16px';
                            modal.style.boxShadow = '0 18px 40px rgba(15, 23, 42, 0.18)';
                            modal.style.maxWidth = '560px';
                            modal.style.width = '92%';
                            modal.style.padding = '1rem 1.2rem';
                            var title = document.createElement('div');
                            title.style.fontWeight = '600';
                            title.style.fontSize = '1rem';
                            title.textContent = 'Notifikasi Stok Menipis';
                            var desc = document.createElement('div');
                            desc.style.color = '#374151';
                            desc.style.margin = '0.4rem 0 0.8rem';
                            desc.textContent = 'Terdapat {{ $criticalCount }} barang di bawah batas minimum.';
                            var list = document.createElement('div');
                            list.style.maxHeight = '220px';
                            list.style.overflow = 'auto';
                            list.style.border = '1px solid #e5e7eb';
                            list.style.borderRadius = '12px';
                            list.style.padding = '0.4rem 0.6rem';
                            list.innerHTML = `{!! collect($criticalItems ?? [])->map(function ($i) {
                    return '<div style="display:flex;justify-content:space-between;padding:6px 2px;border-bottom:1px solid #f3f4f6"><span>' . e($i->code) . ' • ' . e($i->name) . '</span><span style="font-weight:600;color:#b91c1c">' . number_format($i->stock) . '</span></div>';
                })->implode('') !!}`;
                            var actions = document.createElement('div');
                            actions.style.display = 'flex';
                            actions.style.justifyContent = 'flex-end';
                            actions.style.gap = '0.6rem';
                            actions.style.marginTop = '0.8rem';
                            var btnClose = document.createElement('button');
                            btnClose.textContent = 'Tutup';
                            btnClose.style.border = '1px solid #d1d5db';
                            btnClose.style.borderRadius = '999px';
                            btnClose.style.padding = '0.5rem 1rem';
                            btnClose.style.background = '#fff';
                            var btnGo = document.createElement('a');
                            btnGo.textContent = 'Lihat Stok Menipis';
                            btnGo.href = '{{ route('kp.daftar_stok', ['main' => 'low']) }}';
                            btnGo.style.background = '#2563eb';
                            btnGo.style.color = '#fff';
                            btnGo.style.borderRadius = '999px';
                            btnGo.style.padding = '0.5rem 1rem';
                            actions.appendChild(btnClose);
                            actions.appendChild(btnGo);
                            modal.appendChild(title);
                            modal.appendChild(desc);
                            modal.appendChild(list);
                            modal.appendChild(actions);
                            overlay.appendChild(modal);
                            document.body.appendChild(overlay);
                            btnClose.addEventListener('click', function () { overlay.remove(); });
                            overlay.addEventListener('click', function (e) { if (e.target === overlay) { overlay.remove(); } });
                            btnGo.addEventListener('click', function (e) {
                                e.preventDefault();
                                var mainInput = document.getElementById('mainInput');
                                var form = document.getElementById('stockForm');
                                if (mainInput && form) {
                                    mainInput.value = 'low';
                                    form.submit();
                                } else {
                                    window.location.href = '{{ route('kp.daftar_stok', ['main' => 'low']) }}';
                                }
                            });
                        });
                    }
                })();
            </script>
        @endif
    @endif
</body>

</html>