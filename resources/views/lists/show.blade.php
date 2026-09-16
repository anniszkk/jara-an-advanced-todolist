<!DOCTYPE html>
<html>
<head>
    <title>{{ $list->name }}</title>
</head>
<body>
    <h1>{{ $list->name }}</h1>
    <p>{{ $list->description }}</p>
    <p><strong>Pemilik:</strong> {{ $list->owner->name }}</p>

    @if(session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif
    @if(session('error'))
        <p style="color: red;">{{ session('error') }}</p>
    @endif

    {{-- Tombol Edit & Hapus hanya tampil untuk pemilik (SRS003) --}}
    @if($list->owner_id === auth()->id())
        <a href="{{ route('lists.edit', $list) }}">Edit</a> |
        <form action="{{ route('lists.destroy', $list) }}" method="POST" style="display:inline">
            @csrf
            @method('DELETE')
            <button type="submit" onclick="return confirm('Yakin hapus daftar ini beserta semua tugas dan anggotanya?')">Hapus Daftar</button>
        </form>
        |
    @endif
    <a href="{{ route('lists.index') }}">Kembali ke Daftar</a>

    <hr>

    {{-- Progress tugas (SRS006 / SRS003 display) --}}
    @php
        $totalTasks = $list->tasks->count();
        $doneTasks  = $list->tasks->where('status', 'selesai')->count();
    @endphp
    <h2>Progress Tugas</h2>
    <p>{{ $doneTasks }} dari {{ $totalTasks }} tugas selesai
        @if($totalTasks > 0)
            ({{ round(($doneTasks / $totalTasks) * 100) }}%)
        @endif
    </p>
    <a href="{{ route('tasks.create', ['list_id' => $list->id]) }}">+ Tambah Tugas</a>

    <hr>

    <h2>Anggota Daftar Ini</h2>
    <ul>
        <li><strong>{{ $list->owner->name }}</strong> (Pemilik)</li>
        @foreach($list->members as $member)
        <li>
            {{ $member->name }} ({{ $member->email }})
            {{-- Tombol kelola anggota hanya untuk pemilik (SRS004) --}}
            @if($list->owner_id === auth()->id())
                <form action="{{ route('lists.members.remove', [$list, $member]) }}" method="POST" style="display:inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Keluarkan anggota ini?')">Keluarkan</button>
                </form>
                <form action="{{ route('lists.transfer', $list) }}" method="POST" style="display:inline">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ $member->id }}">
                    <button type="submit" onclick="return confirm('Alihkan kepemilikan ke user ini?')">Jadikan Pemilik</button>
                </form>
            @endif
        </li>
        @endforeach
    </ul>

    {{-- Form undang anggota hanya untuk pemilik (SRS004) --}}
    @if($list->owner_id === auth()->id())
        <h3>Undang Anggota Baru</h3>
        <form action="{{ route('lists.invite', $list) }}" method="POST">
            @csrf
            <input type="email" name="email" placeholder="Email user yang diundang" required>
            <button type="submit">Undang</button>
        </form>
    @endif
</body>
</html>