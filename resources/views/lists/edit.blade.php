<!DOCTYPE html>
<html>
<head>
    <title>Edit Daftar</title>
</head>
<body>
    <h1>Edit Daftar</h1>

    <form action="{{ route('lists.update', $list) }}" method="POST">
        @csrf
        @method('PUT')

        <label>Nama Daftar:</label><br>
        <input type="text" name="name" value="{{ $list->name }}" required><br><br>

        <label>Deskripsi:</label><br>
        <textarea name="description">{{ $list->description }}</textarea><br><br>

        <button type="submit">Update</button>
    </form>

    <br>
    <a href="{{ route('lists.index') }}">Kembali</a>
</body>
</html>