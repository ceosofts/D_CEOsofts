<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PostPolicy
{
    /**
     * กำหนดว่า user สามารถดูรายการทั้งหมดของโพสต์ได้หรือไม่
     */
    public function viewAny(User $user): bool
    {
        // ตัวอย่าง: อาจให้เฉพาะผู้ที่มี permission 'view posts' เท่านั้น
        return $user->hasPermissionTo('view posts');
    }

    /**
     * กำหนดว่า user สามารถดูโพสต์นี้ได้หรือไม่
     */
    public function view(User $user, Post $post): bool
    {
        // ตัวอย่าง: ให้ดูได้ถ้าเป็นเจ้าของโพสต์หรือมี permission 'view posts'
        return $user->id === $post->user_id || $user->hasPermissionTo('view posts');
    }

    /**
     * กำหนดว่า user สามารถสร้างโพสต์ใหม่ได้หรือไม่
     */
    public function create(User $user): bool
    {
        // ตัวอย่าง: ให้สร้างได้เฉพาะผู้ที่มี permission 'create posts'
        return $user->hasPermissionTo('create posts');
    }

    /**
     * กำหนดว่า user สามารถแก้ไขโพสต์ได้หรือไม่
     */
    public function update(User $user, Post $post): Response
    {
        // อนุญาตให้แก้ไขได้ถ้า user มี permission 'edit articles'
        // หรือเป็นเจ้าของโพสต์ (user id เท่ากับ post->user_id)
        return ($user->hasPermissionTo('edit articles') || $user->id === $post->user_id)
            ? Response::allow()
            : Response::deny('คุณไม่มีสิทธิ์แก้ไขบทความนี้');
    }

    /**
     * กำหนดว่า user สามารถลบโพสต์ได้หรือไม่
     */
    public function delete(User $user, Post $post): bool
    {
        // ตัวอย่าง: ให้ลบได้เฉพาะผู้ที่มี permission 'delete posts'
        return $user->hasPermissionTo('delete posts');
    }

    /**
     * กำหนดว่า user สามารถกู้คืนโพสต์ที่ถูกลบได้หรือไม่
     */
    public function restore(User $user, Post $post): bool
    {
        // ตัวอย่าง: ให้กู้คืนได้เฉพาะ admin
        return $user->hasRole('admin');
    }

    /**
     * กำหนดว่า user สามารถลบโพสต์อย่างถาวรได้หรือไม่
     */
    public function forceDelete(User $user, Post $post): bool
    {
        // ตัวอย่าง: ให้ลบถาวรได้เฉพาะ admin
        return $user->hasRole('admin');
    }
}
