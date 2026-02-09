<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký</title>
</head>
<body>

<h2>Đăng ký</h2>

{{-- Hiển thị lỗi --}}
@if ($errors->any())
    <ul style="color:red">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
@endif

<form method="POST" action="{{ route('register') }}">
    @csrf

    <div>
        <label>Tên</label><br>
        <input type="text" name="name" required>
    </div>

    <div>
        <label>Email</label><br>
        <input type="email" name="email" required>
    </div>

    <div>
        <label>Mật khẩu</label><br>
        <input type="password" name="password" required>
    </div>

    <div>
        <label>Nhập lại mật khẩu</label><br>
        <input type="password" name="password_confirmation" required>
    </div>

    <button type="submit">Đăng ký</button>
</form>

<p>
    Đã có tài khoản?
    <a href="{{ route('login') }}">Đăng nhập</a>
</p>

</body>
</html>