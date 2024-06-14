<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reward;
use Illuminate\Http\Request;

class RewardController extends Controller
{
    public function index()
    {
        $rewards = Reward::with('menu')->get();

        return response()->json([
            'status' => true,
            'message' => 'Data reward berhasil diambil',
            'data' => $rewards
        ]);
    }

    public function show($id)
    {
        $reward = Reward::findOrFail($id);

        return response()->json([
            'status' => true,
            'message' => 'Data reward berhasil diambil',
            'data' => $reward
        ]);
    }
}
