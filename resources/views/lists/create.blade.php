<!DOCTYPE html>
<html>
<head>
    <title>Buat Daftar Baru</title>
</head>
<body>
    <h1>Buat Daftar Baru</h1>

    <form action="{{ route('lists.store') }}" method="POST">
        @csrf

        <label>Nama Daftar:</label><br>
        <input type="text" name="name" required><br><br>

        <label>Deskripsi:</label><br>
        <textarea name="description"></textarea><br><br>

        <button type="submit">Simpan</button>
    </form>

    <br>
    <a href="{{ route('lists.index') }}">Kembali</a>
</body>
</html>