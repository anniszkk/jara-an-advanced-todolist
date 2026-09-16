<?php

namespace App\Http\Controllers;

use App\Models\TaskList;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

    // ===== SRS004 + SRS010 — Alihkan kepemilikan daftar secara atomic =====
    public function transfer(Request $request, TaskList $list)
    {
        $this->authorizeOwner($list);

        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $newOwnerId = (int) $request->user_id;

        // Pastikan target adalah anggota yang sah
        if (!$list->members()->where('user_id', $newOwnerId)->exists()) {
            return back()->with('error', 'User yang dipilih bukan anggota daftar ini.');
        }

        $oldOwnerId = $list->owner_id;

        // Ketiga operasi dibungkus dalam transaksi (ACID — Atomicity).
        // Jika salah satu gagal (misal constraint violation), semua di-rollback.
        DB::transaction(function () use ($list, $newOwnerId, $oldOwnerId) {
            // Langkah 1: Ganti pemilik daftar
            $list->update(['owner_id' => $newOwnerId]);

            // Langkah 2: Pemilik baru dihapus dari tabel member (sudah jadi owner)
            $list->members()->detach($newOwnerId);

            // Langkah 3: Pemilik lama otomatis jadi anggota biasa
            $list->members()->attach($oldOwnerId);
        });

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