<div class="app-sidebar__overlay" data-toggle="sidebar"></div>
<aside class="app-sidebar">
  <div class="app-sidebar__user">
    <img class="app-sidebar__user-avatar"
         src="{{ asset('storage/' . $admin->avatar) }}"
         width="50px"
         alt="User Image">
    <div>
      <p class="app-sidebar__user-name"><b>{{ $admin->full_name }}</b></p>
      <p class="app-sidebar__user-designation">Chào mừng bạn trở lại</p>
    </div>
  </div>

  <hr>

  <ul class="app-menu">

    {{-- Dashboard --}}
    <li>
      <a class="app-menu__item {{ request()->is('admin/dashboard') ? 'active' : '' }}"
         href="{{ route('admin.dashboard') }}">
        <i class='app-menu__icon bx bx-cart-alt'></i>
        <span class="app-menu__label">POS Bán Hàng</span>
      </a>
    </li>

    {{-- Nhân viên --}}
    <li>
      <a class="app-menu__item {{ request()->is('admin/accounts*') && !request()->is('admin/accounts/show') ? 'active' : '' }}"
         href="{{ route('admin.accounts.index') }}">
        <i class='app-menu__icon bx bx-id-card'></i>
        <span class="app-menu__label">Quản lý nhân viên</span>
      </a>
    </li>

    {{-- Khách hàng --}}
    <li>
      <a class="app-menu__item {{ request()->is('admin/customers*') ? 'active' : '' }}"
         href="{{ route('admin.customers.index') }}">
        <i class='app-menu__icon bx bx-user-voice'></i>
        <span class="app-menu__label">Quản lý khách hàng</span>
      </a>
    </li>

    {{-- Sản phẩm --}}
    <li>
      <a class="app-menu__item {{ request()->is('admin/products*') ? 'active' : '' }}"
         href="{{ route('admin.products.index') }}">
        <i class='app-menu__icon bx bx-purchase-tag-alt'></i>
        <span class="app-menu__label">Quản lý sản phẩm</span>
      </a>
    </li>

    {{-- Đơn hàng --}}
    <li>
      <a class="app-menu__item {{ request()->is('admin/orders*') ? 'active' : '' }}"
         href="{{ route('admin.orders.index') }}">
        <i class='app-menu__icon bx bx-task'></i>
        <span class="app-menu__label">Quản lý đơn hàng</span>
      </a>
    </li>

    {{-- Chức vụ --}}
    <li>
      <a class="app-menu__item {{ request()->is('admin/roles*') ? 'active' : '' }}"
         href="{{ route('admin.roles.index') }}">
        <i class='app-menu__icon bx bx-shield-quarter'></i>
        <span class="app-menu__label">Quản lý chức vụ</span>
      </a>
    </li>

    {{-- Voucher --}}
    <li>
      <a class="app-menu__item {{ request()->is('admin/promotions*') ? 'active' : '' }}"
         href="{{ route('admin.promotions.index') }}">
        <i class='app-menu__icon bx bx-purchase-tag'></i>
        <span class="app-menu__label">Quản lý voucher</span>
      </a>
    </li>

    {{-- Danh mục --}}
    <li>
      <a class="app-menu__item {{ request()->is('admin/categories*') ? 'active' : '' }}"
         href="{{ route('admin.categories.index') }}">
        <i class='app-menu__icon bx bx-category'></i>
        <span class="app-menu__label">Quản lý danh mục</span>
      </a>
    </li>

    {{-- Tin tức --}}
    <li>
      <a class="app-menu__item {{ request()->is('admin/news*') ? 'active' : '' }}"
         href="{{ route('admin.news.index') }}">
        <i class='app-menu__icon bx bx-news'></i>
        <span class="app-menu__label">Quản lý tin tức</span>
      </a>
    </li>

    {{-- Thuộc tính sản phẩm Pickleball --}}
    <li class="dropdown app-menu__item-wrapper position-relative">
      <a class="app-menu__item dropdown-toggle
        {{ request()->is('admin/materials*')
            || request()->is('admin/weights*')
            || request()->is('admin/grip-sizes*')
            || request()->is('admin/colors*')
            ? 'active' : '' }}"
        href="#">
        <i class='app-menu__icon bx bx-tennis-ball'></i>
        <span class="app-menu__label">Thuộc tính Pickleball</span>
      </a>

      <ul class="dropdown-menu show-on-hover position-absolute w-100">
        <li>
          <a class="dropdown-item {{ request()->is('admin/materials*') ? 'active' : '' }}"
            href="">
            <i class="bx bx-layer me-1"></i> Chất liệu vợt
          </a>
        </li>

        <li>
          <a class="dropdown-item {{ request()->is('admin/weights*') ? 'active' : '' }}"
            href="">
            <i class="bx bx-dumbbell me-1"></i> Trọng lượng vợt
          </a>
        </li>

        <li>
          <a class="dropdown-item {{ request()->is('admin/grip-sizes*') ? 'active' : '' }}"
            href="">
            <i class="bx bx-hand me-1"></i> Kích thước tay cầm
          </a>
        </li>

        <li>
          <a class="dropdown-item {{ request()->is('admin/colors*') ? 'active' : '' }}"
            href="">
            <i class="bx bx-palette me-1"></i> Màu sắc
          </a>
        </li>
      </ul>
    </li>


    {{-- Trang khách --}}
    <li>
      <a class="app-menu__item"
         href="{{ route('home') }}">
        <i class='app-menu__icon bx bx-home'></i>
        <span class="app-menu__label">Trang khách hàng</span>
      </a>
    </li>

    {{-- Profile --}}
    <li>
      <a class="app-menu__item {{ request()->is('admin/accounts/show') ? 'active' : '' }}"
         href="{{ route('admin.profile') }}">
        <i class='app-menu__icon bx bx-user'></i>
        <span class="app-menu__label">Thông tin cá nhân</span>
      </a>
    </li>

  </ul>
</aside>
