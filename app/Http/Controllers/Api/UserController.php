<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        return response()->json([
            'status' => true,
            'message' => 'Hello World!'
        ]);
    }

    public function profile()
    {
        return response()->json([
            'status' => true,
            'message' => 'User profile',
            'data' => auth('sanctum')->user()
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'no_telp' => 'required',
            'password' => 'required|min:8|max:64'
        ]);

        if (Auth::attempt($request->all())) {
            $user = Auth::user();
            $token = $request->user()->createToken('api-android')->plainTextToken;

            return response()->json([
                'status' => true,
                'message' => 'User berhasil login',
                'data' => [
                    'user' => $user,
                    'token' => $token,
                ]
            ]);
        }
    }

    public function register(Request $request)
    {
        $request->validate([
            'nama' => 'required|min:3|max:72',
            'no_telp' => 'required|unique:users',
            'tanggal_lahir' => 'required|date|before:today',
            'password' => 'required|min:8|max:64|confirmed'
        ]);

        $user = User::create([
            'nama' => $request->nama,
            'no_telp' => $request->no_telp,
            'tanggal_lahir' => $request->tanggal_lahir,
            'password' => Hash::make($request->password),
            'role_id' => Role::where('nama', 'User')->first()->id
        ]);

        return response()->json([
            'status' => true,
            'message' => 'User berhasil didaftarkan',
            'data' => $user
        ]);
    }

    public function logout()
    {
        auth('sanctum')->user()->tokens()->delete();

        return response()->json([
            'status' => true,
            'message' => 'User berhasil logout'
        ]);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required|min:8|max:64|',
            'new_password' => 'required|min:8|max:64|confirmed'
        ]);

        $user = auth('sanctum')->user();

        if (Hash::check($request->old_password, $user->password)) {
            if ($request->old_password == $request->new_password) {
                return response()->json([
                    'status' => false,
                    'message' => 'Password baru tidak boleh sama dengan password lama'
                ]);
            }

            $user->password = Hash::make($request->new_password);
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'Password berhasil diubah'
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Password lama tidak sesuai'
        ]);
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'nama' => 'min:3|max:72',
            'tanggal_lahir' => 'date|before:today'
        ]);

        $user = auth('sanctum')->user();
        $user->update($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Profil berhasil diubah',
            'data' => $user
        ]);
    }
}
