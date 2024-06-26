  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">
      <li class="nav-item">
        <a class="nav-link {{ request()->is('dashboard') ? '' : 'collapsed' }} " href="{{ url('/dashboard') }}">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li>
      <!-- End Dashboard Nav -->
      @if(auth()->user()->role->name == 'SUPER_ADMIN')
      <li class="nav-item">
        <a class="nav-link {{ request()->is('users') ? '' : 'collapsed' }} " href="{{ url('/users') }}">
          <i class="bi bi-person"></i>
          <span>Users</span>
        </a>
      </li>
      @endif

    </ul>

  </aside>
  <!-- End Sidebar-->