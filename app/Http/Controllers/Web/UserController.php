<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        return view('login.index');
    }

    public function login(Request $request)
    {
        $credentials = [
            'nama' => $request->input('username'),
            'password' => $request->input('password'),
        ];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('dashboard.index');
        } else {
            return redirect()->back()->with('error', 'Username or password is incorrect');
        }
    }

    public function logout(Request $request)
    {
        // Use Auth::logout() to log out the user
        Auth::logout();

        // Regenerate the session to prevent any potential session fixation
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect back to the login page with a logout message
        return redirect()->route('login')->with('message', 'Successfully logged out');
    }

}
