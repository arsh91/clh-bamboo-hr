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
      <li class="nav-item">
        <a class="nav-link {{ request()->is('folder') ? '' : 'collapsed' }} " href="{{ url('/folder') }}">
          <i class="bi bi-folder2-open"></i>
          <span>Folder</span>
        </a>
      </li>
    </ul>

  </aside>
  <!-- End Sidebar-->