<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WebController extends Controller
{
    public function index()
    {
        return view('Dashboard.contents.index');
    }

    public function login()
    {
        return view('Login.index');
    }
}
