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

    /**
     * แสดงรายการโพสต์ (เฉพาะ Admin เท่านั้น)
     */
    public function index()
    {
        // ตัวอย่างการแสดงผล
        return "แสดงรายการโพสต์ (เฉพาะ Admin เท่านั้น)";
    }

    /**
     * แสดงฟอร์มแก้ไขโพสต์สำหรับ Admin
     *
     * @param int $id
     * @return string
     */
    public function edit($id)
    {
        // ตัวอย่างการแสดงผลแก้ไขโพสต์
        return "แก้ไขโพสต์ ID: " . $id . " (เฉพาะ Admin เท่านั้น)";
    }

    /**
     * อัปเดตบทความ (ตรวจสอบสิทธิ์ด้วย authorize)
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Post $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Post $post)
    {
        // ตรวจสอบสิทธิ์การอัปเดตบทความ
        $this->authorize('update', $post);

        // ดำเนินการอัปเดตข้อมูลตามที่ต้องการ
        // (ตัวอย่างนี้ส่งกลับผลลัพธ์เป็น JSON)
        return \response()->json(['message' => 'อัปเดตบทความสำเร็จ!']);
    }
}
