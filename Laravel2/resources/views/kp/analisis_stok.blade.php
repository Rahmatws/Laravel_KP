<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Analisis Stok - CV Panca Indra Keemasan</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
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
            gap: 1.8rem;
            background: #f3f4f6;
            margin-left: 260px;
            width: calc(100% - 260px);
            min-height: 100vh;
        }

        /* Original Analysis Styles (kept for page components) */
        .page-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            color: white;
            padding: 1.5rem 2rem;
            border-radius: 16px;
            margin-bottom: 1rem;
            /* Reduced from 2rem */
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.2);
        }

        .page-title h1 {
            font-family: var(--font-heading);
            /* Updated font */
            margin: 0;
            font-size: 1.5rem;
            font-weight: 700;
        }

        .page-subtitle {
            opacity: 0.9;
            font-size: 0.9rem;
            margin-top: 0.3rem;
        }

        .controls {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
            /* Reduced from 2rem */
        }

        .btn-filter,
        .tab-btn {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            border: 1px solid #d1d5db;
            background: white;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.2s;
            font-family: var(--font-body);
        }

        .btn-filter:hover,
        .tab-btn:hover {
            background: #f3f4f6;
        }

        .btn-filter.active,
        .tab-btn.active {
            background: #eff6ff;
            border-color: #3b82f6;
            color: #1d4ed8;
            font-weight: 600;
        }

        .chart-container {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            height: 500px;
            margin-bottom: 2rem;
        }

        .insights-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .insight-card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            border-left: 5px solid #e5e7eb;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .insight-card.danger {
            border-left-color: #ef4444;
            background: #fef2f2;
        }

        .insight-card.info {
            border-left-color: #3b82f6;
            background: #eff6ff;
        }

        .insight-card.warning {
            border-left-color: #f59e0b;
            background: #fffbeb;
        }

        .insight-title {
            font-size: 0.85rem;
            color: #6b7280;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
        }

        .insight-value {
            font-size: 1.25rem;
            font-weight: 700;
            color: #111827;
        }

        .insight-desc {
            font-size: 0.85rem;
            color: #4b5563;
            margin-top: 0.5rem;
        }
    </style>
</head>

