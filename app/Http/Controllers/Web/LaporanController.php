<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB as FacadesDB;
use Illuminate\Support\Facades\Validator;

class LaporanController extends Controller
{
    public function index()
    {
        return view('dashboard.contents.laporan');
    }

    public function data(Request $request)
    {
        $validations = Validator::make($request->all(), [
            'bulan' => 'required|numeric|min:1|max:12',
            'tahun' => 'required|numeric|min:2024',
        ]);
        if ($validations->fails()) {
            return response()->json($validations->errors(), 400);
        }
    
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        // Mendapatkan data laporan bulanan berdasarkan tabel payments
        $orders = Order::select(
            FacadesDB::raw('DATE(orders.created_at) as tanggal'), // Mengelompokkan per hari
            FacadesDB::raw('SUM(payments.total) as pemasukan'), // Total pemasukan harian dari tabel payments
            FacadesDB::raw('COUNT(orders.id) as jumlah_transaksi') // Total transaksi harian
        )
        ->join('payments', 'orders.id', '=', 'payments.order_id') // Join dengan tabel payments
        ->where('orders.status', 'completed') // Hanya pesanan yang selesai
        ->where('payments.status', 'PAID') // Hanya pembayaran yang berhasil
        ->whereMonth('orders.created_at', $bulan) // Filter berdasarkan bulan
        ->whereYear('orders.created_at', $tahun) // Filter berdasarkan tahun
        ->groupBy('tanggal') // Mengelompokkan per tanggal
        ->get();

        // Untuk setiap tanggal, hitung item terjual dan item terlaris
        $orders->each(function ($order) {
            // Mengambil detail transaksi pada tanggal tertentu
            $details = OrderDetail::whereHas('order', function ($query) use ($order) {
                $query->whereDate('created_at', $order->tanggal)->where('status', 'completed');
            })->get();

            // Menghitung jumlah item terjual
            $order->item_terjual = $details->sum('qty');

            // Menghitung item terlaris
            $terlaris = $details->groupBy('menu_id')
                ->sortByDesc(fn($item) => $item->sum('qty'))
                ->keys()
                ->first();

            // Dapatkan nama menu terlaris
            if ($terlaris) {
                $menuName = \App\Models\Menu::find($terlaris)->nama; // Pastikan model Menu memiliki field 'name'
                $order->item_terlaris = $menuName ? $menuName : 'Tidak ada'; // Jika tidak ada, berikan nilai default
            } else {
                $order->item_terlaris = 'Tidak ada'; // Jika tidak ada item
            }
        });

        // Mengembalikan data yang diformat untuk DataTables
        return DataTables::of($orders)
            ->addIndexColumn()
            ->addColumn('tanggal', fn($order) => Carbon::parse($order->tanggal)->format('d F Y'))
            ->addColumn('pemasukan', fn($order) => number_format($order->pemasukan, 0, ',', '.'))
            ->addColumn('item_terjual', fn($order) => $order->item_terjual)
            ->addColumn('item_terlaris', fn($order) => $order->item_terlaris)
            ->addColumn('jumlah_transaksi', fn($order) => $order->jumlah_transaksi)
            ->rawColumns(['pemasukan', 'item_terlaris'])
            ->make(true);
    }

    public function laporan_print(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $monthName = Carbon::createFromDate(null, $bulan, null)->translatedFormat('F');

        // Mendapatkan data laporan bulanan berdasarkan tabel payments
        $orders = Order::select(
            FacadesDB::raw('DATE(orders.created_at) as tanggal'), // Mengelompokkan per hari
            FacadesDB::raw('SUM(payments.total) as pemasukan'), // Total pemasukan harian dari tabel payments
            FacadesDB::raw('COUNT(orders.id) as jumlah_transaksi') // Total transaksi harian
        )
        ->join('payments', 'orders.id', '=', 'payments.order_id') // Join dengan tabel payments
        ->where('orders.status', 'completed') // Hanya pesanan yang selesai
        ->where('payments.status', 'PAID') // Hanya pembayaran yang berhasil
        ->whereMonth('orders.created_at', $bulan) // Filter berdasarkan bulan
        ->whereYear('orders.created_at', $tahun) // Filter berdasarkan tahun
        ->groupBy('tanggal') // Mengelompokkan per tanggal
        ->get();

        // Hitung total pemasukan
        $totalPemasukan = $orders->sum('pemasukan');

        // Untuk setiap tanggal, hitung item terjual dan item terlaris
        $orders->each(function ($order) {
            // Mengambil detail transaksi pada tanggal tertentu
            $details = OrderDetail::whereHas('order', function ($query) use ($order) {
                $query->whereDate('created_at', $order->tanggal)->where('status', 'completed');
            })->get();

            // Menghitung jumlah item terjual
            $order->item_terjual = $details->sum('qty');

            // Menghitung item terlaris
            $terlaris = $details->groupBy('menu_id')
                ->sortByDesc(fn($item) => $item->sum('qty'))
                ->keys()
                ->first();

            // Dapatkan nama menu terlaris
            if ($terlaris) {
                $menuName = \App\Models\Menu::find($terlaris)->nama; // Pastikan model Menu memiliki field 'name'
                $order->item_terlaris = $menuName ? $menuName : 'Tidak ada'; // Jika tidak ada, berikan nilai default
            } else {
                $order->item_terlaris = 'Tidak ada'; // Jika tidak ada item
            }
        });

        return view('dashboard.contents.print_laporan', compact('orders', 'monthName', 'tahun', 'totalPemasukan'));
    }


}
