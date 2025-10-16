<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminController extends Controller
{
    public function showLogin()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string'
        ]);

        // Chỉ cho phép user đang active
        $credentials = [
            'username' => $data['username'],
            'password' => $data['password'],
            'isActive' => 1,
        ];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/admin/dashboard');
        }

        return back()->withErrors([
            'username' => 'Thông tin đăng nhập không chính xác hoặc tài khoản chưa được kích hoạt.',
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
        // Tự phát hiện tên bảng thật trong DB (để tương thích dữ liệu cũ)
        $clientTable   = Schema::hasTable('clients')  ? 'clients'  : (Schema::hasTable('client')  ? 'client'  : null);
        $projectTable  = Schema::hasTable('projects') ? 'projects' : (Schema::hasTable('project') ? 'project' : null);
        $locationTable = Schema::hasTable('locations')? 'locations': (Schema::hasTable('location')? 'location': null);

        $totalClients   = $clientTable   ? DB::table($clientTable)->count() : 0;
        $totalProjects  = $projectTable  ? DB::table($projectTable)->count() : 0;
        $totalLocations = $locationTable ? DB::table($locationTable)->count() : 0;

        // Dự phòng: nếu có cột status trong projects thì đếm active, nếu không thì = tổng
        $activeProjects = $totalProjects;
        if ($projectTable && Schema::hasColumn($projectTable, 'status')) {
            try {
                $activeProjects = DB::table($projectTable)->where('status', 'active')->count();
            } catch (\Throwable $e) {
                $activeProjects = $totalProjects;
            }
        }

        // Recent projects (an toàn tên cột)
        $recentProjects = [];
        if ($projectTable) {
            $query = DB::table($projectTable);
            // Order by created_at nếu có, không thì order by id desc (định danh theo tên bảng để tránh ambiguous)
            if (Schema::hasColumn($projectTable, 'created_at')) {
                $query = $query->orderByDesc($projectTable.'.created_at');
            } else if (Schema::hasColumn($projectTable, 'updated_at')) {
                $query = $query->orderByDesc($projectTable.'.updated_at');
            } else {
                $query = $query->orderByDesc($projectTable.'.id');
            }

            // Không join client để tránh ambiguous columns giữa 2 bảng

            $recentProjects = $query->limit(5)->get()->map(function ($row) {
                return [
                    'id' => $row->id ?? null,
                    'name' => $row->project_name ?? ($row->name ?? 'N/A'),
                    'client_name' => null,
                    'industry' => $row->industry ?? null,
                    'status' => $row->status ?? 'active',
                ];
            })->toArray();
        }

        $stats = [
            'total_clients' => $totalClients,
            'total_projects' => $totalProjects,
            'total_locations' => $totalLocations,
            'active_projects' => $activeProjects,
        ];

        // Chart data (đơn giản hoá nếu thiếu dữ liệu)
        $comparisonLabels = array_map(fn($p) => $p['name'], $recentProjects);
        $comparisonData = array_fill(0, count($comparisonLabels), 0);
        $scoringStatus = [
            'completed' => 0,
            'in_progress' => 0,
            'not_started' => $totalProjects,
        ];

        return view('admin.dashboard', compact('stats', 'recentProjects', 'comparisonLabels', 'comparisonData', 'scoringStatus'));
    }

    public function setupAdmin()
    {
        // Tạo admin user nếu chưa có theo schema (username/isActive/role)
        $admin = \App\Models\User::firstOrCreate(
            ['username' => 'admin'],
            [
                'password' => Hash::make('admin123'),
                'isActive' => 1,
                'role' => 1
            ]
        );

        return response()->json(['message' => 'Admin user created successfully']);
    }
}
