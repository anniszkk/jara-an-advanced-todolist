<?php

namespace App\Http\Controllers;

use App\Models\TaskList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ListController extends Controller
{
    // ===== SRS003 — Tampilkan daftar milik user + daftar yang diikuti =====
    public function index()
    {
        // Eager load withCount untuk progress tugas (selesai / total)
        $ownedLists = TaskList::where('owner_id', Auth::id())
            ->withCount([
                'tasks',
                'tasks as done_count' => function ($q) {
                    $q->where('status', 'selesai');
                },
            ])
            ->get();

        // Daftar yang user ikuti sebagai anggota (via pivot list_user)
        $memberLists = Auth::user()
            ->lists()
            ->withCount([
                'tasks',
                'tasks as done_count' => function ($q) {
                    $q->where('status', 'selesai');
                },
            ])
            ->get();

        return view('lists.index', compact('ownedLists', 'memberLists'));
    }

    // Form bikin daftar baru
    public function create()
    {
        return view('lists.create');
    }

    // ===== SRS003 + SRS010 — Simpan daftar baru secara atomic =====
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // DB::transaction() memastikan pembuatan daftar berjalan secara atomic (ACID).
        // Jika ada exception, seluruh perubahan di-rollback otomatis.
        DB::transaction(function () use ($request) {
            TaskList::create([
                'name'        => $request->name,
                'description' => $request->description,
                'owner_id'    => Auth::id(),
            ]);
        });

        return redirect()->route('lists.index')->with('success', 'Daftar berhasil dibuat!');
    }

    // ===== SRS003 — Detail daftar; bisa diakses owner ATAU anggota =====
    public function show(TaskList $list)
    {
        $this->authorizeMember($list);

        // Eager load relasi agar tidak terjadi N+1 query
        $list->load(['owner', 'members', 'tasks']);

        return view('lists.show', compact('list'));
    }

    // Form edit daftar (hanya owner)
    public function edit(TaskList $list)
    {
        $this->authorizeOwner($list);
        return view('lists.edit', compact('list'));
    }

    // Simpan hasil edit (hanya owner)
    public function update(Request $request, TaskList $list)
    {
        $this->authorizeOwner($list);

        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $list->update($request->only('name', 'description'));

        return redirect()->route('lists.index')->with('success', 'Daftar berhasil diperbarui!');
    }

    // ===== SRS009 + SRS010 — Hapus daftar beserta tugas & keanggotaan secara atomic =====
    public function destroy(TaskList $list)
    {
        $this->authorizeOwner($list);

        // Semua langkah penghapusan dibungkus dalam satu transaksi (ACID — Atomicity).
        // Jika salah satu langkah gagal, seluruh perubahan akan di-rollback otomatis.
        DB::transaction(function () use ($list) {
            // Langkah 1 (SRS009): Hapus semua tugas dalam daftar ini.
            // Eloquent ORM menggunakan prepared statement — tidak ada raw SQL / string concat.
            $list->tasks()->delete();

            // Langkah 2 (SRS009): Lepaskan semua keanggotaan dari pivot table list_user.
            $list->members()->detach();

            // Langkah 3: Hapus daftar itu sendiri.
            $list->delete();
        });

        return redirect()->route('lists.index')
            ->with('success', 'Daftar beserta seluruh tugas dan keanggotaan berhasil dihapus!');
    }

    // ===== Helper: hanya owner yang boleh mengelola daftar =====
    private function authorizeOwner(TaskList $list)
    {
        if ($list->owner_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke daftar ini.');
        }
    }

    // ===== SRS003 Helper: owner ATAU anggota boleh melihat detail daftar =====
    private function authorizeMember(TaskList $list)
    {
        $userId = Auth::id();

        if ($list->owner_id === $userId) {
            return; // owner pasti boleh
        }

        // Eloquent query builder menggunakan prepared statement secara otomatis.
        // Parameter $userId di-bind dengan aman — tidak ada risiko SQL injection.
        $isMember = $list->members()->where('user_id', $userId)->exists();

        if (!$isMember) {
            abort(403, 'Anda tidak memiliki akses ke daftar ini.');
        }
    }
}