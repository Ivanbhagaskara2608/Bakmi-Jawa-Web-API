  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-warning navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <li class="nav-item dropdown user-menu">
          <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
              <img src="{{ asset('dist/img/user2-160x160.jpg') }}" class="user-image img-circle elevation-2"
                  alt="User Image">
              <span class="d-none d-md-inline">{{ auth()->user()->nama }}</span>
          </a>
          <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
              <!-- User image -->
              <li class="user-header bg-warning">
                  <img src="{{ asset('dist/img/user2-160x160.jpg') }}" class="img-circle elevation-2"
                      alt="User Image">

                  <p>
                      {{ auth()->user()->nama }}
                  </p>
              </li>
              <!-- Menu Footer-->
              <form action="{{ route('logout') }}" method="" class="user-footer">
                  @csrf
                  <button type="submit" class="btn btn-default btn-flat btn-block">Keluar akun</button>
              </form>
          </ul>
      </li>
  </ul>
  </nav>
  <!-- /.navbar -->