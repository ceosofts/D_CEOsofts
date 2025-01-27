<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AddUserCommand extends Command
{
    protected $signature = 'add:user {name} {email}';
    protected $description = 'Add a new user quickly';

    public function handle()
    {
        $name = $this->argument('name');
        $email = $this->argument('email');

        DB::table('users')->insert([
            'name' => $name,
            'email' => $email,
            'email_verified_at' => now(),
            'password' => bcrypt('password123'),
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->info("User '{$name}' added successfully!");
    }
}
