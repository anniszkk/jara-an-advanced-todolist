<?php

namespace App\Http\Controllers;

use App\Models\TaskList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ListController extends Controller
{
    // Nampilin semua daftar milik user yang login
    public function index()
    {
        $lists = TaskList::where('owner_id', Auth::id())->get();
        return view('lists.index', compact('lists'));
    }

    // Form bikin daftar baru
    public function create()
    {
        return view('lists.create');
    }

    // Simpan daftar baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        TaskList::create([
            'name' => $request->name,
            'description' => $request->description,
            'owner_id' => Auth::id(),
        ]);

        return redirect()->route('lists.index')->with('success', 'Daftar berhasil dibuat!');
    }

    // Lihat detail 1 daftar
    public function show(TaskList $list)
    {
        $this->authorizeOwner($list);
        return view('lists.show', compact('list'));
    }

    // Form edit daftar
    public function edit(TaskList $list)
    {
        $this->authorizeOwner($list);
        return view('lists.edit', compact('list'));
    }

    // Simpan hasil edit
    public function update(Request $request, TaskList $list)
    {
        $this->authorizeOwner($list);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $list->update($request->only('name', 'description'));

        return redirect()->route('lists.index')->with('success', 'Daftar berhasil diperbarui!');
    }

    // Hapus daftar
    public function destroy(TaskList $list)
    {
        $this->authorizeOwner($list);
        $list->delete();

        return redirect()->route('lists.index')->with('success', 'Daftar berhasil dihapus!');
    }

    // Helper: cek apakah user yang login adalah pemilik daftar ini
    private function authorizeOwner(TaskList $list)
    {
        if ($list->owner_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke daftar ini.');
        }
    }
}