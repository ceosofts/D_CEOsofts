<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AddUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add:user 
                            {name : The name of the user}
                            {email : The email of the user}
                            {--password= : Optional custom password (random if not provided)}
                            {--admin : Set user as admin}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add a new user to the system';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $name = $this->argument('name');
        $email = $this->argument('email');
        $password = $this->option('password');
        $isAdmin = $this->option('admin');

        // Validate email
        $validator = Validator::make(
            ['email' => $email],
            ['email' => 'required|email']
        );

        if ($validator->fails()) {
            $this->error('Invalid email format!');
            return 1;
        }

        // Check if email already exists
        if (User::where('email', $email)->exists()) {
            $this->error("User with email '{$email}' already exists!");
            return 1;
        }

        // Generate password if not provided
        if (!$password) {
            $password = Str::password(12);
            $this->comment("Generated password: {$password}");
        }

        try {
            $user = new User();
            $user->name = $name;
            $user->email = $email;
            $user->email_verified_at = now();
            $user->password = Hash::make($password);
            $user->remember_token = Str::random(10);
            
            // Set admin status if applicable
            if ($isAdmin && isset($user->is_admin)) {
                $user->is_admin = true;
            }
            
            $user->save();

            $this->info("✓ User '{$name}' added successfully!");
            $this->line("Email: {$email}");
            return 0;
        } catch (\Exception $e) {
            $this->error("Failed to create user: " . $e->getMessage());
            return 1;
        }
    }
}
