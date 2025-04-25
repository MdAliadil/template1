<nav id="sidebar" class="sidebar js-sidebar mr-8">
  <div class="sidebar-content js-simplebar">
    <a class="sidebar-brand" href="#">
      <h2 style="color:aliceblue;">SKoDash</h2>
    </a>

    <div class="sidebar-user">
      <div class="d-flex justify-content-center">
        <div class="flex-shrink-0">
          <img src="{{ asset('assets/img/a.png') }}"  class="avatar img-fluid rounded me-1" alt="User" />
        </div>
        <div class="flex-grow-1 ps-2">
          <a class="sidebar-user-title dropdown-toggle mx-3" href="#" data-bs-toggle="dropdown">
            {{ ucfirst(Auth::user()->name) }}
          </a>
          <div class="dropdown-menu dropdown-menu-start">
            <a class="dropdown-item" href="{{ route('profile') }}"><i class="align-middle me-1" data-feather="user"></i> Profile</a>
             {{--  <a class="dropdown-item" href="#"><i class="align-middle me-1" data-feather="pie-chart"></i> Analytics</a>  --}}
            {{--  <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="#"><i class="align-middle me-1" data-feather="settings"></i> Settings & Privacy</a>
            <a class="dropdown-item" href="#"><i class="align-middle me-1" data-feather="help-circle"></i> Help Center</a>
            <div class="dropdown-divider"></div>  --}}
            <a class="dropdown-item" href="{{ route('logout') }}"> <i data-feather="log-out" class="align-middle me-1"></i>Log out</a>
          </div>
          <h5 class="ps-1" style="color:grey;">{{ Auth::user()->role->name }}</h5>
        </div>
      </div>
    </div>

    <ul class="sidebar-nav">
      <li class="sidebar-item {{ request()->routeIs('home') ? 'active' : '' }}">
        <a class="sidebar-link" href="{{ route('home') }}">
          <i data-feather="bar-chart-2"></i><span class="align-center">Dashboard</span>
        </a>
      </li>
     
    
         
      

      <li class="sidebar-item {{ request()->routeIs('statement') && (request('type') === 'upi' || request('type') === 'payout') ? 'active' : '' }}">
        <a data-bs-target="#dashboards" data-bs-toggle="collapse" class="sidebar-link {{ request()->routeIs('statement') && (request('type') === 'upi' || request('type') === 'payout') ? '' : 'collapsed' }}">
          <i class="align-middle" data-feather="sliders"></i> <span class="align-middle">Reports</span>
        </a>
        <ul id="dashboards" class="sidebar-dropdown list-unstyled collapse {{ request()->routeIs('statement') && (request('type') === 'upi' || request('type') === 'payout') ? 'show' : '' }}" data-bs-parent="#sidebar">
          <li class="sidebar-item">
            <a class="sidebar-link {{ request('type') === 'upi' ? 'active' : '' }}" href="{{ route('statement', ['type' => 'upi']) }}">UPI Report</a>
          </li>
          <li class="sidebar-item">
            <a class="sidebar-link {{ request('type') === 'payout' ? 'active' : '' }}" href="{{ route('statement', ['type' => 'payout']) }}">Payout Report</a>
          </li>
        </ul>
      </li>

      <li class="sidebar-item {{ request()->routeIs('statement') && (request('type') === 'upiaccount' || request('type') === 'account') ? 'active' : '' }}">
        <a data-bs-target="#pages" data-bs-toggle="collapse" class="sidebar-link {{ request()->routeIs('statement') && (request('type') === 'upiaccount' || request('type') === 'account') ? '' : 'collapsed' }}">
          <i class="align-middle" data-feather="layout"></i> <span class="align-middle">Ledger Statement</span>
        </a>
        <ul id="pages" class="sidebar-dropdown list-unstyled collapse {{ request()->routeIs('statement') && (request('type') === 'upiaccount' || request('type') === 'account') ? 'show' : '' }}" data-bs-parent="#sidebar">
          <li class="sidebar-item">
            <a class="sidebar-link {{ request('type') === 'upiaccount' ? 'active' : '' }}" href="{{ route('statement', ['type' => 'upiaccount']) }}">Payout Wallet</a>
          </li>
          <li class="sidebar-item">
            <a class="sidebar-link {{ request('type') === 'account' ? 'active' : '' }}" href="{{ route('statement', ['type' => 'account']) }}">Payin Wallet</a>
          </li>
        </ul>
      </li>

      <li class="sidebar-item {{ request()->routeIs('apisetup') ? 'active' : '' }}">
        <a class="sidebar-link" href="{{ route('apisetup', ['type' => 'document']) }}">
          <i class="align-middle" data-feather="user"></i> <span class="align-middle">API Document</span>
        </a>
      </li>
    </ul>
  </div>
