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
        $tasks = Task::with('list')->get();

        return view('tasks.index', compact('tasks'));
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
    {
        $validated = $request->validate([
            'list_id' => 'required|exists:lists,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:rendah,sedang,tinggi',
            'due_date' => 'nullable|date',
        ]);

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

        return view('tasks.show', compact('task'));
    }

    /**
     * Menampilkan form edit tugas.
     */
    public function edit(string $id)
    {
        $task = Task::findOrFail($id);
        $lists = TaskList::all();

        return view('tasks.edit', compact('task', 'lists'));
    }

    /**
     * Memperbarui tugas.
     */
    public function update(Request $request, string $id)
    {
        $task = Task::findOrFail($id);

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

        $task->delete();

        return redirect()->route('tasks.index')
            ->with('success', 'Tugas berhasil dihapus.');
    }
}