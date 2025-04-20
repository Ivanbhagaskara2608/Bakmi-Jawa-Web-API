<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payments;
use App\Models\Reward;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Response;
use Ramsey\Uuid\Uuid;

class OrderController extends Controller
{
    public function index()
    {  
        $user = auth()->user();

        // Fetch orders with related payments
        $orders = Order::with('payments')->where('user_id', $user->id)->orderBy('created_at', 'desc')->get();

        // Transform the data for the desired response format
        $response = $orders->map(function ($order) {
            return [
                'id' => $order->id,
                'order_type' => $order->order_type,
                'status' => $order->status,
                'payment_status' => optional($order->payments)->status, // Get status from payments
                'note' => $order->note,
                'total' => optional($order->payments)->total, // Get total from payments
                'total_point' => optional($order->payments)->total_point, // Get total_point from payments
                'created_at' => Carbon::parse($order->created_at)->translatedFormat('j F Y'),
            ];
        });

        return response()->json($response);
    }

    public function store(Request $request)
    {
        $request->validate([
            'order_type' => 'required|in:dine_in,take_away',
            'menu_items' => 'required|array',
            'menu_items.*.menu_id' => 'required|exists:menus,id',
            'menu_items.*.qty' => 'required|integer|min:1',
            'payment_method' => 'required|string',
            'payment_method_code' => 'required|string',
            'note' => 'nullable|string',
        ]);
    
        try {
            foreach ($request->menu_items as $item) {
                $menu = Menu::findOrFail($item['menu_id']);
    
                if ($menu->status !== 'TERSEDIA') {
                    return response()->json([
                        'message' => "Menu {$menu->nama} tidak tersedia",
                    ], 400);
                }
            }
    
            $order = Order::create([
                'user_id' => auth()->id(),
                'order_type' => $request->order_type,
                'status' => 'pending',
                'is_point_used' => false,
                'note' => $request->note,
            ]);
    
            $totalAmount = 0;

            foreach ($request->menu_items as $item) {
                $menu = Menu::findOrFail($item['menu_id']);
    
                OrderDetail::create([
                    'order_id' => $order->id,
                    'menu_id' => $menu->id,
                    'qty' => $item['qty'],
                ]);
    
                $itemTotal = $menu->harga * $item['qty'];
                $totalAmount += $itemTotal;
            }
        
            $payment = Payments::create([
                'order_id' => $order->id,
                'payment_method' => $request->payment_method,
                'payment_method_code' => $request->payment_method_code,
                'status' => 'UNPAID',
                'total' => $totalAmount,
                'invoice_id' => 'INV-' . Uuid::uuid4(),
                'expired_at' => Carbon::now()->addDay(),
            ]);
    
            $user = auth()->user();

            $tripay = new PembayaranController();
    
            return response()->json([
                'message' => 'Order placed successfully',
                'order_id' => $order->id,
                'data' => $tripay->pay($user, $request->menu_items, $payment)
            ]);
        } catch (\Exception $e) {
            $order->delete();
            return response()->json([
                'message' => 'Error placing order',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    


    public function order_with_point(Request $request)
    {
        $request->validate([
            'order_type' => 'required|in:dine_in,take_away',
            'reward_id' => 'required|exists:rewards,id',
            'qty' => 'required|integer|min:1',
            'note' => 'nullable|string',
        ]);

        try {
            $reward = Reward::findOrFail($request->reward_id);
            $menu = Menu::findOrFail($reward->menu_id);
            $user = auth()->user();

            if ($menu->status !== 'TERSEDIA') {
                return response()->json([
                    'message' => "Menu {$menu->nama} tidak tersedia",
                ], 400);
            }

            $totalPoint = $reward->point * $request->qty;
            if ($user->point < $totalPoint) {
                return response()->json([
                    'message' => 'Point tidak mencukupi',
                ], 400);
            }

            $order = Order::create([
                'user_id' => auth()->id(), 
                'order_type' => $request->order_type,
                'status' => 'pending',
                'is_point_used' => true,
                'note' => $request->note,
            ]);

            OrderDetail::create([
                'order_id' => $order->id,
                'menu_id' => $menu->id,
                'qty' => $request->qty,
            ]);

            Payments::create([
                'order_id' => $order->id,
                'payment_method' => 'POINT',
                'payment_method_code' => 'POINT',
                'status' => 'PAID',
                'total' => 0,
                'total_point' => $totalPoint,
            ]);

            $user->point -= $totalPoint;
            $user->save();

            $orderType = $request->order_type == 'dine_in' ? 'Makan di Tempat' : 'Bawa Pulang';
            $noteMessage = $request->note ? "Catatan: {$request->note}\n" : "";

            $response = Http::withHeaders([
                'Authorization' => env('FONNTE')
            ])->post('https://api.fonnte.com/send', [
                'target' => env('WA_NUMBER'),
                'message' => "Orderan baru dari {$user->name}\nTipe Pesanan: {$orderType}\n" .
                            "Detail Pesanan:\n{$menu->nama} ({$request->qty})\n" .
                            $noteMessage,
            ]);

            if ($response->successful()) {
                $response = $response->json();
                if ($response['status'] == false) {
                    return Response::json([
                        'success' => false,
                        'message' => 'Gagal mengirim SMS',
                    ]);
                }
            } else {
                return Response::json([
                    'success' => false,
                    'message' => 'Gagal mengirim SMS',
                ]);
            }

            return response()->json([
                'message' => 'Order placed successfully',
                'order_id' => $order->id,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error placing order',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function show($id)
    {
        $order = Order::with('payments', 'orderDetails.menu')->findOrFail($id);

        $response = [
            'id' => $order->id,
            'order_type' => $order->order_type,
            'status' => $order->status,
            'note' => $order->note,
            'total' => optional($order->payments)->total,
            'total_point' => optional($order->payments)->total_point,
            'payment_method' => optional($order->payments)->payment_method,
            'payment_status' => optional($order->payments)->status,
            'checkout_url' => optional($order->payments)->checkout_url,
            'created_at' => Carbon::parse($order->created_at)->translatedFormat('j F Y'),
            'menu_items' => $order->orderDetails->map(function ($detail) {
                return [
                    'menu_id' => $detail->menu->id,
                    'nama' => $detail->menu->nama,
                    'gambar' => $detail->menu->gambar,
                    'qty' => $detail->qty,
                    'harga' => $detail->menu->harga,
                ];
            }),
        ];

        return response()->json($response);
    }

}
