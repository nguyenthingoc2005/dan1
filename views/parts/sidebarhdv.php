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
                <li class="list"><a href="<?= BASEURL ?>?act=dashboard_HDV" class="nav-link"><i class="bx bx-home-alt icon"></i><span class="link">Dashboard HDV</span></a></li>
                <li class="list"><a href="<?= BASEURL ?>?act=xemtour" class="nav-link"><i class="bx bx-user icon"></i><span class="link">Danh Sách tour</span></a></li>
                <li class="list"><a href="<?= BASEURL ?>?act=list_Khach_hang" class="nav-link"><i class="bx bx-building icon"></i><span class="link">Danh Sách Khách Hàng</span></a></li>
                <li class="list"><a href="<?= BASEURL ?>?act=tour_list" class="nav-link"><i class="bx bx-map icon"></i><span class="link">Tour</span></a></li>
                <li class="list"><a href="<?= BASEURL ?>?act=dattourlist " class="nav-link"><i class="bx bx-map icon"></i><span class="link">Quản lí đặt tour</span></a></li>
                <li class="list"><a href="<?= BASEURL ?>?act=user_list" class="nav-link"><i class="bx bx-group"></i><span class="link">Quản lí user</span></a></li>
            </ul>

            <div class="bottom-cotent">
                <li class="list"><a href="#" class="nav-link"><i class="bx bx-cog icon"></i><span class="link">Cài đặt</span></a></li>
                <li class="list"><a href="<?= BASEURL ?>?act=logout" class="nav-link"><i class="bx bx-log-out icon"></i><span class="link">Đăng xuất</span></a></li>
            </div>
        </div>
    </div>
</nav>