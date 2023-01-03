<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Expired extends Controller
{
    // Disabled Notification Process
    public function index()
    {
        // Ubah semua is seen di dalam expired jadi 1
        DB::statement("UPDATE expired SET expired.is_seen=1");

        return json_encode(true);
    }
}
