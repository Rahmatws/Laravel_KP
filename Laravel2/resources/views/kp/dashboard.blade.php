<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Admin - CV Panca Indra Keemasan</title>
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

        /* Shell baru dashboard stok */
        .dashboard-shell {
            width: 100%;
            max-width: 1040px;
            margin: 0 auto;
        }

        .stock-header {
            background: #0f296b;
            color: #ffffff;
            border-radius: 18px;
            padding: 1.1rem 1.6rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.2rem;
            margin-bottom: 1.4rem;
        }

        .stock-header-left {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .stock-header-middle {
            flex: 1;
            display: flex;
            justify-content: center;
        }

        .datetime-panel {
            display: flex;
            align-items: center;
            gap: 1.2rem;
            padding: 0.45rem 1.4rem;
            border-radius: 999px;
            background: rgba(15, 23, 42, 0.18);
            border: 1px solid rgba(255, 255, 255, 0.16);
            font-size: 0.85rem;
        }

        .datetime-group {
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .datetime-label {
            opacity: 0.9;
        }

        .datetime-value {
            font-weight: 600;
        }

        .stock-logo {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.12);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
        }

        .stock-system-name {
            font-family: var(--font-heading);
            font-size: 1.1rem;
            font-weight: 600;
        }

        .stock-subtitle {
            font-size: 0.85rem;
            opacity: 0.9;
        }

        .stock-header-right {
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .icon-btn {
            width: 34px;
            height: 34px;
            border-radius: 999px;
            border: none;
            background: rgba(255, 255, 255, 0.16);
            color: #ffffff;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }

        .icon-btn:hover {
            background: rgba(255, 255, 255, 0.28);
        }

        .user-pill {
            display: flex;
            align-items: center;
            gap: 0.55rem;
            background: rgba(255, 255, 255, 0.16);
            color: #ffffff;
            padding: 0.35rem 0.6rem;
            border-radius: 999px;
        }

        .user-avatar {
            width: 28px;
            height: 28px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.24);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }

        .user-text {
            display: flex;
            flex-direction: column;
            line-height: 1.1;
        }

        .user-name {
            font-weight: 600;
            font-size: 0.85rem;
        }

        .user-role {
            font-size: 0.75rem;
            opacity: 0.92;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
            gap: 1rem;
            margin-bottom: 1.4rem;
        }

        .summary-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 1rem 1.2rem;
            display: flex;
            align-items: center;
            gap: 0.8rem;
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.06);
        }

        .summary-icon {
            width: 38px;
            height: 38px;
            border-radius: 999px;
            background: #0f296b0d;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .summary-label {
            font-size: 0.85rem;
            color: #6b7280;
        }

        .summary-value {
            font-size: 1.3rem;
            font-weight: 600;
            margin-top: 0.1rem;
        }

        .summary-extra {
            font-size: 0.8rem;
            color: #6b7280;
        }

        .chart-and-notif {
            display: grid;
            grid-template-columns: minmax(0, 2.1fr) minmax(0, 1.2fr);
            gap: 1rem;
            margin-bottom: 1.4rem;
        }

        .card-flat {
            background: #ffffff;
            border-radius: 16px;
            padding: 1.2rem 1.4rem;
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.06);
        }

        .section-heading {
            font-size: 0.98rem;
            font-weight: 600;
            margin: 0 0 0.2rem;
        }

        .section-subtext {
            font-size: 0.8rem;
            color: #6b7280;
            margin-bottom: 0.8rem;
        }

        .fake-chart {
            height: 180px;
            border-radius: 12px;
            background: linear-gradient(180deg, #e5edff, #f9fafb);
            position: relative;
            overflow: hidden;
        }

        .fake-chart-line {
            position: absolute;
            inset: auto 6% 18% 4%;
            border-bottom: 2px solid #2563eb;
            border-radius: 999px;
        }

        .notif-list {
            list-style: none;
            padding: 0;
            margin: 0 0 1rem;
            font-size: 0.88rem;
            color: #374151;
        }

        .notif-list li {
            display: flex;
            align-items: flex-start;
            gap: 0.45rem;
            margin-bottom: 0.4rem;
        }

        .notif-icon {
            color: #dc2626;
        }

        .btn-secondary {
            border-radius: 999px;
            border: 1px solid #d1d5db;
            padding: 0.5rem 1.2rem;
            font-size: 0.85rem;
            background: #f9fafb;
            cursor: pointer;
        }

        .btn-secondary:hover {
            background: #e5e7eb;
        }

        .chart-tab {
            border-radius: 999px;
            border: 1px solid #d1d5db;
            padding: 0.35rem 0.9rem;
            font-size: 0.85rem;
            background: #fff;
            cursor: pointer;
        }

        .chart-tab.active {
            background: #eff6ff;
            border-color: #93c5fd;
            color: #1e3a8a;
        }

        .critical-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 1.2rem 1.4rem 1.1rem;
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.06);
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

        .btn-detail {
            border-radius: 999px;
            border: 1px solid #d1d5db;
            padding: 0.25rem 0.9rem;
            font-size: 0.8rem;
            background: #ffffff;
            cursor: pointer;
        }

        .btn-detail:hover {
            background: #f3f4f6;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.4rem;
        }

        .topbar-title {
            font-family: var(--font-heading);
            font-weight: 600;
            font-size: 1.1rem;
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

        .grid-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1.2rem;
        }

        .card {
            background-color: var(--color-card);
            border-radius: var(--radius-lg);
            padding: 1.4rem 1.6rem;
            box-shadow: var(--shadow-soft);
        }

        .card-stat-title {
            font-size: 0.9rem;
            color: var(--color-muted);
            margin-bottom: 0.4rem;
        }

        .card-stat-value {
            font-size: 1.5rem;
            font-weight: 600;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.2rem 0.6rem;
            border-radius: 999px;
            background: rgba(0, 0, 0, 0.06);
            font-size: 0.75rem;
            color: var(--color-muted);
            gap: 0.25rem;
        }

        .section-title {
            font-size: 1rem;
            font-weight: 600;
            margin: 0 0 1rem;
        }

        .grid-two {
            display: grid;
            grid-template-columns: minmax(0, 2.1fr) minmax(0, 1.4fr);
            gap: 1.4rem;
            align-items: flex-start;
        }

        .btn-outline {
            padding: 0.7rem 1rem;
            border-radius: 10px;
            border: 1px solid #ddd;
            background: #fafafa;
            font-size: 0.9rem;
            cursor: pointer;
            text-align: left;
            width: 100%;
            margin-bottom: 0.6rem;
        }

        .btn-outline:hover {
            background: #f0f0f0;
        }

        /* Modul Manajemen Stok */
        .stock-module-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.8rem;
        }

        .stock-module-list {
            list-style: none;
            padding: 0;
            margin: 0.4rem 0 0;
        }

        .stock-module-list li {
            padding: 0.55rem 0;
            border-bottom: 1px dashed #e0e0e0;
            font-size: 0.9rem;
        }

        .stock-module-list li:last-child {
            border-bottom: none;
        }

        .pill-critical {
            display: inline-flex;
            align-items: center;
            padding: 0.2rem 0.65rem;
            border-radius: 999px;
            background: rgba(220, 38, 38, 0.08);
            color: #b91c1c;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
        }

        .table th,
        .table td {
            padding: 0.55rem 0.4rem;
            text-align: left;
        }

        .table thead th {
            font-size: 0.8rem;
            color: var(--color-muted);
            border-bottom: 1px solid #e3e3e3;
        }

        .table tbody tr:nth-child(even) {
            background: #fafafa;
        }

        .tag-low {
            display: inline-flex;
            padding: 0.15rem 0.55rem;
            border-radius: 999px;
            font-size: 0.75rem;
            background: rgba(234, 179, 8, 0.16);
            color: #854d0e;
        }

        .tag-ok {
            display: inline-flex;
            padding: 0.15rem 0.55rem;
            border-radius: 999px;
            font-size: 0.75rem;
            background: rgba(22, 163, 74, 0.14);
            color: #166534;
        }

        .search-box {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.6rem;
        }

        .search-input {
            border-radius: 999px;
            border: 1px solid #ddd;
            padding: 0.4rem 0.8rem;
            font-size: 0.85rem;
            min-width: 160px;
        }

        .muted {
            color: var(--color-muted);
            font-size: 0.85rem;
        }

        /* Popup modal sederhana */
        .modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.32);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        .modal {
            background: #ffffff;
            border-radius: 18px;
            width: min(920px, 96%);
            padding: 1.6rem 1.8rem 1.4rem;
            box-shadow: 0 24px 70px rgba(0, 0, 0, 0.25);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.6rem;
        }

        .modal-title {
            font-family: var(--font-heading);
            font-size: 1.1rem;
            margin: 0;
        }

        .modal-close {
            border: none;
            background: transparent;
            font-size: 1.2rem;
            cursor: pointer;
        }

        .modal ul {
            padding-left: 1.2rem;
            margin: 0.4rem 0 0.2rem;
            font-size: 0.92rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--color-primary), var(--color-primary-dark));
            border: none;
            border-radius: 999px;
            padding: 0.6rem 1.2rem;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            color: #222;
        }

        .btn-primary:hover {
            filter: brightness(1.03);
        }

        /* Detail stok modal */
        .detail-toolbar {
            display: flex;
            flex-wrap: wrap;
            gap: 0.6rem;
            align-items: center;
            margin-bottom: 0.9rem;
        }

        .detail-search {
            flex: 1 1 200px;
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
            cursor: pointer;
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
            border: 1px solid #0f296b;
            padding: 0.4rem 1rem;
            font-size: 0.8rem;
            background: #0f296b;
            color: #ffffff;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }

        .btn-import span {
            font-size: 0.9rem;
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #ef4444;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            border: 2px solid #1e3a8a;
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

            .grid-two {
                grid-template-columns: minmax(0, 1fr);
            }
        }

        .fake-chart {
            background: #ffffff;
            border-radius: 12px;
            padding: 1rem;
            height: 200px;
            position: relative;
        }
    </style>
    <script>
        window.routeNames = {
            dashboard: "{{ route('kp.dashboard') }}",
            detail_barang: "{{ route('kp.detail_barang') }}",
            daftar_stok: "{{ route('kp.daftar_stok') }}",
            import: "{{ route('kp.import') }}",
            kelola_notifikasi: "{{ route('kp.kelola_notifikasi') }}",
            riwayat_stok: "{{ route('kp.riwayat_stok') }}",
            koreksi_stok: "{{ route('kp.koreksi_stok') }}",
            kategori_barang: "{{ route('kp.kategori_barang') }}",
            export_laporan: "{{ route('kp.export_laporan') }}",
        };

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

        document.addEventListener('DOMContentLoaded', function () {
            updateDashboardClock();
            setInterval(updateDashboardClock, 1000);
        });
    </script>
</head>

<body>
    <div class="layout">
        <aside class="sidebar">
            <div class="sidebar-brand">CV Panca Indra Keemasan</div>
            <ul class="sidebar-nav">
                <li><a href="{{ route('kp.dashboard') }}"
                        class="{{ request()->routeIs('kp.dashboard') ? 'active' : '' }}">Dashboard</a></li>
                <li><a href="{{ route('kp.detail_barang') }}">Detail Barang</a></li>
                <li><a href="{{ route('kp.daftar_stok') }}">Daftar Stok</a></li>
                @if(auth()->check() && auth()->user()->role === 'admin')
                    <li><a href="{{ route('kp.koreksi_stok') }}">Koreksi Stok</a></li>
                @endif
                <li><a href="{{ route('kp.import') }}">Import CSV SID</a></li>
                <li><a href="{{ route('kp.import_penjualan') }}">Impor Penjualan App 2</a></li>

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
            <div class="dashboard-shell">
                <header class="stock-header">
                    <div class="stock-header-left">
                        <div class="stock-logo">📦</div>
                        <div>
                            <div class="stock-system-name">Stock Monitoring Assistant</div>
                            <div class="stock-subtitle">Ringkasan kondisi stok hari ini</div>
                        </div>
                    </div>

                    <div class="stock-header-middle">
                        <div class="datetime-panel">
                            <div class="datetime-group">
                                <span class="datetime-label">📅</span>
                                <span class="datetime-value" id="dashboard-date">27 Desember 2025</span>
                            </div>
                            <div class="datetime-group">
                                <span class="datetime-label">📆</span>
                                <span class="datetime-value" id="dashboard-day">Sabtu</span>
                            </div>
                            <div class="datetime-group">
                                <span class="datetime-label">🕒</span>
                                <span class="datetime-value" id="dashboard-time">15:59:13</span>
                            </div>
                        </div>
                    </div>

                    <div class="stock-header-right">
                        <button class="icon-btn" type="button" id="notif-bell" style="position:relative">
                            🔔
                            @if(($criticalCount ?? 0) > 0)
                                <span class="notification-badge">{{ $criticalCount ?? 0 }}</span>
                            @endif
                        </button>
                        @php($user = Auth::user())
                        @if($user)
                            <div class="user-pill">
                                <div class="user-avatar">👤</div>
                                <div class="user-text">
                                    <div class="user-name">{{ $user->name }}</div>
                                    <div class="user-role">{{ ucfirst($user->role ?? 'user') }}</div>
                                </div>
                            </div>
                        @else
                            <div class="user-pill">
                                <div class="user-avatar">👤</div>
                                <div class="user-text">
                                    <div class="user-name">Tamu</div>
                                    <div class="user-role">Belum login</div>
                                </div>
                            </div>
                        @endif
                    </div>
                </header>

                <section class="summary-grid">
                    <article class="summary-card">
                        <div class="summary-icon">📦</div>
                        <div>
                            <div class="summary-label">Total Jenis Barang</div>
                            <div class="summary-value">{{ number_format($totalItems ?? 0) }} item</div>
                        </div>
                    </article>
                    <article class="summary-card">
                        <div class="summary-icon">⚠️</div>
                        <div>
                            <div class="summary-label">Barang Hampir Habis</div>
                            <div class="summary-value">{{ number_format($lowCount ?? 0) }} item</div>
                        </div>
                    </article>
                    <article class="summary-card">
                        <div class="summary-icon">❌</div>
                        <div>
                            <div class="summary-label">Barang Habis</div>
                            <div class="summary-value">{{ number_format($zeroCount ?? 0) }} item</div>
                        </div>
                    </article>
                    <article class="summary-card">
                        <div class="summary-icon">🔄</div>
                        <div>
                            <div class="summary-label">Update Stok Hari Ini</div>
                            <div class="summary-value">{{ number_format($transactionsToday ?? 0) }} transaksi</div>
                            <div class="summary-extra">Periode {{ (collect($labels ?? [])->first()) }} s/d
                                {{ (collect($labels ?? [])->last()) }}
                            </div>
                        </div>
                    </article>
                </section>

                <section class="chart-and-notif">
                    <article class="card-flat">
                        <div class="topbar">
                            <div>
                                <h2 class="section-heading" id="chart-title">Total Stok per Barang</h2>
                                <p class="section-subtext" id="chart-subtitle">Menampilkan stok untuk setiap nama barang
                                    (Paginated).</p>
                            </div>
                            <div style="display:flex;flex-direction:column;align-items:flex-end;gap:0.4rem">
                                <div style="display:flex;gap:0.4rem">
                                    <button class="chart-tab active" data-tab="bar" type="button">Batang</button>
                                    <button class="chart-tab" data-tab="line" type="button">Garis</button>
                                </div>
                                <div id="chart-pagination"
                                    style="display:flex;align-items:center;gap:0.5rem;font-size:0.8rem;">
                                    <button id="prev-chart"
                                        style="padding:0.2rem 0.5rem;border-radius:4px;border:1px solid #ddd;background:#fff;cursor:pointer;">&lt;</button>
                                    <span id="chart-page-info">1 / 1</span>
                                    <button id="next-chart"
                                        style="padding:0.2rem 0.5rem;border-radius:4px;border:1px solid #ddd;background:#fff;cursor:pointer;">&gt;</button>
                                </div>
                            </div>
                        </div>
                        <div class="fake-chart" style="position: relative;">
                            <a href="{{ route('kp.analisis_stok') }}"
                                style="display:block; width:100%; height:100%; text-decoration:none;">
                                <div
                                    style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 10; background: rgba(255,255,255,0.9); padding: 1rem 1.5rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; text-align: center;">
                                    <span
                                        style="display:block; font-weight:600; color:#0f296b; margin-bottom:0.25rem;">🔍
                                        Buka Analisis Stok Lengkap</span>
                                    <span style="font-size:0.8rem; color:#6b7280;">Klik untuk detail & filter</span>
                                </div>
                                <canvas id="mainChart" style="width: 100%; height: 100%; opacity: 0.5;"></canvas>
                            </a>
                        </div>
                    </article>

                    <article class="card-flat">
                        <h2 class="section-heading">Notifikasi Cepat</h2>
                        <p class="section-subtext">Peringatan stok menipis dan habis real-time.</p>
                        <ul class="notif-list">
                            @forelse(($criticalItems ?? collect())->take(3) as $i)
                            @php($catName = $i->category->name ?? null)
                            @php($catDefault = ((isset($mode) && $mode === 'category') && $catName !== null && isset($categoryDefaults[$catName])) ? (int) $categoryDefaults[$catName] : ($defaultMin ?? 0))
                            @php($threshold = (isset($mode) && $mode === 'global') ? ($defaultMin ?? 0) : ((isset($mode) && $mode === 'category') ? max(($i->min_stock ?? 0), $catDefault) : max(($i->min_stock ?? 0), ($defaultMin ?? 0))))
                            <li>
                                <span class="notif-icon">❗</span>
                                <span>{{ $i->name }} {{ ($i->stock ?? 0) <= 0 ? 'habis' : 'menipis' }}
                                    ({{ number_format($i->stock) }}
                                    < {{ number_format($threshold) }})
                                </span>
                            </li>
                            @empty
                            <li><span class="notif-icon">✔️</span><span>Tidak ada notifikasi kritis.</span></li>
                            @endforelse
                        </ul>
                        <a class="btn-secondary" href="{{ route('kp.kelola_notifikasi') }}">Lihat Semua Notifikasi</a>
                    </article>
                </section>

                <section class="critical-card">
                    <h2 class="section-heading">Stok Kritis Hari Ini</h2>
                    <p class="section-subtext">Daftar barang dengan stok di bawah batas minimum.</p>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nama Barang</th>
                                <th>Kode Barang</th>
                                <th>Stok Saat Ini</th>
                                <th>Batas Minimum</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(($criticalItems ?? collect()) as $i)
                            @php($catName = $i->category->name ?? null)
                            @php($catDefault = ((isset($mode) && $mode === 'category') && $catName !== null && isset($categoryDefaults[$catName])) ? (int) $categoryDefaults[$catName] : ($defaultMin ?? 0))
                            @php($threshold = (isset($mode) && $mode === 'global') ? ($defaultMin ?? 0) : ((isset($mode) && $mode === 'category') ? max(($i->min_stock ?? 0), $catDefault) : max(($i->min_stock ?? 0), ($defaultMin ?? 0))))
                            @php($status = ($i->stock ?? 0) <= 0 ? 'Habis' : 'Menipis')
                            <tr>
                                <td>{{ $i->name }}</td>
                                <td>{{ $i->code }}</td>
                                <td>{{ number_format($i->stock) }}</td>
                                <td>{{ number_format($threshold) }}</td>
                                <td>
                                    @if(($i->stock ?? 0) <= 0)
                                        <span class="status-pill status-danger">❌ Habis</span>
                                    @else
                                        <span class="status-pill status-warning">⚠️ Menipis</span>
                                    @endif
                                </td>
                                <td>
                                    <a class="btn-detail"
                                        href="{{ route('kp.detail_barang', ['q' => $i->code]) }}">Detail</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6">Tidak ada barang kritis.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </section>
            </div>
        </main>
    </div>

    <div class="modal-backdrop" id="detail-stock-modal">
        <div class="modal">
            <div class="modal-header">
                <h2 class="modal-title">Detail Stok Barang</h2>
                <button class="modal-close" type="button" aria-label="Tutup">&times;</button>
            </div>
            <p class="section-subtext" style="margin-bottom: 0.8rem;">Tampilan awal data stok barang. Saat ini masih
                statis, nanti bisa dihubungkan ke database dan import dari SID.</p>

            <div class="detail-toolbar">
                <div class="detail-search">
                    <span>🔍</span>
                    <input type="search" placeholder="Cari kode atau nama barang" disabled />
                </div>
                <button class="filter-chip filter-chip--warning" type="button" disabled>
                    <span>⚠️</span>
                    <span>Stok menipis</span>
                </button>
                <button class="filter-chip filter-chip--danger" type="button" disabled>
                    <span>❌</span>
                    <span>Stok habis</span>
                </button>
                <button class="filter-chip filter-chip--category" type="button" disabled>
                    <span>🗂️</span>
                    <span>Filter kategori</span>
                </button>
                <button class="btn-import" type="button" disabled>
                    <span>📥</span>
                    <span>Import Excel / CSV (SID)</span>
                </button>
            </div>

            <div class="card" style="box-shadow: none; padding: 0;">
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
                        <tr>
                            <td>P0001</td>
                            <td>Plastik Cup 16oz</td>
                            <td>Rp 350</td>
                            <td>Rp 500</td>
                            <td>900</td>
                            <td>1.000</td>
                            <td>pcs</td>
                            <td><span class="status-pill status-warning">⚠️ Menipis</span></td>
                            <td><span style="font-size: 0.8rem; color: #2563eb; cursor: default;">Edit</span> | <span
                                    style="font-size: 0.8rem; color: #6b7280; cursor: default;">Riwayat</span></td>
                        </tr>
                        <tr>
                            <td>P0002</td>
                            <td>Box Brown L</td>
                            <td>Rp 1.800</td>
                            <td>Rp 2.500</td>
                            <td>0</td>
                            <td>500</td>
                            <td>pcs</td>
                            <td><span class="status-pill status-danger">❌ Habis</span></td>
                            <td><span style="font-size: 0.8rem; color: #2563eb; cursor: default;">Edit</span> | <span
                                    style="font-size: 0.8rem; color: #6b7280; cursor: default;">Riwayat</span></td>
                        </tr>
                        <tr>
                            <td>P0003</td>
                            <td>Lid Sealer</td>
                            <td>Rp 400</td>
                            <td>Rp 650</td>
                            <td>250</td>
                            <td>500</td>
                            <td>pcs</td>
                            <td><span class="status-pill status-warning">⚠️ Menipis</span></td>
                            <td><span style="font-size: 0.8rem; color: #2563eb; cursor: default;">Edit</span> | <span
                                    style="font-size: 0.8rem; color: #6b7280; cursor: default;">Riwayat</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal-backdrop" id="notif-modal">
        <div class="modal">
            <div class="modal-header">
                <h2 class="modal-title">Notifikasi Stok ({{ ($criticalItems ?? collect())->count() }})</h2>
                <button class="modal-close" type="button" aria-label="Tutup">&times;</button>
            </div>
            <div class="card" style="box-shadow: none; padding: 0; max-height: 60vh; overflow-y: auto;">
                <table class="table">
                    <thead style="position: sticky; top: 0; background: white; z-index: 1;">
                        <tr>
                            <th>Barang</th>
                            <th>Stok / Min</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($criticalItems ?? collect()) as $i)
                        @php($catName = $i->category->name ?? null)
                        @php($catDefault = ((isset($mode) && $mode === 'category') && $catName !== null && isset($categoryDefaults[$catName])) ? (int) $categoryDefaults[$catName] : ($defaultMin ?? 0))
                        @php($threshold = (isset($mode) && $mode === 'global') ? ($defaultMin ?? 0) : ((isset($mode) && $mode === 'category') ? max(($i->min_stock ?? 0), $catDefault) : max(($i->min_stock ?? 0), ($defaultMin ?? 0))))
                        <tr>
                            <td>
                                <div style="font-weight:600;font-size:0.95rem">{{ $i->name }}</div>
                                <div style="font-size:0.8rem;color:#6b7280">{{ $i->code }}</div>
                            </td>
                            <td>
                                <span style="font-weight:600">{{ number_format($i->stock) }}</span>
                                <span style="font-size:0.85rem;color:#6b7280"> / {{ number_format($threshold) }}</span>
                            </td>
                            <td>
                                @if(($i->stock ?? 0) <= 0)
                                    <span class="status-pill status-danger">Habis</span>
                                @else
                                    <span class="status-pill status-warning">Menipis</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('kp.detail_barang', ['q' => $i->code]) }}" class="btn-detail"
                                    style="font-size:0.8rem;padding:0.3rem 0.8rem">Lihat</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" style="text-align:center;padding:2rem;color:#6b7280">
                                Tidak ada notifikasi baru. Stok aman! APDS
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Notification Modal Logic
        const notifBell = document.getElementById('notif-bell');
        const notifModal = document.getElementById('notif-modal');
        if (notifBell && notifModal) {
            notifBell.addEventListener('click', function () {
                notifModal.style.display = 'flex';
            });
            notifModal.querySelector('.modal-close').addEventListener('click', function () {
                notifModal.style.display = 'none';
            });
            notifModal.addEventListener('click', function (e) {
                if (e.target === notifModal) notifModal.style.display = 'none';
            });
        }


        detailButtons.forEach(function (btn) {
            btn.addEventListener('click', openDetailModal);
        });

        detailCloseBtn.addEventListener('click', closeDetailModal);
        detailModal.addEventListener('click', function (e) {
            if (e.target === detailModal) closeDetailModal();
        });
        const metrics = {
            bar: {
                labels: @json($chartBarLabels ?? []),
                values: @json($chartBarValues ?? []),
                page: {{ $barCurrentPage ?? 1 }},
                last: {{ $barLastPage ?? 1 }}
            },
            line: {
                data: @json($lineData ?? []),
                labels: @json($labels ?? []),
                page: {{ $lineCurrentPage ?? 1 }},
                last: {{ $lineLastPage ?? 1 }}
            }
        };

        let myChart = null;
        let activeChart = 'bar';

        function updatePaginationUI() {
            const info = document.getElementById('chart-page-info');
            const prev = document.getElementById('prev-chart');
            const next = document.getElementById('next-chart');
            const current = metrics[activeChart].page;
            const last = metrics[activeChart].last;

            info.textContent = `${current} / ${last}`;
            prev.disabled = current <= 1;
            next.disabled = current >= last;
            prev.style.opacity = prev.disabled ? '0.5' : '1';
            next.style.opacity = next.disabled ? '0.5' : '1';
        }

        async function fetchChartData(type, page) {
            const url = new URL(window.location.href);
            if (type === 'bar') url.searchParams.set('bar_page', page);
            else url.searchParams.set('line_page', page);

            try {
                const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                const data = await response.json();

                if (type === 'bar') {
                    metrics.bar.labels = data.bar.labels;
                    metrics.bar.values = data.bar.values;
                    metrics.bar.page = data.bar.current_page;
                    metrics.bar.last = data.bar.last_page;
                } else {
                    metrics.line.data = data.line.data;
                    metrics.line.labels = data.line.labels;
                    metrics.line.page = data.line.current_page;
                    metrics.line.last = data.line.last_page;
                }

                renderChart();
                updatePaginationUI();
            } catch (e) { console.error('Failed to fetch chart data', e); }
        }

        function renderChart() {
            const ctx = document.getElementById('mainChart');
            if (!ctx) return;
            if (typeof Chart === 'undefined') {
                console.error('Chart.js not loaded');
                ctx.parentElement.innerHTML = '<div style="display:flex;justify-content:center;align-items:center;height:100%;color:#ef4444">Chart.js library failed to load. Check internet connection.</div>';
                return;
            }

            if (myChart) myChart.destroy();

            const titleEl = document.getElementById('chart-title');
            const subEl = document.getElementById('chart-subtitle');

            if (activeChart === 'bar') {
                titleEl.textContent = 'Total Stok per Barang (Horizontal)';
                subEl.textContent = 'Menampilkan stok untuk setiap nama barang (Bar ke samping).';

                myChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: metrics.bar.labels,
                        datasets: [{
                            label: 'Stok Barang',
                            data: metrics.bar.values,
                            backgroundColor: '#0f296b',
                            borderRadius: 6
                        }]
                    },
                    options: {
                        indexAxis: 'y', // Horizontal bars
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            x: { // In horizontal chart, value axis is X
                                beginAtZero: true,
                                suggestedMax: 10
                            }
                        }
                    }
                });
            } else {
                titleEl.textContent = 'Pergerakan Stok per Barang';
                subEl.textContent = 'Tren masuk/keluar seminggu terakhir per item.';

                const datasets = [];
                const colors = ['#16a34a', '#dc2626', '#2563eb', '#d97706', '#7c3aed'];

                metrics.line.data.forEach((item, idx) => {
                    const color = colors[idx % colors.length];
                    datasets.push({
                        label: item.name + ' (In)',
                        data: item.in,
                        borderColor: color,
                        borderDash: [5, 5],
                        tension: 0.3,
                        pointRadius: 2
                    });
                    datasets.push({
                        label: item.name + ' (Out)',
                        data: item.out,
                        borderColor: color,
                        tension: 0.3,
                        pointRadius: 2
                    });
                });

                myChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: metrics.line.labels,
                        datasets: datasets
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: { boxWidth: 10, font: { size: 10 } }
                            }
                        },
                        scales: { y: { beginAtZero: true } }
                    }
                });
            }
        }

        document.getElementById('prev-chart').addEventListener('click', () => {
            if (metrics[activeChart].page > 1) {
                fetchChartData(activeChart, metrics[activeChart].page - 1);
            }
        });

        document.getElementById('next-chart').addEventListener('click', () => {
            if (metrics[activeChart].page < metrics[activeChart].last) {
                fetchChartData(activeChart, metrics[activeChart].page + 1);
            }
        });

        document.querySelectorAll('.chart-tab').forEach(function (btn) {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.chart-tab').forEach(function (b) { b.classList.remove('active'); });
                this.classList.add('active');
                activeChart = this.getAttribute('data-tab');
                renderChart();
                updatePaginationUI();
            });
        });

        renderChart();
        updatePaginationUI();

        window.addEventListener('resize', () => { if (myChart) myChart.resize(); });
        } catch (err) {
            console.error(err);
            const box = document.getElementById('js-error-box');
            box.style.display = 'block';
            box.innerHTML += `CRASH: ${err.message}<br>`;
        }
    </script>

</body>

</html>