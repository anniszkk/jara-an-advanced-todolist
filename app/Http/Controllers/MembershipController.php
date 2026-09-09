<?php

namespace App\Http\Controllers;

use App\Models\TaskList;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MembershipController extends Controller
{
    // Undang user baru ke daftar
    public function invite(Request $request, TaskList $list)
    {
        $this->authorizeOwner($list);

        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($list->owner_id === $user->id) {
            return back()->with('error', 'User ini sudah menjadi pemilik daftar.');
        }

        if ($list->members()->where('user_id', $user->id)->exists()) {
            return back()->with('error', 'User ini sudah menjadi anggota.');
        }

        $list->members()->attach($user->id);

        return back()->with('success', 'Anggota berhasil ditambahkan.');
    }

    // Keluarkan anggota dari daftar
    public function remove(TaskList $list, User $user)
    {
        $this->authorizeOwner($list);

        $list->members()->detach($user->id);

        return back()->with('success', 'Anggota berhasil dikeluarkan.');
    }

    // Alihkan kepemilikan daftar ke anggota lain
    public function transfer(Request $request, TaskList $list)
    {
        $this->authorizeOwner($list);

        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $newOwnerId = $request->user_id;

        // Pastikan target adalah anggota yang sah
        if (!$list->members()->where('user_id', $newOwnerId)->exists()) {
            return back()->with('error', 'User yang dipilih bukan anggota daftar ini.');
        }

        $oldOwnerId = $list->owner_id;

        // Ganti pemilik
        $list->update(['owner_id' => $newOwnerId]);

        // Owner baru dihapus dari tabel member (karena sekarang jadi owner)
        $list->members()->detach($newOwnerId);

        // Owner lama otomatis jadi anggota biasa
        $list->members()->attach($oldOwnerId);

        return redirect()->route('lists.index')->with('success', 'Kepemilikan berhasil dialihkan.');
    }

    // Helper: cek apakah user yang login adalah pemilik daftar
    private function authorizeOwner(TaskList $list)
    {
        if ($list->owner_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk mengelola anggota daftar ini.');
        }
    }
}