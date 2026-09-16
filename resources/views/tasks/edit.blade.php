<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Tugas</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f8;
            margin: 0;
            padding: 40px;
        }

        .container {
            max-width: 600px;
            margin: auto;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
        }

        h1 {
            margin-top: 0;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
        }

        input,
        textarea,
        select {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
        }

        textarea {
            min-height: 100px;
        }

        .btn {
            padding: 10px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        .save {
            background-color: #2563eb;
            color: white;
        }

        .back {
            margin-left: 10px;
        }

        .error {
            background-color: #fee2e2;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 6px;
        }
    </style>
</head>

<body>

<div class="container">

    <h1>Edit Tugas</h1>

    @if ($errors->any())
        <div class="error">
            <strong>Data tidak valid:</strong>

            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('tasks.update', $task->id) }}" method="POST">

        @csrf
        @method('PUT')

        <div class="form-group">
            <label>Daftar</label>

            <select name="list_id" required>
                @foreach ($lists as $list)
                    <option value="{{ $list->id }}"
                        {{ $task->list_id == $list->id ? 'selected' : '' }}>
                        {{ $list->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Judul Tugas</label>

            <input
                type="text"
                name="title"
                value="{{ old('title', $task->title) }}"
                required
            >
        </div>

        <div class="form-group">
            <label>Deskripsi</label>

            <textarea name="description">{{ old('description', $task->description) }}</textarea>
        </div>

        <div class="form-group">
            <label>Prioritas</label>

            <select name="priority" required>
                <option value="rendah"
                    {{ $task->priority == 'rendah' ? 'selected' : '' }}>
                    Rendah
                </option>

                <option value="sedang"
                    {{ $task->priority == 'sedang' ? 'selected' : '' }}>
                    Sedang
                </option>

                <option value="tinggi"
                    {{ $task->priority == 'tinggi' ? 'selected' : '' }}>
                    Tinggi
                </option>
            </select>
        </div>

        <div class="form-group">
            <label>Tenggat</label>

            <input
                type="date"
                name="due_date"
                value="{{ old('due_date', $task->due_date) }}"
            >
        </div>

        <button type="submit" class="btn save">
            Simpan Perubahan
        </button>

        <a href="{{ route('tasks.index') }}" class="back">
            Kembali
        </a>

    </form>

</div>

</body>
</html>