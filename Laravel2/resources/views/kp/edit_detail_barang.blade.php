<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit Barang - CV Panca Indra Keemasan</title>
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
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .edit-container {
            width: 100%;
            max-width: 600px;
            padding: 2rem;
        }

        .card {
            background-color: var(--color-card);
            border-radius: var(--radius-lg);
            padding: 2rem 2.5rem;
            box-shadow: var(--shadow-soft);
        }

        .section-heading {
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0 0 0.5rem;
            font-family: var(--font-heading);
            color: var(--color-primary);
        }

        .section-subtext {
            font-size: 0.95rem;
            color: var(--color-muted);
            margin: 0 0 2rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            font-weight: 500;
        }

        input {
            width: 100%;
            padding: 0.75rem 1rem;
            border-radius: 12px;
            border: 1px solid #d1d5db;
            background: #ffffff;
            font-size: 1rem;
            font-family: var(--font-body);
        }

        input:read-only {
            background-color: #f3f4f6;
            color: #6b7280;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.25rem;
        }

        .btn-actions {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 2rem;
        }

        .btn-cancel {
            padding: 0.75rem 1.5rem;
            border: 1px solid #d1d5db;
            border-radius: 999px;
            background: #ffffff;
            color: #374151;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-save {
            padding: 0.75rem 2rem;
            border: none;
            border-radius: 999px;
            background: var(--color-primary);
            color: #ffffff;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(15, 41, 107, 0.2);
        }

        .btn-save:hover {
            filter: brightness(1.1);
        }
    </style>
</head>

<body>
    <div class="edit-container">
        <div class="card">
            <h1 class="section-heading">Edit Data Barang</h1>
            <p class="section-subtext">Ubah informasi master barang untuk keperluan stok dan penjualan.</p>

            <form action="{{ route('kp.item.update') }}" method="post">
                @csrf
                <input type="hidden" name="id" value="{{ $item->id }}">
                <div class="form-group">
                    <label>Kode Barang (Tidak dapat diubah)</label>
                    <input type="text" name="code" value="{{ $item->code }}" readonly>
                </div>

                <div class="form-group">
                    <label>Nama Barang</label>
                    <input type="text" name="name" value="{{ $item->name }}" required>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label>Harga Beli</label>
                        <input type="number" name="purchase_price" value="{{ (int) $item->purchase_price }}" required>
                    </div>
                    <div class="form-group">
                        <label>Harga Jual</label>
                        <input type="number" name="sale_price" value="{{ (int) $item->sale_price }}" required>
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label>Stok Saat Ini</label>
                        <input type="number" name="stock" value="{{ (int) $item->stock }}" required>
                    </div>
                    <div class="form-group">
                        <label>Minimum Stok (Alert)</label>
                        <input type="number" name="min_stock" value="{{ (int) $item->min_stock }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Satuan (Unit)</label>
                    <input type="text" name="unit" value="{{ $item->unit }}" placeholder="Contoh: Pcs, Box, Kg">
                </div>

                <div class="btn-actions">
                    <a href="{{ route('kp.detail_barang') }}" class="btn-cancel">Batal</a>
                    <button type="submit" class="btn-save">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>