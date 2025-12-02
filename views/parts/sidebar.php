<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<nav>
  <div class="logo">
    <i class="bx bx-menu menu-icon"></i>
    <span class="logo-name">Trang quản trị</span>
  </div>

  <div class="sidebar" aria-hidden="true">
    <!-- <div class="logo">
      <i class="bx bx-menu menu-icon"></i>
      <span class="logo-name">Trang quản trị</span>
    </div> -->
    <div class="sidebar-content">
      <ul class="lists">
        <li class="list"><a href="<?= BASEURL ?>?act=dashboard" class="nav-link"><i class="bx bx-home-alt icon"></i><span class="link">Dashboard</span></a></li>
        <li class="list"><a href="<?= BASEURL ?>?act=ncc_list" class="nav-link"><i class="bx bx-building icon"></i><span class="link">Nhà cung cấp</span></a></li>
        <li class="list"><a href="<?= BASEURL ?>?act=tour_list" class="nav-link"><i class="bx bx-map-alt icon"></i><span class="link">Tour</span></a></li>
        <li class="list"><a href="<?= BASEURL ?>?act=dattourlist "class="nav-link"><i class="bx bx-calendar-check icon"></i><span class="link">Quản lí đặt tour</span></a></li>
        <li class="list"><a href="<?= BASEURL ?>?act=user_list" class="nav-link"><i class="bx bx-shield-alt-2"></i><span class="link">Quản lí tài khoản</span></a></li>
        <li class="list"><a href="<?= BASEURL ?>?act=hdv" class="nav-link"><i class="bx bx-user icon"></i><span class="link">Hướng dẫn viên</span></a></li>
        <li class="list"><a href="<?= BASEURL ?>?act=khachhang_list" class="nav-link"><i class="bx bx-user-circle"></i><span class="link">Khách hàng</span></a></li>
      </ul>

      <div class="bottom-cotent">
        <li class="list"><a href="#" class="nav-link"><i class="bx bx-cog icon"></i><span class="link">Cài đặt</span></a></li>
        <li class="list"><a href="<?= BASEURL ?>?act=logout" class="nav-link"><i class="bx bx-log-out icon"></i><span class="link">Đăng xuất</span></a></li>
      </div>
    </div>
  </div>
</nav>