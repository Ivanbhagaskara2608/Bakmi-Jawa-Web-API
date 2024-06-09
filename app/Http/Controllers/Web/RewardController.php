<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reward;
use Yajra\DataTables\Facades\DataTables;

class RewardController extends Controller
{
    public function index()
    {
        return view('dashboard.contents.rewards');
    }

    public function store(Request $request)
    {
        $request->validate([
            'menu_id' => 'required|exists:menus,id',
            'point' => 'required|integer',
        ]);

        Reward::create($request->all());

        return redirect()->route('reward.index');
    }

    public function data_reward()
    {
        $rewards = Reward::with('menu')->get();
        $dt = DataTables::of($rewards)
            ->addIndexColumn()
            ->addColumn('menu', function ($reward) {
                return $reward->menu->nama;
            })
            ->toJson();

        return $dt;
    }

    public function show($id)
    {
        $reward = Reward::findOrFail($id);
        $menu = $reward->menu;

        return response()->json([
            'reward' => $reward,
            'nama_menu' => $menu->nama,
            'gambar' => $menu->gambar
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'point' => 'required|integer',
        ]);

        $reward = Reward::findOrFail($id);
        $reward->update($request->all());

        return redirect()->route('reward.index');
    }

    public function destroy($id)
    {
        Reward::destroy($id);

        return redirect()->route('reward.index');
    }
}
