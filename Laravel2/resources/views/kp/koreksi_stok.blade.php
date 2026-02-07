<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Koreksi Stok - CV Panca Indra Keemasan</title>
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

        .header-title {
            font-family: var(--font-heading);
            font-size: 1.4rem;
            font-weight: 600;
            margin: 0;
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
            margin: 0;
        }

        .section-subtext {
            font-size: 0.9rem;
            color: var(--color-muted);
            margin: 0;
        }

        .grid-koreksi {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 1.8rem;
        }

        .card {
            background-color: var(--color-card);
            border-radius: var(--radius-lg);
            padding: 1.8rem 2rem 2rem;
            box-shadow: var(--shadow-soft);
            display: flex;
            flex-direction: column;
            gap: 1.2rem;
        }

        .card h2 {
            font-family: var(--font-heading);
            font-size: 1.1rem;
            margin: 0;
        }

        .form-grid {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        label {
            font-size: 0.85rem;
            font-weight: 500;
        }

        select,
        input,
        textarea {
            width: 100%;
            padding: 0.55rem 0.75rem;
            border-radius: 12px;
            border: 1px solid #d1d5db;
            background: #ffffff;
            font-size: 0.9rem;
            font-family: var(--font-body);
        }

        select:disabled,
        input:disabled,
        textarea:disabled {
            background: #f9fafb;
            color: #6b7280;
        }

        textarea {
            resize: vertical;
            min-height: 90px;
        }

        .form-actions {
            display: flex;
            justify-content: flex-start;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--color-primary), var(--color-primary-dark));
            border: none;
            border-radius: 999px;
            padding: 0.6rem 1.6rem;
            font-size: 0.9rem;
            font-weight: 500;
            color: #ffffff;
            cursor: pointer;
            box-shadow: 0 12px 24px rgba(15, 41, 107, 0.18);
        }

        .btn-primary:hover {
            filter: brightness(1.05);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 0.8rem;
        }

        .card-header a {
            font-size: 0.85rem;
            color: var(--color-primary);
            font-weight: 500;
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
            padding: 0.7rem 0.9rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .table tbody tr:last-child td {
            border-bottom: none;
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

            .grid-koreksi {
                grid-template-columns: minmax(0, 1fr);
            }
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
                    <li><a href="{{ route('kp.koreksi_stok') }}" class="active">Koreksi Stok</a></li>
                @endif
                <li><a href="{{ route('kp.import') }}">Import CSV SID</a></li>
                <li><a href="{{ route('kp.import_penjualan') }}">Impor Penjualan App 2</a></li>
                <li><a href="{{ route('kp.kelola_notifikasi') }}">Kelola Notifikasi Stok</a></li>
                <li><a href="{{ route('kp.riwayat_stok') }}">Riwayat Import Stok</a></li>
                <li><a href="{{ route('kp.kategori_barang') }}">Kategori Barang</a></li>
                <li><a href="{{ route('kp.export_laporan') }}">Export Laporan</a></li>
            </ul>
        </aside>

        <main class="main">
            <div class="header-bar">
                <div>
                    <h1 class="page-title">Koreksi Stok</h1>
                    <p class="section-subtext">Formulir penyesuaian stok dan daftar koreksi terbaru.</p>
                </div>
                <button class="btn-logout" type="button">Logout</button>
            </div>
            <form id="logoutForm" method="post" action="{{ route('kp.logout') }}" style="display:none;">@csrf</form>

            <div class="grid-koreksi">
                <section class="card">
                    <h2>Tambah Koreksi Stok</h2>
                    <div class="form-grid">
                        <div>
                            <label for="item-name">Nama Barang</label>
                            <form id="correctionForm" method="post" action="{{ route('kp.koreksi_stok.post') }}">
                                @csrf
                                <select id="item-name" name="item_id" required>
                                    <option value="">Pilih barang</option>
                                    @foreach(($items ?? collect()) as $it)
                                        <option value="{{ $it->id }}" data-stock="{{ (int) $it->stock }}">{{ $it->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                        <div>
                            <label for="stock-current">Stok Saat Ini</label>
                            <input id="stock-current" type="text" value="" readonly />
                        </div>
                        <div>
                            <label for="stock-physical">Stok Fisik</label>
                            <input id="stock-physical" name="physical" type="number" min="0" value=""
                                form="correctionForm" required />
                        </div>
                        <div>
                            <label for="reason">Alasan Koreksi</label>
                            <textarea id="reason" name="reason" placeholder="Catatan koreksi"
                                form="correctionForm"></textarea>
                        </div>
                        <div>
                            <label for="date">Tanggal Koreksi</label>
                            <input id="date" name="date" type="date" value="{{ now()->toDateString() }}"
                                form="correctionForm" />
                        </div>
                        <div>
                            <label for="operator">Operator</label>
                            <select id="operator" name="user_id" form="correctionForm">
                                @foreach(($users ?? collect()) as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button class="btn-primary" type="submit" form="correctionForm">Tambah Koreksi</button>
                    </div>
                </section>

                <section class="card">
                    <div class="card-header">
                        <h2>Koreksi Stok Terbaru</h2>
                        <a href="#" role="button">Lihat Semua</a>
                    </div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nama Barang</th>
                                <th>Stok Awal</th>
                                <th>Stok Fisik</th>
                                <th>Selisih</th>
                                <th>Operator</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(($rows ?? collect()) as $r)
                                <tr>
                                    <td>{{ $r['item'] }}</td>
                                    <td>{{ number_format($r['before']) }}</td>
                                    <td>{{ number_format($r['after']) }}</td>
                                    <td>{{ $r['diff'] >= 0 ? '+' . number_format($r['diff']) : number_format($r['diff']) }}
                                    </td>
                                    <td>{{ $r['user'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">Belum ada koreksi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </section>
            </div>
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
            var select = document.getElementById('item-name');
            var curr = document.getElementById('stock-current');
            function updateStock() {
                var opt = select.options[select.selectedIndex];
                var s = opt ? opt.getAttribute('data-stock') : '';
                curr.value = s ? parseInt(s).toLocaleString('id-ID') : '';
            }
            select.addEventListener('change', updateStock);
            updateStock();
        })();
    </script>
</body>

</html>