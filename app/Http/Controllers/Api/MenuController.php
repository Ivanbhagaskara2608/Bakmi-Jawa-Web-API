<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::all();
        
        return response()->json([
            'status' => true,
            'message' => 'Data menu berhasil diambil',
            'data' => $menus
        ]);
    }

    public function show($id)
    {
        $menu = Menu::findOrFail($id);

        return response()->json([
            'status' => true,
            'message' => 'Data menu berhasil diambil',
            'data' => $menu
        ]);
    }

    public function get_image($image)
    {
        $filePath = public_path('images/menu/' . $image);

        if (file_exists(($filePath))) {
            return response()->file($filePath);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Gambar tidak ditemukan'
            ]);
        }
    }
}
