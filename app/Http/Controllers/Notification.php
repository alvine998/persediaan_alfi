<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Notification extends Controller
{
    // Halaman Notification
    public function index()
    {
        // Validasi Session
        if (!session()->get('login'))
            return redirect()->route('auth')->with('message', 'Anda Belum Login!');

        // Data
        $data = [
            'title'     => 'Home'
        ];

        return view('notification.index', $data);
    }
}
