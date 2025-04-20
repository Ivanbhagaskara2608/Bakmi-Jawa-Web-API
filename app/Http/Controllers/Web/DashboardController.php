<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Reward;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalMenu = Menu::count();
        $dineIn = Order::where('order_type', 'dine_in')->where('status', 'pending')->count();
        $takeAway = Order::where('order_type', 'take_away')->where('status', 'pending')->count();
        $reward = Reward::count();
        return view('dashboard.contents.index', compact('totalMenu', 'dineIn', 'takeAway', 'reward'));
    }
}
