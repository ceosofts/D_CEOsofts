<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * แสดงหน้า Dashboard
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('dashboard'); // ชี้ไปยัง view `resources/views/dashboard.blade.php`
    }
}
