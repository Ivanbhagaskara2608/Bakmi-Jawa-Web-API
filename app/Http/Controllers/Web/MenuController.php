<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        return view('dashboard.contents.menu');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|min:3|max:255',
            'harga' => 'required|integer',
            'kategori' => 'required|in:makanan,minuman',
            'deskripsi' => 'required|min:3',
            'gambar' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Memproses gambar
        $image = $request->file('gambar');
        $image_name = $request->nama . '-' . time() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('images/menu'), $image_name);

        // Membuat entri menu baru
        Menu::create([
            'nama' => $request->nama,
            'kategori' => $request->kategori,
            'harga' => $request->harga,
            'deskripsi' => $request->deskripsi,
            'gambar' => $image_name,
        ]);

        return redirect()->route('menu.index');
    }

    public function menus()
    {
        $menus = Menu::all();
    }
}
