<?php

namespace App\Http\Controllers;

use App\Models\History as HistoryModel;
use Illuminate\Http\Request;

class History extends Controller
{
    public function __construct()
    {
        // Check Expired
        checkExpired();
    }

    // Halaman History
    public function index()
    {
        // Validasi Session
        if (!session()->get('login'))
            return redirect()->route('auth')->with('message', 'Anda Belum Login!');

        // Data
        $data = [
            'title'     => 'History',
            'history'   => HistoryModel::all()
        ];

        return view('history.index', $data);
    }
}
