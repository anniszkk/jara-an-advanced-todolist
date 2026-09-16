<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Tugas</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f8;
            margin: 0;
            padding: 40px;
        }

        .container {
            max-width: 900px;
            margin: auto;
        }

        h1 {
            margin-bottom: 20px;
        }

        .btn {
            display: inline-block;
            padding: 10px 16px;
            text-decoration: none;
            border-radius: 6px;
            background-color: #2563eb;
            color: white;
            margin-bottom: 20px;
        }

        .task-card {
            background-color: white;
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .task-card h3 {
            margin-top: 0;
        }

        .info {
            margin: 8px 0;
        }

        .actions {
            margin-top: 15px;
        }

        .actions a,
        .actions button {
            padding: 7px 12px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            margin-right: 5px;
        }

        .view {
            background-color: #6b7280;
            color: white;
        }

        .edit {
            background-color: #f59e0b;
            color: white;
        }

        .delete {
            background-color: #dc2626;
            color: white;
        }

        .success {
            background-color: #dcfce7;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .empty {
            background-color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px;
        }
    </style>
</head>

<body>

<div class="container">

    <h1>Daftar Tugas</h1>

    @if (session('success'))
        <div class="success">
            {{ session('success') }}
        </div>
    @endif

    <a href="{{ route('tasks.create') }}" class="btn">
        + Tambah Tugas
    </a>

    @if ($tasks->count() > 0)

        @foreach ($tasks as $task)

            <div class="task-card">

                <h3>{{ $task->title }}</h3>

                <div class="info">
                    <strong>Deskripsi:</strong>
                    {{ $task->description ?? '-' }}
                </div>

                <div class="info">
                    <strong>Prioritas:</strong>
                    {{ $task->priority }}
                </div>

                <div class="info">
                    <strong>Tenggat:</strong>
                    {{ $task->due_date ?? '-' }}
                </div>

                <div class="info">
                    <strong>Daftar:</strong>
                    {{ $task->list->name ?? '-' }}
                </div>

                <div class="actions">

                    <a href="{{ route('tasks.show', $task->id) }}"
                       class="view">
                        Lihat
                    </a>

                    <a href="{{ route('tasks.edit', $task->id) }}"
                       class="edit">
                        Edit
                    </a>

                    <form action="{{ route('tasks.destroy', $task->id) }}"
                          method="POST"
                          style="display:inline">

                        @csrf
                        @method('DELETE')

                        <button type="submit" class="delete">
                            Hapus
                        </button>

                    </form>

                </div>

            </div>

        @endforeach

    @else

        <div class="empty">
            <p>Belum ada tugas.</p>
            <p>Silakan tambahkan tugas baru.</p>
        </div>

    @endif

</div>

</body>
</html>