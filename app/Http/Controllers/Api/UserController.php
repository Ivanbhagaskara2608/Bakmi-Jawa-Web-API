<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Otp;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Response;

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
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'No Telp atau Password salah'
        ], 401);
    }

    public function register(Request $request)
    {
        $request->validate([
            'nama' => 'required|min:3|max:72',
            'no_telp' => 'required',
            'tanggal_lahir' => 'required|date|before:today',
            'password' => 'required|min:8|max:64|confirmed'
        ]);

        if (User::where('no_telp', $request->no_telp)->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'No Telp sudah terdaftar'
            ]);
        }

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
            'message' => 'Profile berhasil diubah',
            'data' => $user
        ]);
    }

    // create otp
    public function createOtp(Request $request)
    {
        $request->validate([
            'phone_number' => 'required'
        ]);

        $otp = Otp::create([
            'otp' => rand(100000, 999999),
            'phone_number' => $request->phone_number,
            'expired_at' => now()->addMinutes(5)
        ]);

        $response = Http::withHeaders(['Authorization' => env('FONNTE')])->post('https://api.fonnte.com/send',[
            'target' => '62' . $request->phone_number,
            'message' => 'Kode OTP: ' . $otp['otp'] . ' berlaku selama 5 menit',
        ]);

        if ($response->successful()) {
            $response = $response->json();
            if ($response['status'] == false) {
                return Response::json([
                    'success' => false,
                    'message' => 'Gagal mengirim SMS',
                ]);
            };
        } else {
            return Response::json([
                'success' => false,
                'message' => 'Gagal mengirim SMS',
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'OTP berhasil dibuat',
            'data' => $otp
        ]);
    }

    // verify otp
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone_number' => 'required',
            'otp' => 'required'
        ]);

        $otp = Otp::where('phone_number', $request->phone_number)
            ->where('otp', $request->otp)
            ->where('expired_at', '>=', now())
            ->first();

        if ($otp) {
            $otp->is_verified = true;
            $otp->save();

            return response()->json([
                'status' => true,
                'message' => 'OTP berhasil diverifikasi'
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'OTP tidak valid atau sudah kadaluarsa'
        ]);
    }

    // forgot password
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'phone_number' => 'required'
        ]);

        $user = User::where('no_telp', $request->phone_number)->first();

        if ($user) {
            $otp = Otp::create([
                'otp' => rand(100000, 999999),
                'phone_number' => $request->phone_number,
                'expired_at' => now()->addMinutes(5)
            ]);

            $response = Http::withHeaders(['Authorization' => env('FONNTE')])->post('https://api.fonnte.com/send',[
                'target' => '62' . $request->phone_number,
                'message' => 'Kode OTP: ' . $otp['otp'] . ' berlaku selama 5 menit',
            ]);

            if ($response->successful()) {
                $response = $response->json();
                if ($response['status'] == false) {
                    return Response::json([
                        'success' => false,
                        'message' => 'Gagal mengirim SMS',
                    ]);
                };
            } else {
                return Response::json([
                    'success' => false,
                    'message' => 'Gagal mengirim SMS',
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => 'OTP berhasil dibuat',
                'data' => $otp
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Nomor telepon tidak terdaftar'
        ]);
    }

    // reset password
    public function resetPassword(Request $request)
    {
        $request->validate([
            'phone_number' => 'required',
            'password' => 'required|min:8|max:64|confirmed'
        ]);

        $otp = Otp::where('phone_number', $request->phone_number)
            ->where('expired_at', '>=', now())
            ->where('is_verified', true)
            ->first();

        if (!$otp) {
            return response()->json([
                'status' => false,
                'message' => 'OTP tidak valid atau sudah kadaluarsa'
            ]);
        }

        $user = User::where('no_telp', $request->phone_number)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Password berhasil diubah'
        ]);
    }

}