<body>
    <div class="layout">
        <aside class="sidebar">
            <div class="sidebar-brand">CV Panca Indra Keemasan</div>
            <ul class="sidebar-nav">
                <li><a href="{{ route('kp.dashboard') }}" class="active">Dashboard</a></li>
                <!-- Analisis Stok hidden from sidebar -->
                <li><a href="{{ route('kp.detail_barang') }}">Detail Barang</a></li>
                <li><a href="{{ route('kp.daftar_stok') }}">Daftar Stok</a></li>
                @if(auth()->check() && auth()->user()->role === 'admin')
                    <li><a href="{{ route('kp.koreksi_stok') }}">Koreksi Stok</a></li>
                @endif
                <li><a href="{{ route('kp.import') }}">Import CSV SID</a></li>
                <li><a href="{{ route('kp.import_penjualan') }}">Impor Penjualan App 2</a></li>
                <li><a href="{{ route('kp.riwayat_stok') }}">Riwayat Import Stok</a></li>
                <li><a href="{{ route('kp.export_laporan') }}">Export Laporan</a></li>
            </ul>
        </aside>

        <main class="main">
            <!-- Header -->
            <div class="page-header">
                <div class="page-title">
                    <h1>Analisis Stok Barang</h1>
                    <div class="page-subtitle">Monitoring pergerakan stok real-time</div>
                </div>
                <div style="text-align:right">
                    <div style="font-size:1.5rem; font-weight:600" id="dashboard-time">{{ now()->format('H:i:s') }}
                    </div>
                    <div style="opacity:0.9">
                        <span id="dashboard-day">{{ now()->isoFormat('dddd') }}</span>,
                        <span id="dashboard-date">{{ now()->format('d F Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Controls -->
            <div class="controls" style="justify-content: space-between;">
                <div style="display:flex; gap:0.5rem;">
                    <button class="tab-btn active" data-target="batang" onclick="switchTab('batang')">Grafik
                        Batang</button>
                    <button class="tab-btn" data-target="garis" onclick="switchTab('garis')">Grafik Garis</button>
                </div>
                <div style="display:flex; gap:0.5rem;">
                    <a href="?days=7" class="btn-filter {{ $days == 7 ? 'active' : '' }}">7 Hari</a>
                    <a href="?days=30" class="btn-filter {{ $days == 30 ? 'active' : '' }}">30 Hari</a>
                    <a href="?days=90" class="btn-filter {{ $days == 90 ? 'active' : '' }}">3 Bulan</a>
                </div>
            </div>

            <!-- Chart Area -->
            <div class="chart-container" style="display:flex; flex-direction:column; height:auto; min-height:500px;">
                <div style="flex:1; position:relative; min-height:400px;">
                    <canvas id="analysisChart"></canvas>
                </div>
                <div id="pagination-controls"
                    style="display:flex; justify-content:center; gap:1rem; margin-top:1rem; align-items:center;">
                    <button class="tab-btn" onclick="changePage(-1)" id="btn-prev">Previous</button>
                    <span id="page-info" style="font-size:0.9rem; font-weight:600; color:#4b5563;">Page 1 / 1</span>
                    <button class="tab-btn" onclick="changePage(1)" id="btn-next">Next</button>
                </div>
            </div>

            <!-- Insights -->
            <div class="insights-grid">
                <!-- Card 1: Penurunan Tertinggi -->
                <div class="insight-card danger">
                    <div class="insight-title">Penurunan Tertinggi</div>
                    @if($biggestDropItem)
                        <div class="insight-value">{{ $biggestDropItem['name'] }}</div>
                        <div class="insight-desc">Berkurang {{ number_format($biggestDropItem['total']) }} unit</div>
                    @else
                        <div class="insight-value">-</div>
                        <div class="insight-desc">Tidak ada data keluar</div>
                    @endif
                </div>

                <!-- Card 2: Rata-rata Penurunan -->
                <div class="insight-card info">
                    <div class="insight-title">Rata-rata Penurunan</div>
                    <div class="insight-value">{{ number_format($avgDecline) }} unit</div>
                    <div class="insight-desc">Per hari (semua kategori)</div>
                </div>

                <!-- Card 3: Perlu Restock -->
                <div class="insight-card warning">
                    <div class="insight-title">Perlu Restock</div>
                    <div class="insight-value">{{ $restockCount }} Item</div>
                    <div class="insight-desc">Stok di bawah batas minimum</div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Data from Controller
        const barLabels = @json($chartBarLabels);
        const barStock = @json($chartBarStock);
        const barMin = @json($chartBarMin);

        const lineLabels = @json($labels);
        const lineDataRaw = @json($lineData);

        let myChart = null;
        let activeTab = 'batang';

        // Pagination State
        const itemsPerPage = 15;
        let currentPage = 1;
        const totalPages = Math.ceil(barLabels.length / itemsPerPage);

        function updatePaginationControls() {
            const controls = document.getElementById('pagination-controls');
            if (activeTab === 'batang') {
                controls.style.display = 'flex';
                document.getElementById('page-info').textContent = `Page ${currentPage} / ${totalPages}`;
                document.getElementById('btn-prev').disabled = currentPage === 1;
                document.getElementById('btn-next').disabled = currentPage === totalPages;
                document.getElementById('btn-prev').style.opacity = currentPage === 1 ? '0.5' : '1';
                document.getElementById('btn-next').style.opacity = currentPage === totalPages ? '0.5' : '1';
            } else {
                controls.style.display = 'none';
            }
        }

        function changePage(delta) {
            const newPage = currentPage + delta;
            if (newPage >= 1 && newPage <= totalPages) {
                currentPage = newPage;
                initChart();
            }
        }

        function initChart() {
            const ctx = document.getElementById('analysisChart').getContext('2d');
            if (myChart) myChart.destroy();

            if (activeTab === 'batang') {
                const start = (currentPage - 1) * itemsPerPage;
                const end = start + itemsPerPage;

                const slicedLabels = barLabels.slice(start, end);
                const slicedStock = barStock.slice(start, end);
                const slicedMin = barMin.slice(start, end);

                myChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: slicedLabels,
                        datasets: [
                            {
                                label: 'Stok Aktual',
                                data: slicedStock,
                                backgroundColor: '#3b82f6',
                                borderRadius: 4,
                                minBarLength: 5
                            },
                            {
                                label: 'Minimum Stok',
                                data: slicedMin,
                                backgroundColor: '#ef4444',
                                borderRadius: 4
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        indexAxis: 'y', // Horizontal bars as preferred for readability
                        plugins: {
                            title: { display: true, text: `Jumlah Stok Aktual vs Minimum (Hal ${currentPage}/${totalPages})` },
                        },
                        scales: { x: { beginAtZero: true } }
                    }
                });
            } else {
                const datasets = lineDataRaw.map(item => ({
                    label: item.name,
                    data: item.data,
                    borderColor: item.borderColor,
                    tension: 0.3,
                    fill: false
                }));

                myChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: lineLabels,
                        datasets: datasets
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: { display: true, text: 'Tren Pergerakan Stok' },
                            legend: { position: 'bottom' }
                        },
                        scales: { y: { beginAtZero: true } }
                    }
                });
            }
        }

        function switchTab(tab) {
            activeTab = tab;
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelector(`[data-target="${tab}"]`).classList.add('active');
            initChart();
        }

        // Real-time Clock
        function updateDashboardClock() {
            var now = new Date();
            var days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            var months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

            var dayName = days[now.getDay()];
            var dateStr = now.getDate() + ' ' + months[now.getMonth()] + ' ' + now.getFullYear();

            var hh = String(now.getHours()).padStart(2, '0');
            var mm = String(now.getMinutes()).padStart(2, '0');
            var ss = String(now.getSeconds()).padStart(2, '0');
            var timeStr = hh + ':' + mm + ':' + ss;

            var dayEl = document.getElementById('dashboard-day');
            var dateEl = document.getElementById('dashboard-date');
            var timeEl = document.getElementById('dashboard-time');

            if (dayEl) dayEl.textContent = dayName;
            if (dateEl) dateEl.textContent = dateStr;
            if (timeEl) timeEl.textContent = timeStr;
        }

        // Init on load
        initChart();
        updateDashboardClock();
        setInterval(updateDashboardClock, 1000);
    </script>
</body>

</html>