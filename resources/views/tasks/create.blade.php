<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Tugas</title>
</head>
<body>

    <h1>Tambah Tugas</h1>

    <form action="{{ route('tasks.store') }}" method="POST">
        @csrf

        <div>
            <label>Daftar</label>
            <select name="list_id" required>
                <option value="">-- Pilih Daftar --</option>

                @foreach ($lists as $list)
                    <option value="{{ $list->id }}">
                        {{ $list->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <br>

        <div>
            <label>Judul Tugas</label>
            <input type="text" name="title" value="{{ old('title') }}" required>
        </div>

        <br>

        <div>
            <label>Deskripsi</label>
            <textarea name="description">{{ old('description') }}</textarea>
        </div>

        <br>

        <div>
            <label>Prioritas</label>
            <select name="priority" required>
                <option value="">-- Pilih Prioritas --</option>
                <option value="rendah">Rendah</option>
                <option value="sedang">Sedang</option>
                <option value="tinggi">Tinggi</option>
            </select>
        </div>

        <br>

        <div>
            <label>Tenggat</label>
            <input type="date" name="due_date" value="{{ old('due_date') }}">
        </div>

        <br>

        <button type="submit">Simpan Tugas</button>

    </form>

    <br>

    <a href="{{ route('tasks.index') }}">Kembali</a>

</body>
</html>