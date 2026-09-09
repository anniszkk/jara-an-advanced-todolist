<!DOCTYPE html>
<html>
<head>
    <title>{{ $list->name }}</title>
</head>
<body>
    <h1>{{ $list->name }}</h1>
    <p>{{ $list->description }}</p>

    <a href="{{ route('lists.edit', $list) }}">Edit</a> |
    <a href="{{ route('lists.index') }}">Kembali ke Daftar</a>
</body>
</html>