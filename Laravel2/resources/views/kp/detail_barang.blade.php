<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Detail Barang - CV Panca Indra Keemasan</title>
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

        .detail-toolbar {
            display: flex;
            flex-wrap: wrap;
            gap: 0.6rem;
            align-items: center;
            margin-bottom: 0.9rem;
        }

        .detail-search {
            flex: 1 1 220px;
            display: flex;
            align-items: center;
            gap: 0.4rem;
            border-radius: 999px;
            border: 1px solid #e5e7eb;
            padding: 0.35rem 0.9rem;
            background: #f9fafb;
            font-size: 0.85rem;
        }

        .detail-search input {
            border: none;
            outline: none;
            background: transparent;
            width: 100%;
        }

        .filter-chip {
            border-radius: 999px;
            border: 1px solid #d1d5db;
            padding: 0.3rem 0.8rem;
            font-size: 0.8rem;
            background: #ffffff;
            cursor: default;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }

        .filter-chip--warning {
            border-color: #f59e0b33;
            background: #fffbeb;
            color: #92400e;
        }

        .filter-chip--danger {
            border-color: #ef444433;
            background: #fef2f2;
            color: #b91c1c;
        }

        .filter-chip--category {
            border-color: #0f296b33;
            background: #eff6ff;
            color: #1e3a8a;
        }

        .btn-import {
            margin-left: auto;
            border-radius: 999px;
            border: 1px solid var(--color-primary);
            padding: 0.4rem 1rem;
            font-size: 0.8rem;
            background: var(--color-primary);
            color: #ffffff;
            cursor: default;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }

        .btn-import span {
            font-size: 0.9rem;
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
            background: #fef3c7;
            color: #92400e;
        }

        .status-danger {
            background: #fee2e2;
            color: #b91c1c;
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
            @endif
        </aside>

        <main class="main">
            <div class="header-bar">
                <div class="header-title">Detail Barang</div>
                <button class="btn-logout" type="button">Logout</button>
            </div>
            <form id="logoutForm" method="post" action="{{ route('kp.logout') }}" style="display:none;">@csrf</form>

            <section class="card">
                <h1 class="section-heading">Data Stok Barang</h1>
                <p class="section-subtext">Tampilan data stok barang.</p>

                @if(session('import_count'))
                    <div
                        style="margin-bottom:1rem;padding:1rem 1.2rem;border-radius:12px;background:linear-gradient(135deg, #059669, #10b981);color:#fff;display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;box-shadow:0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);">
                        <div>
                            <div
                                style="font-weight:700;font-size:1.1rem;margin-bottom:0.4rem;display:flex;align-items:center;gap:0.5rem;">
                                <span
                                    style="background:rgba(255,255,255,0.2);padding:0.2rem 0.5rem;border-radius:6px;">📊</span>
                                Laporan Import
                            </div>
                            <div style="font-size:0.95rem;line-height:1.4;">
                                Hari ini Anda telah menambahkan <strong>{{ session('import_count') }}</strong> barang
                                @if(session('new_item_count') > 0)
                                    , termasuk <strong
                                        style="background:rgba(255,255,255,0.25);padding:0.1rem 0.4rem;border-radius:4px;">{{ session('new_item_count') }}
                                        barang baru</strong>
                                @endif.
                            </div>
                        </div>
                        <a href="{{ route('kp.detail_barang') }}"
                            style="background:#fff;color:#059669;text-decoration:none;border-radius:999px;padding:0.6rem 1.2rem;font-weight:700;font-size:0.9rem;box-shadow:0 2px 4px rgba(0,0,0,0.1);transition:transform 0.1s;"
                            onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                            Lihat Semua Data →
                        </a>
                    </div>
                @endif

                @if(auth()->check() && auth()->user()->has_imported && !auth()->user()->has_viewed_details && !session('import_count'))
                    <div
                        style="margin-bottom:1rem;padding:1rem 1.2rem;border-radius:12px;background:linear-gradient(135deg, #0f296b, #1e40af);color:#fff;display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;">
                        <div>
                            <div style="font-weight:600;font-size:1rem;margin-bottom:0.3rem;">✅ Import Berhasil!</div>
                            <div style="font-size:0.9rem;opacity:0.95;">Data CSV SID telah berhasil diimport. Silakan klik
                                tombol di samping untuk melanjutkan ke Dashboard.</div>
                        </div>
                        <form method="post" action="{{ route('kp.detail_barang.complete') }}" style="margin:0;">
                            @csrf
                            <button type="submit"
                                style="background:#fff;color:#0f296b;border:none;border-radius:999px;padding:0.65rem 1.5rem;font-weight:600;cursor:pointer;white-space:nowrap;box-shadow:0 4px 12px rgba(0,0,0,0.15);">
                                Lanjut ke Dashboard →
                            </button>
                        </form>
                    </div>
                @endif

                @if(isset($criticalCount) && $criticalCount > 0 && !session('import_count'))
                    <div id="criticalBanner"
                        style="margin-bottom:0.8rem;padding:0.6rem 0.9rem;border-radius:12px;background:#fee2e2;color:#b91c1c;cursor:pointer">
                        Terdapat {{ $criticalCount }} barang dengan stok di bawah minimum dan notifikasi aktif.
                    </div>
                @endif

                <form class="detail-toolbar" action="{{ route('kp.detail_barang') }}" method="get"
                    style="position:relative">
                    <div class="detail-search">
                        <span>🔍</span>
                        <input type="search" name="q" value="{{ $q ?? '' }}" placeholder="Cari kode atau nama barang" />
                    </div>
                    <button type="submit" name="status" value="low" class="filter-chip filter-chip--warning"
                        style="{{ ($status ?? '') === 'low' ? 'border:2px solid #b45309;background:#fef3c7;font-weight:700;box-shadow:0 0 0 2px #fcd34d;' : '' }}">
                        <span>⚠️</span><span>Stok menipis</span>
                    </button>
                    <button type="submit" name="status" value="zero" class="filter-chip filter-chip--danger"
                        style="{{ ($status ?? '') === 'zero' ? 'border:2px solid #b91c1c;background:#fee2e2;font-weight:700;box-shadow:0 0 0 2px #fca5a5;' : '' }}">
                        <span>❌</span><span>Stok habis</span>
                    </button>
                    @if(($status ?? '') !== '' || ($category ?? '') !== '')
                        <a href="{{ route('kp.detail_barang', ['q' => $q ?? '']) }}" class="filter-chip"
                            style="background:#f3f4f6;color:#4b5563;border-color:#d1d5db;text-decoration:none;font-weight:600;transition:all 0.2s;gap:0.4rem;">
                            <span style="font-size:1rem;line-height:1;">↺</span>
                            <span>Reset Filter</span>
                        </a>
                    @endif
                    <div class="filter-chip filter-chip--category {{ $category ? 'active' : '' }}"
                        id="btn-filter-kategori"
                        style="{{ $category ? 'background:#dbeafe;border-color:#2563eb;color:#1e40af;font-weight:600;' : '' }}">
                        <span>🗂️</span>
                        <span>{{ $category ?: 'Filter kategori' }}</span>
                    </div>
                    <input type="hidden" name="category" id="hidden-category" value="{{ $category ?? '' }}" />
                    <a class="btn-import" href="{{ route('kp.import') }}">
                        <span>📥</span>
                        <span>Import Excel / CSV (SID)</span>
                    </a>
                    <div id="kategori-dropdown"
                        style="display:none;position:absolute;top:100%;left:280px;z-index:10;background:#fff;border:1px solid #e5e7eb;border-radius:12px;box-shadow:0 18px 40px rgba(15,23,42,0.12);min-width:220px;padding:6px">
                        <div style="padding:8px 10px;color:#6b7280;font-size:0.85rem">Pilih Kategori</div>
                        <button type="button" class="dropdown-item" data-cat=""
                            style="display:block;width:100%;text-align:left;padding:8px 10px;border:none;background:#fff">Semua
                            Kategori</button>
                        @foreach($categories as $cat)
                            <button type="button" class="dropdown-item" data-cat="{{ $cat->name }}"
                                style="display:block;width:100%;text-align:left;padding:8px 10px;border:none;background:#fff">{{ $cat->name }}</button>
                        @endforeach
                    </div>
                </form>

                <table class="table">
                    <thead>
                        <tr>
                            <th>Kode Barang</th>
                            <th>Nama Barang</th>
                            <th>Harga Beli</th>
                            <th>Harga Jual</th>
                            <th>Stok</th>
                            <th>Minimum</th>
                            <th>Satuan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                        @php($catName = $item->category->name ?? null)
                        @php($catDefault = ((isset($mode) && $mode === 'category') && $catName !== null && isset($categoryDefaults[$catName])) ? (int) $categoryDefaults[$catName] : ($defaultMin ?? 0))
                        @php($threshold = (isset($mode) && $mode === 'global') ? ($defaultMin ?? 0) : ((isset($mode) && $mode === 'category') ? max(($item->min_stock ?? 0), $catDefault) : max(($item->min_stock ?? 0), ($defaultMin ?? 0))))
                        @php($status = $item->stock <= 0 ? 'habis' : ($item->stock < $threshold ? 'menipis' : 'aman'))
                        <tr>
                            <td>{{ $item->code }}</td>
                            <td>{{ $item->name }}</td>
                            <td>Rp {{ number_format($item->purchase_price) }}</td>
                            <td>Rp {{ number_format($item->sale_price) }}</td>
                            <td>{{ number_format($item->stock) }}</td>
                            <td>{{ number_format($threshold) }}</td>
                            <td>{{ $item->unit }}</td>
                            <td>
                                @if($status === 'habis')
                                    <span class="status-pill status-danger">❌ Habis</span>
                                @elseif($status === 'menipis')
                                    <span class="status-pill status-warning">⚠️ Menipis</span>
                                @else
                                    <span class="status-pill" style="background:#dcfce7;color:#166534;">Aman</span>
                                @endif
                            </td>
                            <td style="font-size: 0.8rem; display: flex; gap: 0.5rem;">
                                <a href="{{ route('kp.edit_detail_barang', ['id' => $item->id]) }}"
                                    style="color:#0f296b; font-weight:600; text-decoration:none;">Edit</a>
                                <span style="color:#e5e7eb">|</span>
                                <a href="{{ route('kp.riwayat_stok') }}"
                                    style="color:#6b7280; text-decoration:none;">Riwayat</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9">Belum ada data barang.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
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
            var btnCat = document.getElementById('btn-filter-kategori');
            var dropdown = document.getElementById('kategori-dropdown');
            var hiddenCat = document.getElementById('hidden-category');
            if (btnCat && dropdown) {
                btnCat.addEventListener('click', function () {
                    dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
                });
            }
            document.querySelectorAll('#kategori-dropdown .dropdown-item').forEach(function (item) {
                item.addEventListener('click', function () {
                    hiddenCat.value = this.getAttribute('data-cat') || '';
                    var form = document.querySelector('.detail-toolbar');
                    if (form) form.submit();
                });
            });
        })();
    </script>
    @if(($notifOn ?? 1) === 1 && ($criticalCount ?? 0) > 0)
        <script>         (function () {
                var shown = sessionStorage.getItem('popup_stock_detail_shown'); if (!shown) {
                    var overlay = document.createElement('div'); overlay.style.position = 'fixed'; overlay.style.inset = '0'; overlay.style.background = 'rgba(0,0,0,0.35)'; overlay.style.display = 'flex'; overlay.style.alignItems = 'center'; overlay.style.justifyContent = 'center'; overlay.style.zIndex = '9999'; var modal = document.createElement('div'); modal.style.background = '#fff'; modal.style.borderRadius = '16px'; modal.style.boxShadow = '0 18px 40px rgba(15, 23, 42, 0.18)'; modal.style.maxWidth = '560px'; modal.style.width = '92%'; modal.style.padding = '1rem 1.2rem'; var title = document.createElement('div'); title.style.fontWeight = '600'; title.style.fontSize = '1rem'; title.textContent = 'Notifikasi Stok Menipis'; var desc = document.createElement('div'); desc.style.color = '#374151'; desc.style.margin = '0.4rem 0 0.8rem'; desc.textContent = 'Terdapat {{ $criticalCount }} barang di bawah batas minimum.'; var list = document.createElement('div'); list.style.maxHeight = '220px'; list.style.overflow = 'auto'; list.style.border = '1px solid #e5e7eb'; list.style.borderRadius = '12px'; list.style.padding = '0.4rem 0.6rem'; list.innerHTML = `{!! collect($criticalItems ?? [])->map(function ($i) {
            return '<div style="display:flex;justify-content:space-between;padding:6px 2px;border-bottom:1px solid #f3f4f6"><span>' . e($i->code) . ' • ' . e($i->name) . '</span><span style="font-weight:600;color:#b91c1c">' . number_format($i->stock) . '</span></div>';
        })->implode('') !!}`; var actions = document.createElement('div'); actions.style.display = 'flex'; actions.style.justifyContent = 'flex-end'; actions.style.gap = '0.6rem'; actions.style.marginTop = '0.8rem'; var btnClose = document.createElement('button'); btnClose.textContent = 'Tutup'; btnClose.style.border = '1px solid #d1d5db'; btnClose.style.borderRadius = '999px'; btnClose.style.padding = '0.5rem 1rem'; btnClose.style.background = '#fff'; var btnGo = document.createElement('a'); btnGo.textContent = 'Lihat Stok Menipis'; btnGo.href = '{{ route('kp.daftar_stok', ['main' => 'low']) }}'; btnGo.style.background = '#2563eb'; btnGo.style.color = '#fff'; btnGo.style.borderRadius = '999px'; btnGo.style.padding = '0.5rem 1rem'; actions.appendChild(btnClose); actions.appendChild(btnGo); modal.appendChild(title); modal.appendChild(desc); modal.appendChild(list); modal.appendChild(actions); overlay.appendChild(modal); document.body.appendChild(overlay); btnClose.addEventListener('click', function () { overlay.remove(); sessionStorage.setItem('popup_stock_detail_shown', '1'); }); overlay.addEventListener('click', function (e) { if (e.target === overlay) { overlay.remove(); sessionStorage.setItem('popup_stock_detail_shown', '1'); } }); btnGo.addEventListener('click', function (e) { e.preventDefault(); sessionStorage.setItem('popup_stock_detail_shown', '1'); window.location.href = '{{ route('kp.daftar_stok', ['main' => 'low']) }}'; });
                }
            })();
        </script>
        @if(($notifOn ?? 1) === 1 && ($criticalCount ?? 0) > 0)
            <script>         (function () {
                    var banner = document.getElementById('criticalBanner'); if (banner) {
                        banner.addEventListener('click', function () {
                            var overlay = document.createElement('div'); overlay.style.position = 'fixed'; overlay.style.inset = '0'; overlay.style.background = 'rgba(0,0,0,0.35)'; overlay.style.display = 'flex'; overlay.style.alignItems = 'center'; overlay.style.justifyContent = 'center'; overlay.style.zIndex = '9999'; var modal = document.createElement('div'); modal.style.background = '#fff'; modal.style.borderRadius = '16px'; modal.style.boxShadow = '0 18px 40px rgba(15, 23, 42, 0.18)'; modal.style.maxWidth = '560px'; modal.style.width = '92%'; modal.style.padding = '1rem 1.2rem'; var title = document.createElement('div'); title.style.fontWeight = '600'; title.style.fontSize = '1rem'; title.textContent = 'Notifikasi Stok Menipis'; var desc = document.createElement('div'); desc.style.color = '#374151'; desc.style.margin = '0.4rem 0 0.8rem'; desc.textContent = 'Terdapat {{ $criticalCount }} barang di bawah batas minimum.'; var list = document.createElement('div'); list.style.maxHeight = '220px'; list.style.overflow = 'auto'; list.style.border = '1px solid #e5e7eb'; list.style.borderRadius = '12px'; list.style.padding = '0.4rem 0.6rem'; list.innerHTML = `{!! collect($criticalItems ?? [])->map(function ($i) {
                    return '<div style="display:flex;justify-content:space-between;padding:6px 2px;border-bottom:1px solid #f3f4f6"><span>' . e($i->code) . ' • ' . e($i->name) . '</span><span style="font-weight:600;color:#b91c1c">' . number_format($i->stock) . '</span></div>';
                })->implode('') !!}`; var actions = document.createElement('div'); actions.style.display = 'flex'; actions.style.justifyContent = 'flex-end'; actions.style.gap = '0.6rem'; actions.style.marginTop = '0.8rem'; var btnClose = document.createElement('button'); btnClose.textContent = 'Tutup'; btnClose.style.border = '1px solid #d1d5db'; btnClose.style.borderRadius = '999px'; btnClose.style.padding = '0.5rem 1rem'; btnClose.style.background = '#fff'; var btnGo = document.createElement('a'); btnGo.textContent = 'Lihat Stok Menipis'; btnGo.href = '{{ route('kp.daftar_stok', ['main' => 'low']) }}'; btnGo.style.background = '#2563eb'; btnGo.style.color = '#fff'; btnGo.style.borderRadius = '999px'; btnGo.style.padding = '0.5rem 1rem'; actions.appendChild(btnClose); actions.appendChild(btnGo); modal.appendChild(title); modal.appendChild(desc); modal.appendChild(list); modal.appendChild(actions); overlay.appendChild(modal); document.body.appendChild(overlay); btnClose.addEventListener('click', function () { overlay.remove(); }); overlay.addEventListener('click', function (e) { if (e.target === overlay) { overlay.remove(); } }); btnGo.addEventListener('click', function (e) { e.preventDefault(); window.location.href = '{{ route('kp.daftar_stok', ['main' => 'low']) }}'; });
                        });
                    }
                })();
            </script>
        @endif
    @endif
</body>

</html>