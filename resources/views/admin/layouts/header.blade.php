<header class="navbar navbar-expand-md navbar-light bg-white border-bottom shadow-sm sticky-top px-3 admin-header-compact w-100">
    <a class="navbar-brand fw-bold text-primary py-0 d-flex align-items-center" href="{{ url('/admin') }}" title="PBall Store - Pickleball">
        <span class="navbar-brand-logo me-2" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" width="30" height="30" fill="currentColor" role="img" aria-label="Pickleball logo">
                <!-- Quả bóng pickleball (có lỗ) -->
                <circle cx="16" cy="16" r="14" fill="currentColor" opacity="0.9"/>
                <circle cx="16" cy="16" r="10" fill="transparent" stroke="var(--bs-body-bg, #fff)" stroke-width="1.5"/>
                <circle cx="16" cy="10" r="2" fill="var(--bs-body-bg, #fff)"/>
                <circle cx="16" cy="22" r="2" fill="var(--bs-body-bg, #fff)"/>
                <circle cx="10" cy="16" r="2" fill="var(--bs-body-bg, #fff)"/>
                <circle cx="22" cy="16" r="2" fill="var(--bs-body-bg, #fff)"/>
                <circle cx="12.5" cy="12.5" r="1.5" fill="var(--bs-body-bg, #fff)"/>
                <circle cx="19.5" cy="12.5" r="1.5" fill="var(--bs-body-bg, #fff)"/>
                <circle cx="12.5" cy="19.5" r="1.5" fill="var(--bs-body-bg, #fff)"/>
                <circle cx="19.5" cy="19.5" r="1.5" fill="var(--bs-body-bg, #fff)"/>
            </svg>
        </span>
        PBall Store
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar"
        aria-controls="adminNavbar" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-end" id="adminNavbar">
        <ul class="navbar-nav align-items-center header-nav-actions">
            <!-- Nút Thông báo -->
            <li class="nav-item header-nav-item">
                <a class="nav-link text-dark" href="#" title="Thông báo">
                    <i class="fas fa-bell"></i>
                </a>
            </li>

            <!-- Đổi web theo ngày/đêm (chỉ icon) -->
            <li class="nav-item header-nav-item">
                <button type="button" class="nav-link text-dark border-0 bg-transparent" id="toggleDayNight" title="Đổi web theo ngày đêm" aria-label="Đổi web theo ngày đêm" style="cursor:pointer;">
                    <i class="fas fa-moon" id="iconDayNight"></i>
                </button>
            </li>

            <!-- Tài khoản -->
            <li class="nav-item header-nav-item dropdown">
                <a class="nav-link dropdown-toggle text-dark" href="#" id="adminDropdown" role="button"
                   data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="https://i.pravatar.cc/30" class="rounded-circle me-1" width="30" height="30" alt="Avatar">
                    Admin
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="adminDropdown">
                    <li><a class="dropdown-item" href="#">Hồ sơ</a></li>
                    <li><a class="dropdown-item" href="#">Cài đặt</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="#">Đăng xuất</a></li>
                </ul>
            </li>
        </ul>
    </div>
</header>
