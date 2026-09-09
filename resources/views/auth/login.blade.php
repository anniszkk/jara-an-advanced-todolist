<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <style>
        body { font-family: sans-serif; max-width: 400px; margin: 60px auto; }
        .error { color: red; font-size: 0.9em; }
        input { display: block; width: 100%; margin-bottom: 10px; padding: 8px; box-sizing: border-box; }
        button { padding: 8px 16px; }
    </style>
</head>
<body>
    <h1>Login</h1>

    @if ($errors->any())
        <div class="error">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <label>Email</label>
        <input type="email" name="email" value="{{ old('email') }}" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <label style="display:flex; align-items:center; gap:6px;">
            <input type="checkbox" name="remember" style="width:auto;"> Ingat saya
        </label>

        <button type="submit">Login</button>
    </form>

    <p>Belum punya akun? <a href="{{ route('register') }}">Daftar di sini</a></p>
</body>
</html>