<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the dashboard view
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Check if the user is an admin, redirect to admin dashboard if true
        if (Auth::user() && Auth::user()->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }
        
        return view('dashboard');
    }

    /**
     * Redirect to dashboard from /home
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectToDashboard()
    {
        return redirect()->route('dashboard');
    }

    /**
     * Display the admin dashboard view
     *
     * @return \Illuminate\View\View
     */
    public function adminHome()
    {
        // You can pass admin-specific data to the view here
        $data = [
            'userCount' => \App\Models\User::count(),
            'title' => 'Admin Dashboard'
        ];
        
        return view('admin.dashboard', $data);
    }
}
