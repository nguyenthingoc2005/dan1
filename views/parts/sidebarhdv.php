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

                <li class="list"><a href="<?= BASEURL ?>?act=Ho_so_canhan" class="nav-link"><i class="bx bx-user icon"></i><span class="link">Hồ sơ cá nhân</span></a></li>
                <li class="list"><a href="<?= BASEURL ?>?act=xemtour" class="nav-link"><i class="bx bx-map icon"></i><span class="link">Lịch làm việc của tôi</span></a></li>


            </ul>

            <div class="bottom-cotent">
                <li class="list"><a href="#" class="nav-link"><i class="bx bx-cog icon"></i><span class="link">Cài đặt</span></a></li>
                <li class="list"><a href="<?= BASEURL ?>?act=logout" class="nav-link"><i class="bx bx-log-out icon"></i><span class="link">Đăng xuất</span></a></li>
            </div>
        </div>
    </div>
</nav>