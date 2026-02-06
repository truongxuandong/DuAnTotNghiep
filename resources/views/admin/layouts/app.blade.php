<!DOCTYPE html>
<html lang="en">
<head>
  <title>@yield('title', 'Trang quản trị')</title>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Main CSS -->
  <link rel="stylesheet" href="{{ asset('css/main.css') }}">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
  <style>
    /* Thu gọn thanh header admin */
    .admin-header-compact { min-height: 48px !important; padding-top: 0.25rem !important; padding-bottom: 0.25rem !important; }
    .admin-header-compact .navbar-brand { font-size: 1rem; }
    .admin-header-compact .nav-link { padding-top: 0.35rem !important; padding-bottom: 0.35rem !important; }
    .admin-header-compact .nav-link img { width: 26px !important; height: 26px !important; }

    /* Layout: Sidebar trái, Header + Content phải — không vỡ layout */
    .app-right-wrapper {
      width: 100%;
      min-width: 0;
      box-sizing: border-box;
      overflow-x: hidden;
    }
    @media (min-width: 768px) {
      .app-right-wrapper {
        margin-left: 250px;
        width: calc(100% - 250px);
      }
      .app-right-wrapper .app-content { margin-left: 0 !important; }
    }
    @media (min-width: 768px) {
      .sidebar-mini.sidenav-toggled .app-right-wrapper {
        margin-left: 50px;
        width: calc(100% - 50px);
      }
    }
    @media (max-width: 767px) {
      .app-right-wrapper .app-content { margin-left: 0 !important; margin-top: 10px; }
    }
    /* Header full trong vùng bên phải, ô thông báo + tài khoản căn phải */
    .app-right-wrapper .admin-header-compact {
      width: 100%;
      min-width: 0;
      border-radius: 0;
      box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    }
    .app-right-wrapper .navbar-collapse { flex-grow: 1; justify-content: flex-end; }
    @media (min-width: 768px) {
      .app-right-wrapper .navbar-collapse { display: flex !important; }
    }
    /* Khoảng cách 3 nút header: Thông báo | Đổi sáng/tối | Admin */
    .header-nav-actions .header-nav-item { margin-left: 1.25rem; }
    .header-nav-actions .header-nav-item:first-child { margin-left: 0; }
    .header-nav-actions .nav-link { padding-left: 0.5rem !important; padding-right: 0.5rem !important; }

    /* Chế độ tối (đêm) */
    body.admin-dark-mode { background: #1a1d21; color: #e4e6eb; }
    body.admin-dark-mode .app-right-wrapper .admin-header-compact { background: #242628 !important; border-color: #3e4245 !important; }
    body.admin-dark-mode .app-right-wrapper .admin-header-compact .navbar-brand,
    body.admin-dark-mode .app-right-wrapper .admin-header-compact .nav-link,
    body.admin-dark-mode .app-right-wrapper .admin-header-compact .btn-link { color: #e4e6eb !important; }
    body.admin-dark-mode .app-right-wrapper .app-content { background: #1a1d21 !important; color: #e4e6eb; }
    body.admin-dark-mode .app-right-wrapper .card { background: #242628; border-color: #3e4245; color: #e4e6eb; }
    body.admin-dark-mode .app-right-wrapper .card-header { background: rgba(36,38,40,0.9) !important; border-color: #3e4245; color: #e4e6eb; }
    body.admin-dark-mode .app-right-wrapper .dropdown-menu { background: #242628; border-color: #3e4245; }
    body.admin-dark-mode .app-right-wrapper .dropdown-item { color: #e4e6eb; }
    body.admin-dark-mode .app-right-wrapper .dropdown-item:hover { background: #3e4245; color: #fff; }
    /* Logo Pickleball: chế độ tối dùng màu vàng (bóng pickleball) */
    body.admin-dark-mode .navbar-brand-logo { color: #f0c14b; }
    body.admin-dark-mode .navbar-brand-logo svg circle[fill="var(--bs-body-bg, #fff)"],
    body.admin-dark-mode .navbar-brand-logo svg circle[stroke] { stroke: #1a1d21; fill: #1a1d21; }
  </style>
</head>

<body onload="time(); initDayNight();" class="app sidebar-mini">
  <!-- Sidebar (cố định bên trái) -->
  @include('admin.layouts.sidebar')

  <!-- Vùng bên phải: chỉ có Header + Nội dung (không đè lên sidebar) -->
  <div class="app-right-wrapper">
    <!-- Header chỉ nằm trong khoảng trắng bên phải -->
    @include('admin.layouts.header')

    <!-- Main content -->
    <main class="app-content">
      @yield('content')
      <div class="text-center" style="font-size: 13px">
        <p><b>&copy; <script>document.write(new Date().getFullYear());</script> PBall Store</b></p>
      </div>
    </main>
  </div>

  <!-- Scripts -->
  <script src="{{ asset('js/jquery-3.2.1.min.js') }}"></script>
  <script src="{{ asset('js/popper.min.js') }}"></script>
  <script src="https://unpkg.com/boxicons@latest/dist/boxicons.js"></script>
  <script src="{{ asset('js/bootstrap.min.js') }}"></script>
  <script src="{{ asset('js/main.js') }}"></script>
  <script src="{{ asset('js/plugins/pace.min.js') }}"></script>
  <script src="{{ asset('js/plugins/chart.js') }}"></script>

  @yield('scripts')

  <script>
    function time() {
      var today = new Date();
      var weekday = ["Chủ Nhật", "Thứ Hai", "Thứ Ba", "Thứ Tư", "Thứ Năm", "Thứ Sáu", "Thứ Bảy"];
      var day = weekday[today.getDay()];
      var dd = today.getDate();
      var mm = today.getMonth() + 1;
      var yyyy = today.getFullYear();
      var h = today.getHours();
      var m = today.getMinutes();
      var s = today.getSeconds();
      m = checkTime(m); s = checkTime(s);
      var nowTime = h + " giờ " + m + " phút " + s + " giây";
      if (dd < 10) dd = '0' + dd;
      if (mm < 10) mm = '0' + mm;
      var formatted = `<span class="date"> ${day}, ${dd}/${mm}/${yyyy} - ${nowTime}</span>`;
            document.addEventListener("DOMContentLoaded", function() {
    document.getElementById("myElement").innerHTML = "Hello!";
});

      setTimeout(time, 1000);
    }
    function checkTime(i) {
      return (i < 10) ? "0" + i : i;
    }

    /* Đổi web theo ngày/đêm */
    function initDayNight() {
      var isDark = localStorage.getItem('adminDarkMode') === '1';
      if (isDark) document.body.classList.add('admin-dark-mode');
      updateDayNightIcon(isDark);
      var btn = document.getElementById('toggleDayNight');
      if (btn) btn.addEventListener('click', function() {
        document.body.classList.toggle('admin-dark-mode');
        var dark = document.body.classList.contains('admin-dark-mode');
        localStorage.setItem('adminDarkMode', dark ? '1' : '0');
        updateDayNightIcon(dark);
      });
    }
    function updateDayNightIcon(isDark) {
      var icon = document.getElementById('iconDayNight');
      if (!icon) return;
      icon.className = isDark ? 'fas fa-sun' : 'fas fa-moon';
      icon.setAttribute('title', isDark ? 'Bật chế độ sáng' : 'Đổi web theo ngày đêm');
    }
  </script>
</body>
</html>