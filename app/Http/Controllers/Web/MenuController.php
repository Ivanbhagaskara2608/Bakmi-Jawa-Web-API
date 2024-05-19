<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

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

    public function data_menu()
    {
        $menus = Menu::all();
        $dt = DataTables::of($menus)
            ->addIndexColumn()
            ->editColumn('gambar', function ($menu) {
                return '<img src="' . asset('images/menu/' . $menu->gambar) . '" alt="' . $menu->nama . '" class="img-fluid" style="max-width: 100px">';
            })
            ->rawColumns(['gambar'])
            ->toJson();

        return $dt;
    }

    public function show($id)
    {
        $menu = Menu::findOrFail($id);
        return response()->json($menu);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|min:3|max:255',
            'harga' => 'required|integer',
            'kategori' => 'required|in:makanan,minuman',
            'status' => 'required|in:TERSEDIA,HABIS',
            'deskripsi' => 'required|min:3',
            'gambar' => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $menu = Menu::findOrFail($id);

        if ($request->hasFile('gambar')) {
            $currentImage = $menu->gambar;
            if ($currentImage) {
                $file_path = public_path('images/menu/') . $currentImage;
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }

            $image = $request->file('gambar');
            $image_name = $request->nama . '-' . time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/menu'), $image_name);

            $menu->update([
                'nama' => $request->nama,
                'kategori' => $request->kategori,
                'harga' => $request->harga,
                'deskripsi' => $request->deskripsi,
                'gambar' => $image_name,
                'status' => $request->status, 
            ]);
        } else {
            $menu->update($request->all());
        }

        return redirect()->route('menu.index');
    } 

    public function destroy($id)
    {
        $menu = Menu::findOrFail($id);
        $currentImage = $menu->gambar;
        if ($currentImage) {
            $file_path = public_path('images/menu/') . $currentImage;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }

        $menu->delete();
        return redirect()->route('menu.index');
    }
}
