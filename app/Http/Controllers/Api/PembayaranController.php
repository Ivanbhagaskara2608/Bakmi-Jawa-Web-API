<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Payments;
use App\Models\User;
use Nekoding\Tripay\Networks\HttpClient;
use Nekoding\Tripay\Signature;
use Nekoding\Tripay\Tripay;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

class PembayaranController extends Controller
{
    public function __construct()
    {
        $this->tripay = new Tripay(new HttpClient(env('TRIPAY_API_KEY')));
    }

    public function channel_pembayaran()
    {
        $tripay = new Tripay(new HttpClient(env('TRIPAY_API_KEY')));
        $response = $tripay->getChannelPembayaran();

        return response()->json([
            'success' => true,
            'data' => $response,
        ]);
    }

    public function pay($user, $menuItems, $payment)
    {
        $orderItems = [];
        foreach ($menuItems as $item) {
            $menu = Menu::findOrFail($item['menu_id']);
            $orderItems[] = [
                'name' => $menu->nama,
                'price' => $menu->harga,
                'quantity' => $item['qty'],
            ];
        }
        
        $data = [
            'method' => $payment->payment_method_code,
            'merchant_ref' => $payment->invoice_id,
            'amount' => $payment->total,
            'customer_name' => $user->nama,
            'customer_email' => 'test@gmail.com',
            'customer_phone' => $user->no_telp,
            'order_items' => $orderItems,
            'return_url' => 'https://localhost:8000',
            'callback_url' => 'https://6c30-103-92-232-2.ngrok-free.app/api/payment/callback',
            'expired_time' => (time() + (24 * 60 * 60)),
            'signature' => Signature::generate($payment->invoice_id . $payment->total),
        ];  

        // dd($data['signature']);

        $res = $this->tripay->createTransaction($data,Tripay::CLOSE_TRANSACTION);

        if ($res->getResponse()['success'] === false) {
            return response()->json([
                'success' => false,
                'message' => $res->getResponse()['message']
            ]);
        }

        $payment->update([
            'checkout_url' => $res->getResponse()['data']['checkout_url'],
        ]);

        return $res->getResponse();
    }

    public function tripay_callback(Request $request)
    {
        $callbackSignature = $request->server('HTTP_X_CALLBACK_SIGNATURE');
        $json = $request->getContent();
        $signature = hash_hmac('sha256', $json, env('TRIPAY_PRIVATE_KEY'));

        if ($signature !== (string) $callbackSignature) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid signature',
            ]);
        }

        if ('payment_status' !== (string) $request->server('HTTP_X_CALLBACK_EVENT')) {
            return response()->json([
                'success' => false,
                'message' => 'Unrecognized callback event, no action was taken',
            ]);
        }

        $data = json_decode($json);

        if (JSON_ERROR_NONE !== json_last_error()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid data sent by tripay',
            ]);
        }

        $invoiceId = $data->merchant_ref;
        $status = strtoupper((string) $data->status);

        if ($data->is_closed_payment === 1) {
            $invoice = Payments::where('invoice_id', $invoiceId)
                ->where('status', '=', 'UNPAID')
                ->first();

            if (! $invoice) {
                return response()->json([
                    'success' => false,
                    'message' => 'No invoice found or already paid: ' . $invoiceId,
                ]);
            }

            switch ($status) {
                case 'PAID':
                    $invoice->update(['status' => 'PAID']);
                    
                    $order = $invoice->order;
                    $user = $order->user;
                    $pointsEarned = floor($invoice->total / 10000) * 10;
                    $user->point += $pointsEarned;
                    $user->save();
                    $orderDetailsMessage = "Detail Pesanan:\n";
                    foreach ($order->orderDetails as $orderDetail) {
                        $orderDetailsMessage .= "- {$orderDetail->menu->nama} x{$orderDetail->qty}\n";
                    }

                    $orderType = $order->order_type == 'dine_in' ? 'Dine In' : 'Take Away';
                    $noteMessage = $order->note ? "Catatan: {$order->note}\n" : "";

                    $response = Http::withHeaders([
                        'Authorization' => env('FONNTE')
                    ])->post('https://api.fonnte.com/send', [
                        'target' => env('WA_NUMBER'),
                        'message' => "Orderan baru dari {$user->nama}\nTipe Pesanan: {$orderType}\n" . $noteMessage . $orderDetailsMessage,
                    ]);
            
                    if ($response->successful()) {
                        $response = $response->json();
                        if ($response['status'] == false) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Gagal mengirim SMS',
                            ]);
                        }
                    } else {
                        return response()->json([
                            'success' => false,
                            'message' => 'Gagal mengirim SMS',
                        ]);
                    }

                    break;

                case 'EXPIRED':
                    $invoice->update(['status' => 'EXPIRED']);
                    $order = $invoice->order;
                    $order->delete();
                    break;

                case 'FAILED':
                    $invoice->update(['status' => 'FAILED']);
                    $order = $invoice->order;
                    $order->delete();
                    break;

                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Unrecognized payment status',
                    ]);
            }

            return response()->json(['success' => true]);
        }
    }

}
