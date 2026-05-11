<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/debug/students', function () {
    try {
        $students = \App\Models\User::where('role', 'student')->latest()->limit(5)->get();
        return response()->json([
            'success' => true,
            'count' => $students->count(),
            'students' => $students->makeVisible(['id', 'name', 'email', 'role'])
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ], 500);
    }
})->middleware('auth');

Route::get('/debug/database', function () {
    try {
        \DB::connection()->getPdo();
        
        $tables = \DB::select("
            SELECT tablename 
            FROM pg_tables 
            WHERE schemaname = 'public'
            ORDER BY tablename
        ");
        
        return response()->json([
            'success' => true,
            'connection' => 'PostgreSQL Connected',
            'database' => config('database.connections.pgsql.database'),
            'tables_count' => count($tables),
            'tables' => array_column($tables, 'tablename')
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'connection_string' => [
                'host' => config('database.connections.pgsql.host'),
                'port' => config('database.connections.pgsql.port'),
                'database' => config('database.connections.pgsql.database'),
            ]
        ], 500);
    }
});

Route::get('/debug/user-count', function () {
    try {
        $admin_count = \App\Models\User::where('role', 'admin')->count();
        $staff_count = \App\Models\User::where('role', 'staff')->count();
        $student_count = \App\Models\User::where('role', 'student')->count();
        
        return response()->json([
            'success' => true,
            'admin_count' => $admin_count,
            'staff_count' => $staff_count,
            'student_count' => $student_count,
            'total_users' => $admin_count + $staff_count + $student_count
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
});

