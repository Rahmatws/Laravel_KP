<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Stok Akhir</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .no-print {
            margin-bottom: 20px;
            padding: 10px;
            background: #eee;
            border: 1px solid #ddd;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="no-print">
        <strong>Mode Cetak PDF</strong><br>
        Silakan tekan tombol <code>Ctrl + P</code> atau klik kanan > Print, lalu pilih "Save as PDF" / "Simpan sebagai
        PDF".
    </div>

    <div class="header">
        @php
            $labels = [
                'stok-akhir' => 'Laporan Stok Akhir',
                'riwayat-stok' => 'Laporan Riwayat Stok',
                'barang-menipis' => 'Laporan Barang Menipis',
                'per-barang' => 'Laporan Per Barang'
            ];
            $title = $labels[$type] ?? 'Laporan Stok';
        @endphp
        <h2>{{ $title }}</h2>
        <p>CV Panca Indra Keemasan<br>
            @if($start && $end)
                Periode: {{ date('d/m/Y', strtotime($start)) }} - {{ date('d/m/Y', strtotime($end)) }}<br>
            @endif
            Tanggal Cetak: {{ date('d M Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                @foreach($headers as $h)
                    <th>{{ $h }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $rowData)
                <tr>
                    @foreach($rowData as $val)
                        <td>{{ $val }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    <script>
        window.print();
    </script>
</body>

</html>