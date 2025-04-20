<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
        }
        h2 {
            margin: 0;
        }
        .title {
            margin-bottom: 10px;
        }
        .total-pemasukan {
            text-align: right; /* Align to the right */
            margin-top: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table, th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        img {
            max-width: 100px; /* Adjust the logo size as needed */
            margin-bottom: 10px; /* Space below the image */
        }
    </style>
</head>
<body>

    <div class="title">
        <img src="{{ asset('dist/img/Logo_Bakmi.png') }}" alt="Logo Bakmi Jawa Pak Surat">
        <h2>Laporan Penjualan</h2>
        <h3>Bulan {{ $monthName }} Tahun {{ $tahun }}</h3>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Tanggal</th>
                <th>Pemasukan</th>
                <th>Item Terjual</th>
                <th>Item Terlaris</th>
                <th>Jumlah Transaksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orders as $index => $order)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $order->tanggal }}</td>
                    <td>Rp {{ number_format($order->pemasukan, 0, ',', '.') }}</td>
                    <td>{{ $order->item_terjual }}</td>
                    <td>{{ $order->item_terlaris }}</td>
                    <td>{{ $order->jumlah_transaksi }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-pemasukan">
        <h4>Total Pemasukan: Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</h4>
    </div>

    <script>
        window.print();
    </script>

</body>
</html>
