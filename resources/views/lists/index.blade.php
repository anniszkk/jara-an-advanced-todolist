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
    @if(session('error'))
        <p style="color: red;">{{ session('error') }}</p>
    @endif

    <a href="{{ route('lists.create') }}">+ Buat Daftar Baru</a>

    {{-- ===== SRS003: Daftar yang dimiliki ===== --}}
    <h2>Daftar Milik Saya</h2>
    <table border="1" cellpadding="8" style="margin-top: 10px;">
        <tr>
            <th>Nama</th>
            <th>Deskripsi</th>
            <th>Progress Tugas</th>
            <th>Aksi</th>
        </tr>
        @forelse($ownedLists as $list)
        <tr>
            <td>{{ $list->name }}</td>
            <td>{{ $list->description }}</td>
            <td>{{ $list->done_count }} / {{ $list->tasks_count }} selesai</td>
            <td>
                <a href="{{ route('lists.show', $list) }}">Lihat</a> |
                <a href="{{ route('lists.edit', $list) }}">Edit</a> |
                <form action="{{ route('lists.destroy', $list) }}" method="POST" style="display:inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Yakin hapus daftar ini beserta semua tugas dan anggotanya?')">Hapus</button>
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="4">Belum ada daftar yang kamu miliki.</td></tr>
        @endforelse
    </table>

    {{-- ===== SRS003: Daftar yang diikuti sebagai anggota ===== --}}
    <h2 style="margin-top: 30px;">Daftar yang Saya Ikuti</h2>
    <table border="1" cellpadding="8" style="margin-top: 10px;">
        <tr>
            <th>Nama</th>
            <th>Deskripsi</th>
            <th>Progress Tugas</th>
            <th>Aksi</th>
        </tr>
        @forelse($memberLists as $list)
        <tr>
            <td>{{ $list->name }}</td>
            <td>{{ $list->description }}</td>
            <td>{{ $list->done_count }} / {{ $list->tasks_count }} selesai</td>
            <td>
                <a href="{{ route('lists.show', $list) }}">Lihat</a>
            </td>
        </tr>
        @empty
        <tr><td colspan="4">Kamu belum diundang ke daftar manapun.</td></tr>
        @endforelse
    </table>
</body>
</html>