<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Export Laporan - CV Panca Indra Keemasan</title>
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

        .header-text h1 {
            font-family: var(--font-heading);
            font-size: 1.7rem;
            margin: 0;
        }

        .header-text p {
            margin: 0.3rem 0 0;
            color: var(--color-muted);
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
        }

        .filters {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1rem;
        }

        label {
            font-size: 0.85rem;
            font-weight: 500;
            display: block;
            margin-bottom: 0.35rem;
        }

        select,
        input {
            width: 100%;
            padding: 0.55rem 0.75rem;
            border-radius: 12px;
            border: 1px solid #d1d5db;
            background: #ffffff;
            font-size: 0.9rem;
        }

        select:disabled,
        input:disabled {
            background: #f9fafb;
            color: #6b7280;
        }

        .export-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.2rem;
        }

        .export-card {
            border-radius: var(--radius-lg);
            padding: 1.4rem 1.6rem;
            border: 1px solid transparent;
            display: flex;
            align-items: center;
            gap: 1rem;
            cursor: pointer;
            box-shadow: var(--shadow-soft);
            transition: transform 0.15s ease, box-shadow 0.15s ease;
            border: none;
            width: 100%;
            text-align: left;
            background: #eff6ff;
        }

        .export-card:focus-visible {
            outline: 3px solid rgba(15, 41, 107, 0.28);
            outline-offset: 2px;
        }

        .export-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 22px 45px rgba(15, 23, 42, 0.18);
        }

        .export-card__icon {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            font-size: 1.4rem;
            background: rgba(255, 255, 255, 0.28);
        }

        .export-card__text {
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
        }

        .export-card__text strong {
            font-family: var(--font-heading);
            font-size: 1rem;
        }

        .export-card__text span {
            font-size: 0.85rem;
            color: var(--color-muted);
        }

        .export-card--pdf {
            background: linear-gradient(135deg, rgba(248, 113, 113, 0.22), rgba(239, 68, 68, 0.28));
            border: 1px solid rgba(239, 68, 68, 0.25);
            color: #7f1d1d;
        }

        .export-card--excel {
            background: linear-gradient(135deg, rgba(74, 222, 128, 0.18), rgba(34, 197, 94, 0.26));
            border: 1px solid rgba(34, 197, 94, 0.24);
            color: #14532d;
        }

        .export-card--csv {
            background: linear-gradient(135deg, rgba(96, 165, 250, 0.2), rgba(59, 130, 246, 0.22));
            border: 1px solid rgba(59, 130, 246, 0.24);
            color: #1e3a8a;
        }

        .preview-card {
            background-color: var(--color-card);
            border-radius: var(--radius-lg);
            padding: 1.6rem 1.8rem 1.8rem;
            box-shadow: var(--shadow-soft);
            display: grid;
            gap: 1.1rem;
        }

        .preview-header {
            border-left: 3px solid var(--color-primary);
            padding-left: 1rem;
        }

        .preview-header h2 {
            font-family: var(--font-heading);
            font-size: 1.1rem;
            margin: 0 0 0.35rem;
        }

        .preview-header p {
            margin: 0;
            color: var(--color-muted);
            font-size: 0.85rem;
        }

        .preview-table {
            border-radius: 16px;
            border: 1px solid #d1d5db;
            overflow: hidden;
        }

        .preview-table table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.88rem;
        }

        .preview-table th,
        .preview-table td {
            padding: 0.7rem 1rem;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        .preview-table thead {
            background: #f8fafc;
        }

        .preview-table tbody tr:last-child td {
            border-bottom: none;
        }

        .modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.45);
            display: none;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            z-index: 1000;
        }

        .modal-backdrop.is-open {
            display: flex;
        }

        .modal {
            width: min(620px, 100%);
            background: #ffffff;
            border-radius: var(--radius-lg);
            box-shadow: 0 30px 60px rgba(15, 23, 42, 0.2);
            display: flex;
            flex-direction: column;
            max-height: 90vh;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.4rem 1.6rem 0.8rem;
        }

        .modal-title {
            font-family: var(--font-heading);
            font-size: 1.2rem;
            margin: 0;
        }

        .modal-close {
            border: none;
            background: transparent;
            font-size: 1.4rem;
            cursor: pointer;
            color: var(--color-muted);
        }

        .modal-body {
            padding: 0 1.6rem 1.6rem;
            overflow-y: auto;
        }

        .modal-meta {
            font-size: 0.85rem;
            color: var(--color-muted);
            margin: 0 0 1rem;
        }

        .modal-preview {
            border-radius: var(--radius-lg);
            border: 1px solid #e5e7eb;
            padding: 1rem;
            background: #f8fafc;
        }

        .modal-actions {
            padding: 1rem 1.6rem 1.4rem;
            display: flex;
            justify-content: flex-end;
            gap: 0.8rem;
            border-top: 1px solid #e5e7eb;
        }

        .btn-secondary {
            padding: 0.55rem 1.2rem;
            border-radius: 999px;
            border: 1px solid #d1d5db;
            background: #ffffff;
            color: var(--color-dark);
            font-size: 0.9rem;
            cursor: pointer;
        }

        .btn-secondary:hover {
            background: #f3f4f6;
        }

        body.modal-open {
            overflow: hidden;
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
            <div class="header-bar">
                <div class="header-text">
                    <h1>Export Laporan</h1>
                    <p>Pilih jenis laporan dan rentang tanggal yang ingin diunduh. Tampilan ini masih statis sebagai
                        mockup.</p>
                </div>
                <button class="btn-logout" type="button">Logout</button>
            </div>
            <form id="logoutForm" method="post" action="{{ route('kp.logout') }}" style="display:none;">@csrf</form>
            <form id="exportForm" action="{{ route('kp.export.process') }}" method="GET" target="_blank"
                style="display:none;">
                <input type="hidden" name="type" id="form-type">
                <input type="hidden" name="start" id="form-start">
                <input type="hidden" name="end" id="form-end">
                <input type="hidden" name="format" id="form-format">
                <input type="hidden" name="item_id" id="form-item-id">
            </form>

            <section class="card">
                <div class="filters">
                    <div>
                        <label for="report-type">Jenis Laporan</label>
                        <select id="report-type">
                            <option value="stok-akhir">Laporan Stok Akhir</option>
                            <option value="riwayat-stok">Laporan Riwayat Stok</option>
                            <option value="barang-menipis">Laporan Barang Menipis</option>
                            <option value="per-barang">Laporan Per Barang (Detail)</option>
                        </select>
                    </div>
                    <div id="item-selector-wrapper" style="display:none;">
                        <label for="item-id">Pilih Barang</label>
                        <select id="item-id">
                            <option value="">-- Pilih Barang --</option>
                            @foreach(\App\Models\Item::orderBy('name')->get() as $it)
                                <option value="{{ $it->id }}">{{ $it->name }} ({{ $it->code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="range-start">Rentang Tanggal Mulai</label>
                        <input id="range-start" type="date" value="{{ date('Y-m-01') }}" />
                    </div>
                    <div>
                        <label for="range-end">Rentang Tanggal Selesai</label>
                        <input id="range-end" type="date" value="{{ date('Y-m-d') }}" />
                    </div>
                </div>
            </section>

            <section class="export-actions">
                <button class="export-card export-card--pdf" data-format="pdf" type="button">
                    <span class="export-card__icon">📄</span>
                    <div class="export-card__text">
                        <strong>PDF</strong>
                        <span>Unduh laporan dalam format PDF yang siap cetak.</span>
                    </div>
                </button>
                <button class="export-card export-card--excel" data-format="excel" type="button">
                    <span class="export-card__icon">📊</span>
                    <div class="export-card__text">
                        <strong>Excel</strong>
                        <span>Export ke Excel untuk analisis lanjutan.</span>
                    </div>
                </button>
                <button class="export-card export-card--csv" data-format="csv" type="button">
                    <span class="export-card__icon">🧾</span>
                    <div class="export-card__text">
                        <strong>CSV (Sync App 2 & 3)</strong>
                        <span>Format CSV standar untuk Flashdisk (Gunakan untuk Kasir & Monitoring).</span>
                    </div>
                </button>
            </section>

            <section class="preview-card">
                <div class="preview-header">
                    <h2>Preview Sampel</h2>
                    <p>[Logo Perusahaan] · PT. Maju Jaya Kemasan · Laporan Stok Akhir · Tanggal Cetak: 28 Nov 2025</p>
                </div>
                <div class="preview-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Nama Barang</th>
                                <th>Stok Awal</th>
                                <th>Masuk</th>
                                <th>Keluar</th>
                                <th>Stok Akhir</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Cup Plastik</td>
                                <td>5.000</td>
                                <td>300</td>
                                <td>250</td>
                                <td>5.050</td>
                            </tr>
                            <tr>
                                <td>Tutup Cup</td>
                                <td>2.000</td>
                                <td>500</td>
                                <td>120</td>
                                <td>2.380</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
    <div class="modal-backdrop" id="export-confirm-modal" role="dialog" aria-modal="true" aria-hidden="true">
        <div class="modal">
            <div class="modal-header">
                <h2 class="modal-title">Konfirmasi Export</h2>
                <button class="modal-close" type="button" aria-label="Tutup">&times;</button>
            </div>
            <div class="modal-body">
                <p class="modal-meta"></p>
                <div class="modal-preview"></div>
            </div>
            <div class="modal-actions">
                <button class="btn-secondary" type="button" data-dismiss="modal">Batal</button>
                <button class="btn-primary" type="button" data-confirm="modal">Konfirmasi &amp; Unduh</button>
            </div>
        </div>
    </div>
    <script>
        (function () {
            const reportTypeEl = document.getElementById('report-type');
            const itemSelectorWrapper = document.getElementById('item-selector-wrapper');
            const itemIdEl = document.getElementById('item-id');
            const startDateEl = document.getElementById('range-start');
            const endDateEl = document.getElementById('range-end');
            const exportButtons = document.querySelectorAll('.export-card');
            const modal = document.getElementById('export-confirm-modal');
            const modalTitle = modal.querySelector('.modal-title');
            const modalMeta = modal.querySelector('.modal-meta');
            const modalClose = modal.querySelector('.modal-close');
            const modalCancel = modal.querySelector('[data-dismiss="modal"]');
            const modalConfirm = modal.querySelector('[data-confirm="modal"]');
            let pendingExport = null;

            reportTypeEl.addEventListener('change', () => {
                if (reportTypeEl.value === 'per-barang') {
                    itemSelectorWrapper.style.display = 'block';
                } else {
                    itemSelectorWrapper.style.display = 'none';
                }
            });

            const formatLabels = {
                pdf: 'PDF',
                excel: 'Excel',
                csv: 'CSV'
            };

            const reportLabels = {
                'stok-akhir': 'Laporan Stok Akhir',
                'riwayat-stok': 'Laporan Riwayat Stok',
                'barang-menipis': 'Laporan Barang Menipis',
                'per-barang': 'Laporan Per Barang (Detail)'
            };

            function openModal(payload) {
                pendingExport = payload;
                modalTitle.textContent = `Konfirmasi Export ${formatLabels[payload.format]}`;
                let metaText = `Jenis Laporan: ${reportLabels[payload.reportKey]} • Periode ${payload.startDate || '-'} s/d ${payload.endDate || '-'}`;
                if (payload.reportKey === 'per-barang' && payload.itemName) {
                    metaText += ` • Barang: ${payload.itemName}`;
                }
                modalMeta.textContent = metaText;
                modal.classList.add('is-open');
                modal.setAttribute('aria-hidden', 'false');
                document.body.classList.add('modal-open');
                modalConfirm.focus();
            }

            function closeModal() {
                pendingExport = null;
                modal.classList.remove('is-open');
                modal.setAttribute('aria-hidden', 'true');
                document.body.classList.remove('modal-open');
            }

            exportButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const format = button.dataset.format;
                    const reportKey = reportTypeEl.value;
                    const startDate = startDateEl.value;
                    const endDate = endDateEl.value;
                    const itemId = itemIdEl.value;
                    const itemName = itemIdEl.options[itemIdEl.selectedIndex].text;

                    if (reportKey === 'per-barang' && !itemId) {
                        alert('Silakan pilih barang terlebih dahulu.');
                        return;
                    }

                    openModal({ format, reportKey, startDate, endDate, itemId, itemName });
                });
            });

            modalClose.addEventListener('click', closeModal);
            modalCancel.addEventListener('click', closeModal);
            modal.addEventListener('click', (event) => {
                if (event.target === modal) {
                    closeModal();
                }
            });

            modalConfirm.addEventListener('click', () => {
                if (!pendingExport) { return; }

                // Set form values
                document.getElementById('form-type').value = pendingExport.reportKey;
                document.getElementById('form-start').value = pendingExport.startDate;
                document.getElementById('form-end').value = pendingExport.endDate;
                document.getElementById('form-format').value = pendingExport.format;
                document.getElementById('form-item-id').value = pendingExport.itemId || '';

                // Submit form
                document.getElementById('exportForm').submit();

                closeModal();
            });
        })();
    </script>
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