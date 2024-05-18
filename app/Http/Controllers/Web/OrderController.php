<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
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
}
