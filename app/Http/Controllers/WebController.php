<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WebController extends Controller
{
    public function index()
    {
        return view('dashboard.contents.index');
    }

    public function login()
    {
        return view('login.index');
    }

    public function menu()
    {
        return view('dashboard.contents.menu');
    }

    public function reward()
    {
        return view('dashboard.contents.rewards');
    }
}
