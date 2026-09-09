<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
</head>
<body>
    <h1>Selamat datang, {{ auth()->user()->name }}!</h1>
    <p>Ini halaman internal, cuma bisa diakses kalau sudah login (dilindungi middleware <code>auth</code>).</p>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Logout</button>
    </form>
</body>
</html>