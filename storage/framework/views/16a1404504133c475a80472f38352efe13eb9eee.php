<nav id="sidebar" class="sidebar js-sidebar mr-8">
  <div class="sidebar-content js-simplebar">
    <a class="sidebar-brand" href="#">
      <h2 style="color:aliceblue;">SKoDash</h2>
    </a>

    <div class="sidebar-user">
      <div class="d-flex justify-content-center">
        <div class="flex-shrink-0">
          <img src="<?php echo e(asset('assets/img/a.png')); ?>"  class="avatar img-fluid rounded me-1" alt="User" />
        </div>
        <div class="flex-grow-1 ps-2">
          <a class="sidebar-user-title dropdown-toggle mx-3" href="#" data-bs-toggle="dropdown">
            <?php echo e(ucfirst(Auth::user()->name)); ?>

          </a>
          <div class="dropdown-menu dropdown-menu-start">
            <a class="dropdown-item" href="<?php echo e(route('profile')); ?>"><i class="align-middle me-1" data-feather="user"></i> Profile</a>
             
            
            <a class="dropdown-item" href="<?php echo e(route('logout')); ?>"> <i data-feather="log-out" class="align-middle me-1"></i>Log out</a>
          </div>
          <h5 class="ps-1" style="color:grey;"><?php echo e(Auth::user()->role->name); ?></h5>
        </div>
      </div>
    </div>

    <ul class="sidebar-nav">
      <li class="sidebar-item <?php echo e(request()->routeIs('home') ? 'active' : ''); ?>">
        <a class="sidebar-link" href="<?php echo e(route('home')); ?>">
          <i data-feather="bar-chart-2"></i><span class="align-center">Dashboard</span>
        </a>
      </li>
     
    
         
      

      <li class="sidebar-item <?php echo e(request()->routeIs('statement') && (request('type') === 'upi' || request('type') === 'payout') ? 'active' : ''); ?>">
        <a data-bs-target="#dashboards" data-bs-toggle="collapse" class="sidebar-link <?php echo e(request()->routeIs('statement') && (request('type') === 'upi' || request('type') === 'payout') ? '' : 'collapsed'); ?>">
          <i class="align-middle" data-feather="sliders"></i> <span class="align-middle">Reports</span>
        </a>
        <ul id="dashboards" class="sidebar-dropdown list-unstyled collapse <?php echo e(request()->routeIs('statement') && (request('type') === 'upi' || request('type') === 'payout') ? 'show' : ''); ?>" data-bs-parent="#sidebar">
          <li class="sidebar-item">
            <a class="sidebar-link <?php echo e(request('type') === 'upi' ? 'active' : ''); ?>" href="<?php echo e(route('statement', ['type' => 'upi'])); ?>">UPI Report</a>
          </li>
          <li class="sidebar-item">
            <a class="sidebar-link <?php echo e(request('type') === 'payout' ? 'active' : ''); ?>" href="<?php echo e(route('statement', ['type' => 'payout'])); ?>">Payout Report</a>
          </li>
        </ul>
      </li>

      <li class="sidebar-item <?php echo e(request()->routeIs('statement') && (request('type') === 'upiaccount' || request('type') === 'account') ? 'active' : ''); ?>">
        <a data-bs-target="#pages" data-bs-toggle="collapse" class="sidebar-link <?php echo e(request()->routeIs('statement') && (request('type') === 'upiaccount' || request('type') === 'account') ? '' : 'collapsed'); ?>">
          <i class="align-middle" data-feather="layout"></i> <span class="align-middle">Ledger Statement</span>
        </a>
        <ul id="pages" class="sidebar-dropdown list-unstyled collapse <?php echo e(request()->routeIs('statement') && (request('type') === 'upiaccount' || request('type') === 'account') ? 'show' : ''); ?>" data-bs-parent="#sidebar">
          <li class="sidebar-item">
            <a class="sidebar-link <?php echo e(request('type') === 'upiaccount' ? 'active' : ''); ?>" href="<?php echo e(route('statement', ['type' => 'upiaccount'])); ?>">Payout Wallet</a>
          </li>
          <li class="sidebar-item">
            <a class="sidebar-link <?php echo e(request('type') === 'account' ? 'active' : ''); ?>" href="<?php echo e(route('statement', ['type' => 'account'])); ?>">Payin Wallet</a>
          </li>
        </ul>
      </li>

      <li class="sidebar-item <?php echo e(request()->routeIs('apisetup') ? 'active' : ''); ?>">
        <a class="sidebar-link" href="<?php echo e(route('apisetup', ['type' => 'document'])); ?>">
          <i class="align-middle" data-feather="user"></i> <span class="align-middle">API Document</span>
        </a>
      </li>
    </ul>
  </div>
</nav>



















<?php /**PATH C:\wamp64\www\template1\resources\views/layouts/sidebar.blade.php ENDPATH**/ ?>