<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập</title>
</head>
<body>

<h2>Đăng nhập</h2>

{{-- Hiển thị lỗi --}}
@if ($errors->any())
    <ul style="color:red">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
@endif

{{-- Thông báo thành công --}}
@if (session('success'))
    <p style="color:green">{{ session('success') }}</p>
@endif

<form method="POST" action="{{ route('login') }}">
    @csrf

    <div>
        <label>Email</label><br>
        <input type="email" name="email" required>
    </div>

    <div>
        <label>Mật khẩu</label><br>
        <input type="password" name="password" required>
    </div>

    <button type="submit">Đăng nhập</button>
</form>

<p>
    Chưa có tài khoản?
    <a href="{{ route('register') }}">Đăng ký</a>
</p>

</body>
</html>