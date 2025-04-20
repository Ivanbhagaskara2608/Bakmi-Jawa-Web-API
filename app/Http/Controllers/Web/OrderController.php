<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Response;

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
        // Fetch orders with order_type 'dine_in' and eager load order details and menu
        $orders = Order::with(['orderDetails.menu'])
                        ->where('order_type', 'dine_in')
                        ->whereHas('payments', function ($query) {
                            $query->where('status', 'PAID');
                        })
                        ->orderBy('created_at', 'desc')
                        ->get();

        // Return the data formatted for DataTables
        return DataTables::of($orders)
            ->addIndexColumn() // Add row index
            ->addColumn('jenis', function ($order) {
                // Display 'Point' if is_point_used is true, otherwise 'Regular'
                return $order->is_point_used ? 'Point' : 'Regular';
            })
            ->addColumn('pesanan', function ($order) {
                // Get the names of the ordered menus from order details
                return $order->orderDetails->map(function ($detail) {
                    return '<li>' . $detail->menu->nama . '</li>';
                })->implode('');
            })
            ->addColumn('catatan', function ($order) {
                // Get the note from order table
                return $order->note ?? '-';
            })
            ->addColumn('tanggal', function ($order) {
                // Format the created_at date
                return \Carbon\Carbon::parse($order->created_at)->format('d F Y');
            })
            ->addColumn('status', function ($order) {
                // Conditional ternary logic for status
                return $order->status == 'pending' ? 'Diproses' :
                       ($order->status == 'completed' ? 'Selesai' : 'Dibatalkan');
            })

            ->rawColumns(['pesanan']) // Ensure HTML rendering
            ->make(true);
    }

    public function data_order_takeaway()
    {
        // Fetch orders with order_type 'take_away' and eager load order details and menu
        $orders = Order::with(['orderDetails.menu'])
                        ->where('order_type', 'take_away')
                        ->whereHas('payments', function ($query) {
                            $query->where('status', 'PAID');
                        })
                        ->orderBy('created_at', 'desc')
                        ->get();

        // Return the data formatted for DataTables
        return DataTables::of($orders)
            ->addIndexColumn() // Add row index
            ->addColumn('jenis', function ($order) {
                // Display 'Point' if is_point_used is true, otherwise 'Regular'
                return $order->is_point_used ? 'Point' : 'Regular';
            })
            ->addColumn('pesanan', function ($order) {
                // Get the names of the ordered menus from order details
                return $order->orderDetails->map(function ($detail) {
                    return '<li>' . $detail->menu->nama . '</li>';
                })->implode('');
            })
            ->addColumn('catatan', function ($order) {
                // Get the note from order table
                return $order->note ?? '-';
            })
            ->addColumn('tanggal', function ($order) {
                // Format the created_at date
                return \Carbon\Carbon::parse($order->created_at)->format('d F Y');
            })
            ->addColumn('status', function ($order) {
                // Conditional ternary logic for status
                return $order->status == 'pending' ? 'Diproses' :
                       ($order->status == 'completed' ? 'Selesai' : 'Dibatalkan');
            })
            ->rawColumns(['pesanan']) // Ensure HTML rendering
            ->make(true);
    }

    public function show($id)
    {
        $order = Order::findOrFail($id);
        return response()->json($order);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,completed,cancelled',
        ]);

        $order = Order::findOrFail($id);
        $no_wa = $order->user->no_telp;

        if ($request->status == 'completed') {
            $response = Http::withHeaders(['Authorization' => env('FONNTE')])->post('https://api.fonnte.com/send',[
                'target' => '62' . $no_wa,
                'message' => 'Orderan kamu telah siap diambil',
            ]);

            if ($response->successful()) {
                $response = $response->json();
                if ($response['status'] == false) {
                    return Response::json([
                        'success' => false,
                        'message' => 'Gagal mengirim SMS',
                    ]);
                };
            } else {
                return Response::json([
                    'success' => false,
                    'message' => 'Gagal mengirim SMS',
                ]);
            }
        }

        $order->update([
            'status' => $request->status,
        ]);

        if ($order->order_type == 'dine_in') {
            return redirect()->route('pesanan.dinein');
        } else {
            return redirect()->route('pesanan.takeaway');
        }

        // if ($order->order_type == 'dine_in') {
        //     $order->update([
        //         'status' => $request->status,
        //     ]);
        //     return redirect()->route('pesanan.dinein');
        // } else {
        //     if ($request->status == 'completed') {
        //         $response = Http::withHeaders(['Authorization' => env('FONNTE')])->post('https://api.fonnte.com/send',[
        //             'target' => '62' . $no_wa,
        //             'message' => 'Orderan kamu telah siap diambil',
        //         ]);

        //         if ($response->successful()) {
        //             $response = $response->json();
        //             if ($response['status'] == false) {
        //                 return Response::json([
        //                     'success' => false,
        //                     'message' => 'Gagal mengirim SMS',
        //                 ]);
        //             };
        //         } else {
        //             return Response::json([
        //                 'success' => false,
        //                 'message' => 'Gagal mengirim SMS',
        //             ]);
        //         }
        //     }
        //     $order->update([
        //         'status' => $request->status,
        //     ]);
        //     return redirect()->route('pesanan.takeaway');
        // }
    }
}
