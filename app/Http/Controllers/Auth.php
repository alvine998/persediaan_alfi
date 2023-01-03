<?php

namespace App\Http\Controllers;

use App\Models\User_Login;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Auth extends Controller
{
    public function __construct()
    {
        // Check Expired
        checkExpired();
    }

    // Halaman Auth
    public function index()
    {
        // Data
        $data = [
            'title'     => 'Authentification'
        ];

        return view('auth.index', $data);
    }

    // Proses Log-out
    public function logout()
    {
        session()->forget('login');
        session()->flush();

        // Set Flashdata
        return redirect()->route('auth')->with('message', 'Anda Telah Keluar!');
    }

    // Proses Login
    public function login(Request $request)
    {
        // Validation 
        // Validasi
        $request->validate([
            'username'  => 'required',
            'password'  => 'required'
        ]);

        // Request Data
        $username = $request->username;
        $password = $request->password;

        // Validasti Login
        $getUserData = DB::table('user_login')->where('username', '=', $username)->first();

        // Check Username
        if (empty($getUserData)) {
            session()->flash('message', 'Akun Anda Tidak Ada Di Data Kami!');
            return redirect()->route('auth');
        } else {
            // Check Password
            if ($getUserData->password != $password) {
                session()->flash('message', 'Password Akun Salah!');
                return redirect()->route('auth');
            } else {
                $sessionData = [
                    'user_login_id'         => $getUserData->user_login_id,
                    'name'                  => $getUserData->name,
                    'username'              => $getUserData->username
                ];

                session()->put('login', $sessionData);

                session()->flash('message', 'Selamat Datang Di Applikasi Persediaan Kami');
                return redirect()->route('home');
            }
        }
    }
}
