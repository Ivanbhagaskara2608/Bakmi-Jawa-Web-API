  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index3.html" class="brand-link">
      <img src="{{ asset('dist/img/Logo_Bakmi.png') }}" alt="Bakmi Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">Bakmi Jawa Pak Surat</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item">
            <a href="{{ route('dashboard.index') }}" class="nav-link {{ url()->current() == route('dashboard.index') ? 'active' : '' }}">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard
              </p>
            </a>
          </li>
          <li class="nav-item {{ Request::routeIs('pesanan.*') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ Request::routeIs('pesanan.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-shopping-cart"></i>
              <p>
                Pesanan
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item {{ Request::routeIs('pesanan.*') ? 'menu-open' : '' }}">
                <a href="{{ route('pesanan.dinein') }}" class="nav-link {{ Request::routeIs('pesanan.dinein') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Dine In</p>
                </a>
              </li>
              <li class="nav-item {{ Request::routeIs('pesanan.*') ? 'menu-open' : '' }}">
                <a href="{{ route('pesanan.takeaway') }}" class="nav-link {{ Request::routeIs('pesanan.takeaway') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Take Away</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="{{ route('menu.index') }}" class="nav-link {{ Request::routeIs('menu.index') ? 'active' : '' }}">
                <i class="nav-icon fas fa-utensils"></i>
              <p>
                Menu
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('reward.index') }}" class="nav-link {{ Request::routeIs('reward.index') ? 'active' : '' }}">
                <i class="nav-icon fas fa-star"></i>
              <p>
                Rewards
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="nav-icon fas fa-print"></i>
              <p>
                Laporan Penjualan
              </p>
            </a>
          </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>