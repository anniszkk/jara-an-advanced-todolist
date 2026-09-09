<!DOCTYPE html>
<html>
<head>
    <title>Daftar Saya</title>
</head>
<body>
    <h1>Daftar Saya</h1>

    @if(session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    <a href="{{ route('lists.create') }}">+ Buat Daftar Baru</a>

    <table border="1" cellpadding="8" style="margin-top: 10px;">
        <tr>
            <th>Nama</th>
            <th>Deskripsi</th>
            <th>Aksi</th>
        </tr>
        @forelse($lists as $list)
        <tr>
            <td>{{ $list->name }}</td>
            <td>{{ $list->description }}</td>
            <td>
                <a href="{{ route('lists.show', $list) }}">Lihat</a> |
                <a href="{{ route('lists.edit', $list) }}">Edit</a> |
                <form action="{{ route('lists.destroy', $list) }}" method="POST" style="display:inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Yakin hapus daftar ini?')">Hapus</button>
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="3">Belum ada daftar.</td></tr>
        @endforelse
    </table>
</body>
</html>