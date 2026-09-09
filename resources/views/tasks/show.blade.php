<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Tugas</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f8;
            margin: 0;
            padding: 40px;
        }

        .container {
            max-width: 700px;
            margin: auto;
        }

        .card {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
        }

        .info {
            margin: 15px 0;
        }

        .btn {
            display: inline-block;
            padding: 10px 16px;
            border-radius: 6px;
            text-decoration: none;
            background-color: #2563eb;
            color: white;
        }
    </style>
</head>

<body>

<div class="container">

    <div class="card">

        <h1>{{ $task->title }}</h1>

        <div class="info">
            <strong>Deskripsi:</strong>
            <p>{{ $task->description ?? '-' }}</p>
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

        <br>

        <a href="{{ route('tasks.edit', $task->id) }}" class="btn">
            Edit Tugas
        </a>

        <a href="{{ route('tasks.index') }}">
            Kembali
        </a>

    </div>

</div>

</body>
</html>