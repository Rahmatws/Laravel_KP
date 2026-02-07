<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function loginView()
    {
        return view('kp.login_blade');
    }

    public function registerView()
    {
        return view('kp.registrasi');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'fullname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:staff'], // Only staff allowed for registration
        ]);

        try {
            $user = User::create([
                'name' => $data['fullname'],
                'email' => $data['email'],
                'role' => $data['role'],
                'password' => Hash::make($data['password']),
            ]);
        } catch (\Throwable $e) {
            return back()->with('error', 'Registrasi gagal, silakan coba lagi.')->withInput();
        }

        return redirect('/')->with('success', 'Registrasi berhasil, silakan login.');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $remember = (bool) $request->boolean('remember');

        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']], $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Reset onboarding status setiap kali login untuk admin DAN staff
            // Request user: "saya ingin setiap kali saya login maka akan masuk nya ke setup awal menu Import CSV SID"
            if ($user->role === 'admin' || $user->role === 'staff') {
                $user->update([
                    'has_imported' => false,
                    'has_viewed_details' => false,
                    'has_viewed_stock' => false,
                ]);

                // Karena sudah direset, pasti akan masuk ke kondisi ini:
                return redirect()->route('kp.import')
                    ->with('info', 'Silakan import data CSV SID terlebih dahulu atau klik Lewati.');
            }

            return redirect()->route('kp.dashboard');
        }

        return back()->withErrors([
            'email' => 'Kredensial tidak valid.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
