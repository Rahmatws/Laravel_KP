<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Kategori Barang - CV Panca Indra Keemasan</title>
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
            gap: 1.4rem;
            margin-left: 260px;
            width: calc(100% - 260px);
            min-height: 100vh;
        }

        .header-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-title {
            font-family: var(--font-heading);
            font-size: 1.7rem;
            margin: 0;
        }

        .section-subtext {
            font-size: 0.9rem;
            color: var(--color-muted);
            margin: 0.35rem 0 0;
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

        .actions-bar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 1rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--color-primary), var(--color-primary-dark));
            border: none;
            border-radius: 999px;
            padding: 0.55rem 1.5rem;
            font-size: 0.9rem;
            font-weight: 500;
            color: #ffffff;
            cursor: pointer;
            box-shadow: 0 12px 24px rgba(15, 41, 107, 0.18);
        }

        .btn-primary:hover {
            filter: brightness(1.05);
        }

        .btn-outline {
            background: #ffffff;
            border-radius: 999px;
            border: 1px solid #d1d5db;
            padding: 0.55rem 1.4rem;
            font-size: 0.9rem;
            color: var(--color-dark);
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .btn-outline:hover {
            background: #f3f4f6;
        }

        .btn-with-icon {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }

        .btn-with-icon .btn-icon {
            font-size: 1.1rem;
            line-height: 1;
        }

        .search-field {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            border-radius: 999px;
            border: 1px solid #d1d5db;
            padding: 0.55rem 1.1rem;
            background: #ffffff;
            box-shadow: var(--shadow-soft);
            max-width: 320px;
        }

        .search-field span {
            color: var(--color-muted);
        }

        .search-field input {
            border: none;
            outline: none;
            flex: 1;
            font-size: 0.9rem;
            background: transparent;
        }

        .table-wrapper {
            overflow: hidden;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-soft);
            background: #f9fbff;
            padding-bottom: 0.6rem;
        }

        .form-card {
            display: none;
            margin-top: 1.4rem;
            background-color: var(--color-card);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-soft);
            padding: 1.8rem 2rem 2rem;
            border: 1px solid rgba(15, 41, 107, 0.12);
            animation: fadeIn 0.3s ease forwards;
        }

        .form-card.is-visible {
            display: block;
        }

        .form-card header {
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
            margin-bottom: 1.4rem;
        }

        .form-card h2 {
            font-family: var(--font-heading);
            font-size: 1.25rem;
            margin: 0;
        }

        .form-card p {
            margin: 0;
            color: var(--color-muted);
            font-size: 0.9rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 1.2rem;
        }

        .form-field {
            display: flex;
            flex-direction: column;
            gap: 0.45rem;
        }

        .form-field label {
            font-size: 0.9rem;
            font-weight: 600;
        }

        .form-field input,
        .form-field textarea,
        .form-field select {
            border-radius: 12px;
            border: 1px solid #d1d5db;
            padding: 0.65rem 0.85rem;
            font-size: 0.95rem;
            font-family: var(--font-body);
            background: #ffffff;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .form-field textarea {
            min-height: 120px;
            resize: vertical;
        }

        .form-field input:focus,
        .form-field textarea:focus,
        .form-field select:focus {
            outline: none;
            border-color: rgba(15, 41, 107, 0.5);
            box-shadow: 0 0 0 3px rgba(15, 41, 107, 0.12);
        }

        .radio-group {
            display: flex;
            gap: 1.2rem;
            align-items: center;
        }

        .radio-option {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            font-size: 0.9rem;
        }

        .form-actions {
            margin-top: 1.6rem;
            display: flex;
            justify-content: flex-end;
            gap: 0.8rem;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
            background: #ffffff;
        }

        thead {
            background: linear-gradient(135deg, var(--color-primary), var(--color-primary-dark));
            color: #ffffff;
        }

        th,
        td {
            text-align: left;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #e5e7eb;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.3rem 0.75rem;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .status-active {
            background: rgba(34, 197, 94, 0.16);
            color: #15803d;
        }

        .status-inactive {
            background: rgba(239, 68, 68, 0.16);
            color: #b91c1c;
        }

        .action-links {
            display: inline-flex;
            gap: 0.75rem;
            font-size: 0.85rem;
        }

        .action-btn {
            border: none;
            background: transparent;
            font: inherit;
            color: inherit;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0;
        }

        .action-btn .btn-icon {
            font-size: 1.05rem;
            line-height: 1;
        }

        .action-btn--edit {
            color: var(--color-primary);
        }

        .action-btn--delete {
            color: #ef4444;
        }

        .action-btn:hover {
            text-decoration: underline;
        }

        .pagination {
            margin-top: 1.4rem;
        }

        .pagination nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            font-size: 0.85rem;
            color: var(--color-muted);
        }

        .pagination nav>div:first-child {
            /* "Showing x to y of z results" */
            white-space: nowrap;
        }

        .pagination nav>div:last-child {
            display: flex;
            justify-content: center;
        }

        .pagination nav ul {
            display: inline-flex;
            list-style: none;
            padding: 0;
            margin: 0;
            gap: 0.25rem;
        }

        .pagination nav ul li a,
        .pagination nav ul li span {
            display: inline-flex;
            min-width: 32px;
            height: 32px;
            padding: 0 0.75rem;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            border: 1px solid #e5e7eb;
            background: #ffffff;
            box-shadow: var(--shadow-soft);
        }

        .pagination nav ul li span[aria-current="page"] {
            background: linear-gradient(135deg, var(--color-primary), var(--color-primary-dark));
            color: #ffffff;
            border-color: transparent;
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

            .actions-bar {
                flex-direction: column;
                align-items: stretch;
            }

            .search-field {
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
                <div>
                    <h1 class="page-title">Kategori Barang</h1>
                    <p class="section-subtext">Kelola kategori untuk mengelompokkan stok barang. Tampilan masih statis
                        untuk kebutuhan desain.</p>
                </div>
                <button class="btn-logout" type="button">Logout</button>
            </div>

            <form id="logoutForm" method="post" action="{{ route('kp.logout') }}" style="display:none;">@csrf</form>

            <section class="card">
                <div class="actions-bar">
                    <button class="btn-primary btn-with-icon" type="button" id="add-category-button">
                        <span class="btn-icon">➕</span>
                        <span>Tambah Kategori</span>
                    </button>
                    <form id="category-filter-form" method="get" style="display:flex; gap:0.75rem; align-items:center;">
                        <div class="search-field">
                            <button type="submit"
                                style="border:none;background:transparent;cursor:pointer;padding:0;display:flex;align-items:center;justify-content:center;color:var(--color-muted);">
                                <span>🔍</span>
                            </button>
                            <input type="search" name="q" value="{{ $q ?? '' }}" placeholder="Search kategori..." />
                        </div>
                        <input id="per-page-input" type="number" name="per_page" min="1" max="100"
                            value="{{ $perPage ?? 10 }}"
                            style="width:80px; padding:0.45rem 0.6rem; border-radius:999px; border:1px solid #d1d5db; font-size:0.85rem;" />
                    </form>
                </div>
            </section>

            <section class="form-card" id="add-category-form">
                <header>
                    <h2>Tambah Kategori</h2>
                    <p>Lengkapi informasi kategori baru untuk mengelompokkan stok.</p>
                </header>
                <form id="category-form" method="post" action="{{ route('kp.kategori_barang.store') }}">
                    @csrf
                    <div class="form-grid">
                        <div class="form-field">
                            <label for="category-name">Nama Kategori</label>
                            <input id="category-name" name="name" type="text" placeholder="Contoh: Bahan Pengemasan"
                                required />
                        </div>
                        <div class="form-field">
                            <label for="category-status">Status</label>
                            <div class="radio-group" id="category-status">
                                <label class="radio-option">
                                    <input type="radio" name="is_active" value="1" checked />
                                    <span>Aktif</span>
                                </label>
                                <label class="radio-option">
                                    <input type="radio" name="is_active" value="0" />
                                    <span>Nonaktif</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-field" style="margin-top: 1.2rem;">
                        <label for="category-description">Deskripsi (opsional)</label>
                        <textarea id="category-description" name="description"
                            placeholder="Catat deskripsi singkat untuk kategori ini..." disabled></textarea>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn-outline btn-with-icon" data-action="cancel">
                            <span class="btn-icon">✖</span>
                            <span>Batal</span>
                        </button>
                        <button type="submit" class="btn-primary btn-with-icon">
                            <span class="btn-icon">💾</span>
                            <span>Simpan</span>
                        </button>
                    </div>
                </form>
            </section>

            <section class="form-card" id="edit-category-form">
                <header>
                    <h2>Edit Kategori</h2>
                    <p>Perbarui informasi kategori yang sudah ada.</p>
                </header>
                <form id="category-edit-form" method="post">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="category_id" />
                    <div class="form-grid">
                        <div class="form-field">
                            <label for="edit-category-name">Nama Kategori</label>
                            <input id="edit-category-name" name="name" type="text" placeholder="Nama kategori"
                                required />
                        </div>
                        <div class="form-field">
                            <label for="edit-category-status">Status</label>
                            <div class="radio-group" id="edit-category-status">
                                <label class="radio-option">
                                    <input type="radio" name="is_active" value="1" />
                                    <span>Aktif</span>
                                </label>
                                <label class="radio-option">
                                    <input type="radio" name="is_active" value="0" />
                                    <span>Nonaktif</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-field" style="margin-top: 1.2rem;">
                        <label for="edit-category-description">Deskripsi</label>
                        <textarea id="edit-category-description" name="edit-category-description"
                            placeholder="Tambahkan catatan deskripsi..."></textarea>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn-outline btn-with-icon" data-edit-action="cancel">
                            <span class="btn-icon">✖</span>
                            <span>Batal</span>
                        </button>
                        <button type="button" class="btn-primary btn-with-icon" data-edit-action="save">
                            <span class="btn-icon">💾</span>
                            <span>Simpan Perubahan</span>
                        </button>
                    </div>
                </form>
            </section>

            <section class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Nama Kategori</th>
                            <th>Jumlah Item</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            <tr>
                                <td>{{ $category->name }}</td>
                                <td>{{ $category->items_count }} Item</td>
                                <td>
                                    @if($category->is_active)
                                        <span class="status-pill status-active">Aktif</span>
                                    @else
                                        <span class="status-pill status-inactive">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="action-links">
                                    <button type="button" class="action-btn action-btn--edit" data-edit-button
                                        data-name="{{ $category->name }}"
                                        data-is-active="{{ $category->is_active ? 1 : 0 }}"
                                        data-update-url="{{ route('kp.kategori_barang.update', $category) }}">
                                        <span class="btn-icon">✏️</span>
                                        <span>Edit</span>
                                    </button>
                                    <form method="post" action="{{ route('kp.kategori_barang.destroy', $category) }}"
                                        onsubmit="return confirm('Hapus kategori {{ $category->name }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn action-btn--delete">
                                            <span class="btn-icon">🗑️</span>
                                            <span>Hapus</span>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">Belum ada kategori.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="pagination">
                    {{ $categories->onEachSide(1)->links() }}
                </div>
            </section>
        </main>
    </div>
    <script>
        (function () {
            var logoutBtns = document.querySelectorAll('.btn-logout');
            logoutBtns.forEach(function (btn) {
                btn.addEventListener('click', function () {
                    if (confirm('Yakin ingin logout?')) {
                        document.getElementById('logoutForm').submit();
                    }
                });
            });

            const addButton = document.getElementById('add-category-button');
            const addFormCard = document.getElementById('add-category-form');
            const addForm = document.getElementById('category-form');
            const addCancelButton = addForm.querySelector('[data-action="cancel"]');
            const editFormCard = document.getElementById('edit-category-form');
            const editForm = document.getElementById('category-edit-form');
            const editCancelButton = editForm.querySelector('[data-edit-action="cancel"]');
            const editButtons = document.querySelectorAll('[data-edit-button]');
            const filterForm = document.getElementById('category-filter-form');
            const perPageInput = document.getElementById('per-page-input');

            function showAddForm() {
                hideEditForm();
                addFormCard.classList.add('is-visible');
                addButton.setAttribute('disabled', 'disabled');
                const firstInput = addForm.querySelector('input, textarea, select');
                if (firstInput) {
                    firstInput.focus();
                }
                addForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }

            function hideAddForm() {
                addForm.reset();
                addFormCard.classList.remove('is-visible');
                addButton.removeAttribute('disabled');
                addButton.focus();
            }

            function hideEditForm() {
                editForm.reset();
                editFormCard.classList.remove('is-visible');
            }

            addButton.addEventListener('click', showAddForm);
            addCancelButton.addEventListener('click', hideAddForm);

            editCancelButton.addEventListener('click', () => {
                hideEditForm();
                addButton.removeAttribute('disabled');
                addButton.focus();
            });

            editButtons.forEach(function (btn) {
                btn.addEventListener('click', function () {
                    hideAddForm();
                    const name = btn.getAttribute('data-name') || '';
                    const isActive = btn.getAttribute('data-is-active') === '1';
                    const updateUrl = btn.getAttribute('data-update-url');

                    editForm.setAttribute('action', updateUrl);
                    editForm.querySelector('input[name="category_id"]').value = name;
                    document.getElementById('edit-category-name').value = name;

                    const activeRadio = editForm.querySelector('input[name="is_active"][value="1"]');
                    const inactiveRadio = editForm.querySelector('input[name="is_active"][value="0"]');
                    if (isActive && activeRadio) {
                        activeRadio.checked = true;
                    } else if (!isActive && inactiveRadio) {
                        inactiveRadio.checked = true;
                    }

                    editFormCard.classList.add('is-visible');
                    addButton.setAttribute('disabled', 'disabled');
                    editForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    document.getElementById('edit-category-name').focus();
                });
            });

            if (perPageInput && filterForm) {
                let perPageTimeout = null;
                perPageInput.addEventListener('input', function () {
                    if (perPageTimeout) {
                        clearTimeout(perPageTimeout);
                    }
                    perPageTimeout = setTimeout(function () {
                        if (perPageInput.value && parseInt(perPageInput.value, 10) > 0) {
                            filterForm.submit();
                        }
                    }, 400);
                });
            }
        })();
    </script>
</body>

</html>