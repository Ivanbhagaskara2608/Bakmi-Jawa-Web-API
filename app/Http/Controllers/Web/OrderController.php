<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function dinein()
    {
        return view('dashboard.contents.dinein');
    }

    public function takeaway()
    {
        return view('dashboard.contents.takeaway');
    }

    public function data_order_dinein()
    {
        $orders = Order::where('status', 'Dine In')->get();
        $dt = DataTables::of($orders)
            ->addIndexColumn()
            ->editColumn('total', function ($order) {
                return 'Rp' . number_format($order->total, 0, ',', '.');
            })
            ->editColumn('status', function ($order) {
                return '<span class="badge badge-primary">' . $order->status . '</span>';
            })
            ->editColumn('action', function ($order) {
                return '<a href="' . route('order.show', $order->id) . '" class="btn btn-sm btn-primary">Detail</a>';
            })
            ->rawColumns(['status', 'action'])
            ->toJson();

        return $dt;
    }
}