</nav>



















{{--  <nav id="sidebar" class="sidebar js-sidebar">
  <div class="sidebar-content js-simplebar">
    <a class='sidebar-brand' href='#' >
      <h2 style="color:aliceblue;">SKoDash</h3>
    </a>

    <div class="sidebar-user">
      <div class="d-flex justify-content-center">
        <div class="flex-shrink-0">
          <img src="{{asset('assetsss/img/avatars/avatar.jpg')}}" class="avatar img-fluid rounded me-1" alt="Charles Hall" />
        </div>
        <div class="flex-grow-1 ps-2">
          <a class="sidebar-user-title dropdown-toggle" href="#" data-bs-toggle="dropdown">
            {{ucfirst(Auth::user()->name) }}
           </a>
          <div class="dropdown-menu dropdown-menu-start">
            <a class='dropdown-item' href="{{ route('profile') }}"><i class="align-middle me-1" data-feather="user"></i> Profile</a>
            <a class="dropdown-item" href="#"><i class="align-middle me-1" data-feather="pie-chart"></i> Analytics</a>
            <div class="dropdown-divider"></div>
            <a class='dropdown-item' href='pages-settings.html'><i class="align-middle me-1" data-feather="settings"></i> Settings &
              Privacy</a>
            <a class="dropdown-item" href="#"><i class="align-middle me-1" data-feather="help-circle"></i> Help Center</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="#">Log out</a>
          </div>
         
         
        
            <h5 class="ps-1 " style="color:grey;">{{ Auth::user()->role->name }}</h5>
        
        </div>

      </div>
     
    </div>

    <ul class="sidebar-nav">
      {{--  <li class="sidebar-header">
        UI Elements
      </li>  
      <li class="sidebar-item ">
        <a  class="sidebar-link" href="{{ route('home') }}">
          Dashboard
        </a>
      </li>
      <li class="sidebar-item">
        <a data-bs-target="#dashboards" data-bs-toggle="collapse"  class="sidebar-link collapsed">
          <i class="align-middle" data-feather="sliders"></i> <span class="align-middle">Reports</span>
        </a>
        <ul id="dashboards" class="sidebar-dropdown list-unstyled collapse " data-bs-parent="#sidebar">
          <li class="sidebar-item"><a class='sidebar-link' <a href="{{route('statement', ['type' => 'upi'])}}">UPI Report</a></li>
          <li class="sidebar-item"><a class='sidebar-link' <a href="{{route('statement', ['type' => 'payout'])}}">Payout Report</a></li>
        </ul>
      </li>
      <li class="sidebar-item ">
        <a data-bs-target="#pages" data-bs-toggle="collapse" class="sidebar-link">
          <i class="align-middle" data-feather="layout">Payout Wallet</i> <span class="align-middle">Wallet Managment</span>
        </a>
        <ul id="pages" class="sidebar-dropdown list-unstyled collapse " data-bs-parent="#sidebar">
          <li class="sidebar-item"><a class='sidebar-link' href="{{route('statement', ['type' => 'upiaccount'])}}" >Products List</a></li>
          <li class="sidebar-item"><a class='sidebar-link' href="{{route('statement', ['type' => 'account'])}}">Payi Wallet</a></li>
        </ul>
      </li>

     

     

      <li class="sidebar-item">
        <a class='sidebar-link' href="{{route('apisetup', ['type' => 'document'])}}">
          <i class="align-middle" data-feather="user"></i> <span class="align-middle" >API Document</span>
        </a>
      </li>
  </div>
</nav>  --}}