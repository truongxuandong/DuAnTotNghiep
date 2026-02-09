@auth
    Xin chào {{ auth()->user()->name }}

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Đăng xuất</button>
    </form>
@endauth

@guest
    <a href="{{ route('login') }}">Đăng nhập</a>
@endguest

@if (session('success'))
    <p style="color:green">{{ session('success') }}</p>
@endif

@if ($errors->any())
    <ul style="color:red">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
@endif