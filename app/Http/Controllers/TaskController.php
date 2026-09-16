<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskList;
use Illuminate\Http\Request;


class TaskController extends Controller
{
    /**
     * Menampilkan semua tugas.
     */
    public function index()
    {
        $tasks = Task::with('list')
        ->whereHas('list', function ($query) {
            $query->where('owner_id', auth()->id())
                ->orWhereHas('members', function ($memberQuery) {
                    $memberQuery->where('users.id', auth()->id());
                });
        })
        ->get();
        $lists = TaskList::with('tasks')->get();

        return view('tasks.index', compact('tasks', 'lists'));
    }

    /**
     * Menampilkan form tambah tugas.
     */
    public function create()
    {
        $lists = TaskList::all();

        return view('tasks.create', compact('lists'));
    }

    /**
     * Menyimpan tugas baru.
     */
    public function store(Request $request)
    {   //Validasi seluruh input tugas sebelum diproses ke database.
        // Input dicek berdasarkan tipe, panjang, nilai yang diperbolehkan,dan keberadaan data yang direferensikan.
        $validated = $request->validate([
            'list_id' => 'required|exists:lists,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:rendah,sedang,tinggi',
            'due_date' => 'nullable|date',
        ]);

        // Menggunakan Eloquent dengan data yang telah divalidasi,sehingga input tidak digabungkan langsung ke query SQL.
        Task::create($validated);

        return redirect()->route('tasks.index')
            ->with('success', 'Tugas berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail tugas.
     */
    public function show(string $id)
    {
        $task = Task::with('list')->findOrFail($id);

        $this->authorizeTask($task);

        return view('tasks.show', compact('task'));
    }

    /**
     * Menampilkan form edit tugas.
     */
    public function edit(string $id)
    {
        $task = Task::with('list')->findOrFail($id);

        $this->authorizeTask($task);

        $lists = TaskList::where('owner_id', auth()->id())->get();

        return view('tasks.edit', compact('task', 'lists'));
    }

    /**
     * Memperbarui tugas.
     */
    public function update(Request $request, string $id)
    {
        $task = Task::findOrFail($id);
        $this->authorizeTask($task);
        // Memvalidasi input perubahan tugas sebelum diperbarui di database.
        $validated = $request->validate([
            'list_id' => 'required|exists:lists,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:rendah,sedang,tinggi',
            'due_date' => 'nullable|date',
        ]);

        $task->update($validated);

        return redirect()->route('tasks.index')
            ->with('success', 'Tugas berhasil diperbarui.');
    }

    /**
     * Menghapus tugas.
     */
    public function destroy(string $id)
    {
        $task = Task::findOrFail($id);

        $this->authorizeTask($task);

        $task->delete();

        return redirect()->route('tasks.index')
            ->with('success', 'Tugas berhasil dihapus.');
    }

    public function updateStatus(Request $request, string $id)
    {
        $task = Task::findOrFail($id);
        $this->authorizeTask($task);
        $validated = $request->validate([
            'status' => 'required|in:belum,dikerjakan,selesai',
        ]);

        $task->update($validated);

        return redirect()->route('tasks.index')
            ->with('success', 'Status tugas berhasil diperbarui.');
    }

    private function authorizeTask(Task $task)
    {
        $user = auth()->user();

        $hasAccess = $task->list->owner_id === $user->id
            || $task->list->members()->where('user_id', $user->id)->exists();

        if (!$hasAccess) {
            abort(403, 'Anda tidak memiliki akses ke tugas ini.');
        }
    }

}