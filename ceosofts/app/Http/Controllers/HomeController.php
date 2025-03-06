<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * สร้าง instance ใหม่ของ HomeController
     *
     * @return void
     */
    public function __construct()
    {
        // กำหนด middleware 'auth' เพื่อให้ทุกฟังก์ชันใน controller นี้ต้องเข้าสู่ระบบ
        $this->middleware('auth');
    }

    /**
     * แสดงหน้า Dashboard ของแอปพลิเคชัน
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // ใช้ \view() จาก global namespace เพื่อชี้ไปยังไฟล์ view: resources/views/home.blade.php
        return \view('home');
    }
}
