<!DOCTYPE html>
<html>
<head>
    <title>{{ $list->name }}</title>
</head>
<body>
    <h1>{{ $list->name }}</h1>
    <p>{{ $list->description }}</p>

    @if(session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif
    @if(session('error'))
        <p style="color: red;">{{ session('error') }}</p>
    @endif

    <a href="{{ route('lists.edit', $list) }}">Edit</a> |
    <a href="{{ route('lists.index') }}">Kembali ke Daftar</a>

    <hr>

    <h2>Anggota Daftar Ini</h2>
    <ul>
        <li><strong>{{ $list->owner->name }}</strong> (Pemilik)</li>
        @foreach($list->members as $member)
        <li>
            {{ $member->name }} ({{ $member->email }})
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
        </li>
        @endforeach
    </ul>

    <h3>Undang Anggota Baru</h3>
    <form action="{{ route('lists.invite', $list) }}" method="POST">
        @csrf
        <input type="email" name="email" placeholder="Email user yang diundang" required>
        <button type="submit">Undang</button>
    </form>
</body>
</html>