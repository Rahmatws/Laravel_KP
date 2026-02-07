<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Riwayat Import Stok - CV Panca Indra Keemasan</title>
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
            display: flex;
            flex-direction: column;
            gap: 1.6rem;
            margin-left: 260px;
            width: calc(100% - 260px);
            min-height: 100vh;
        }

        .header-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
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

        .page-header {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .page-title {
            font-family: var(--font-heading);
            font-size: 1.7rem;
            margin: 0;
        }

        .section-subtext {
            font-size: 0.9rem;
            color: var(--color-muted);
            margin: 0;
        }

        .search-bar {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            border-radius: 999px;
            border: 1px solid #d1d5db;
            padding: 0.6rem 1.1rem;
            background: #ffffff;
            box-shadow: var(--shadow-soft);
            max-width: 420px;
        }

        .search-bar span {
            font-size: 1rem;
            color: var(--color-muted);
        }

        .search-bar input {
            border: none;
            outline: none;
            flex: 1;
            font-size: 0.9rem;
            background: transparent;
        }

        .card {
            background-color: var(--color-card);
            border-radius: var(--radius-lg);
            padding: 1.8rem 2rem 2rem;
            box-shadow: var(--shadow-soft);
            overflow-x: auto;
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
            white-space: nowrap;
        }

        .table tbody tr:last-child td {
            border-bottom: none;
        }

        .source-badge {
            display: inline-block;
            padding: 0.2rem 0.6rem;
            border-radius: 99px;
            background: #e0f2fe;
            color: #0369a1;
            font-size: 0.8rem;
            font-weight: 600;
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

            .search-bar {
                max-width: 100%;
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
                <div class="page-header">
                    <h1 class="page-title">Riwayat Import Stok</h1>
                    <p class="section-subtext">Catatan riwayat import data stok dari SID Retail Pro.</p>
                </div>
                <button class="btn-logout" type="button">Logout</button>
            </div>
            <form id="logoutForm" method="post" action="{{ route('kp.logout') }}" style="display:none;">@csrf</form>

            <div class="search-bar" style="display:none;">
                <span>🔍</span>
                <input type="search" placeholder="Cari..." disabled />
            </div>

            <form action="{{ route('kp.riwayat_stok') }}" method="get"
                style="display:flex;gap:1.5rem;flex-wrap:wrap;margin-bottom:0.5rem;">
                <div style="display:flex;flex-direction:column;gap:0.4rem;flex:1;min-width:200px;">
                    <label for="start_date" style="font-weight:600;font-size:0.95rem;color:#1e293b;">Rentang Tanggal
                        Mulai</label>
                    <div style="position:relative;">
                        <input type="date" id="start_date" name="start_date" value="{{ $startDate ?? '' }}"
                            style="width:100%;padding:0.75rem 1rem;border:1px solid #cbd5e1;border-radius:12px;font-family:inherit;font-size:0.95rem;color:#334155;outline:none;transition:all 0.2s;">
                    </div>
                </div>

                <div style="display:flex;flex-direction:column;gap:0.4rem;flex:1;min-width:200px;">
                    <label for="end_date" style="font-weight:600;font-size:0.95rem;color:#1e293b;">Rentang Tanggal
                        Selesai</label>
                    <div style="position:relative;">
                        <input type="date" id="end_date" name="end_date" value="{{ $endDate ?? '' }}"
                            style="width:100%;padding:0.75rem 1rem;border:1px solid #cbd5e1;border-radius:12px;font-family:inherit;font-size:0.95rem;color:#334155;outline:none;transition:all 0.2s;">
                    </div>
                </div>

                <div style="display:flex;align-items:end;padding-bottom:1px;">
                    <button type="submit"
                        style="padding:0.75rem 1.5rem;background:var(--color-primary);color:#fff;border:none;border-radius:12px;font-weight:500;cursor:pointer;font-size:0.95rem;height:46px;">Filter</button>
                    @if($startDate || $endDate)
                        <a href="{{ route('kp.riwayat_stok') }}"
                            style="margin-left:0.5rem;padding:0.75rem 1rem;background:#e2e8f0;color:#475569;border-radius:12px;font-weight:500;text-decoration:none;font-size:0.95rem;height:46px;display:flex;align-items:center;">Reset</a>
                    @endif
                </div>
            </form>

            <section class="card">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal Import</th>
                            <th>Kode Barang</th>
                            <th>Nama Barang</th>
                            <th>Jumlah Stok</th>
                            <th>Satuan</th>
                            <th>Sumber Data</th>
                            <th>User Import</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($changes as $index => $chg)
                            @php
                                $note = [];
                                try {
                                    $note = json_decode($chg->note, true) ?: [];
                                } catch (\Throwable $e) {
                                }
                                $absoluteStock = $note['absolute_stock'] ?? $chg->qty;
                                $unit = $note['unit'] ?? ($chg->item->unit ?? 'pcs');
                                $source = $note['source'] ?? 'SID Retail Pro';
                            @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ optional($chg->occurred_at)->format('d M Y, H:i') }}</td>
                                <td>{{ optional($chg->item)->code }}</td>
                                <td>{{ optional($chg->item)->name }}</td>
                                <td style="font-weight:600;color:#0f172a;">{{ number_format($absoluteStock) }}</td>
                                <td>{{ $unit }}</td>
                                <td><span class="source-badge">{{ $source }}</span></td>
                                <td>{{ optional($chg->user)->name }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align:center;padding:2rem;">Belum ada riwayat import stok.</td>
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
        })();
    </script>
</body>

</html>