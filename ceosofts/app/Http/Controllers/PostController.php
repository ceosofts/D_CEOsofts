<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;

class PostController extends Controller
{
    public function __construct()
    {
        // กำหนด Middleware ให้ใช้เฉพาะบางเมธอด
        $this->middleware('role:admin')->only(['index', 'edit']);
        $this->middleware('permission:edit articles')->only(['update']);
    }

    public function index()
    {
        return "แสดงรายการโพสต์ (เฉพาะ Admin เท่านั้น)";
    }

    public function edit($id)
    {
        return "แก้ไขโพสต์ ID: " . $id . " (เฉพาะ Admin เท่านั้น)";
    }

    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post); // ตรวจสอบสิทธิ์

        // return "อัปเดตบทความสำเร็จ!";
        return response()->json(['message' => 'อัปเดตบทความสำเร็จ!']);
    }
}
