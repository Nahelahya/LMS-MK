<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * Tampilkan daftar semua siswa (role = 'student').
     * Mendukung pencarian by nama/email dan filter status.
     */
    public function index(Request $request)
    {
        $query = User::where('role', 'student');

        // Pencarian berdasarkan nama atau email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan status aktif/nonaktif (jika kolom ada)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Urutkan terbaru, 10 per halaman (pagination otomatis)
        $students = $query->latest()->paginate(10)->withQueryString();

        return view('admin.students.index', compact('students'));
    }

    /**
     * Tampilkan detail satu siswa.
     */
    public function show(User $user)
    {
        // Pastikan yang dibuka memang role student
        abort_if($user->role !== 'student', 404);

        return view('admin.students.show', compact('user'));
    }

    /**
     * Hapus siswa dari database.
     */
    public function destroy(User $user)
    {
        abort_if($user->role !== 'student', 404);

        // Hapus foto profil dari storage jika ada
        if ($user->photo) {
            \Storage::disk('public')->delete($user->photo);
        }

        $user->delete();

        return redirect()->route('students.index')
                         ->with('success', 'Data siswa berhasil dihapus.');
    }
}