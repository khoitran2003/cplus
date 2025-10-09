<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function showLogin()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/admin/dashboard');
        }

        return back()->withErrors([
            'email' => 'Thông tin đăng nhập không chính xác.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/admin/login');
    }

    public function dashboard()
    {
        $stats = [
            'total_clients' => \App\Models\Client::count(),
            'total_projects' => \App\Models\Project::count(),
            'total_locations' => \App\Models\Location::count(),
            'active_projects' => \App\Models\Project::where('status', 'active')->count()
        ];

        return view('admin.dashboard', compact('stats'));
    }

    public function setupAdmin()
    {
        // Tạo admin user nếu chưa có
        $admin = \App\Models\User::firstOrCreate(
            ['email' => 'admin@cplus.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now()
            ]
        );

        return response()->json(['message' => 'Admin user created successfully']);
    }
}
