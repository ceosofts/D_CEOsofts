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
        // หากต้องการส่งข้อมูลเพิ่มเติมไปยัง view สามารถเพิ่ม array ใน parameter ที่สองได้
        return \view('dashboard'); // ชี้ไปยัง view ที่อยู่ใน resources/views/dashboard.blade.php
    }
}
