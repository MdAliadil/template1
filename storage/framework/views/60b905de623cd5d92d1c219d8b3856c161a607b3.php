<nav class="navbar navbar-expand navbar-light navbar-bg ">
  <a class="sidebar-toggle js-sidebar-toggle">
    <i class="hamburger align-self-center"></i>
  </a>

  

  

  <div class="navbar-collapse collapse">
    <ul class="navbar-nav navbar-align">
      
      <li class="nav-item dropdown">
        <a class="nav-icon dropdown-toggle" href="#" id="messagesDropdown" data-bs-toggle="dropdown">
          <div class="position-relative">
            <i class="align-middle" data-feather="message-square"></i>
          </div>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end py-0" aria-labelledby="messagesDropdown">
          <div class="dropdown-menu-header">
            <div class="position-relative">
              4 New Messages
            </div>
          </div>
          <div class="list-group">
            <a href="#" class="list-group-item">
              <div class="row g-0 align-items-center">
                <div class="col-2">
                  <img src="<?php echo e(asset('assetsss/img/avatars/avatar-5.jpg')); ?>" class="avatar img-fluid rounded-circle" alt="Vanessa Tucker">
                </div>
                <div class="col-10 ps-2">
                  <div class="text-dark">Vanessa Tucker</div>
                  <div class="text-muted small mt-1">Nam pretium turpis et arcu. Duis arcu tortor.</div>
                  <div class="text-muted small mt-1">15m ago</div>
                </div>
              </div>
            </a>
            <a href="#" class="list-group-item">
              <div class="row g-0 align-items-center">
                <div class="col-2">
                  <img src="<?php echo e(asset('assetsss/img/avatars/avatar-2.jpg')); ?>" class="avatar img-fluid rounded-circle" alt="William Harris">
                </div>
                <div class="col-10 ps-2">
                  <div class="text-dark">William Harris</div>
                  <div class="text-muted small mt-1">Curabitur ligula sapien euismod vitae.</div>
                  <div class="text-muted small mt-1">2h ago</div>
                </div>
              </div>
            </a>
            <a href="#" class="list-group-item">
              <div class="row g-0 align-items-center">
                <div class="col-2">
                  <img src="<?php echo e(asset('assetsss/img/avatars/avatar-4.jpg')); ?>" class="avatar img-fluid rounded-circle" alt="Christina Mason">
                </div>
                <div class="col-10 ps-2">
                  <div class="text-dark">Christina Mason</div>
                  <div class="text-muted small mt-1">Pellentesque auctor neque nec urna.</div>
                  <div class="text-muted small mt-1">4h ago</div>
                </div>
              </div>
            </a>
            <a href="#" class="list-group-item">
              <div class="row g-0 align-items-center">
                <div class="col-2">
                  <img src="<?php echo e(asset('assetsss/img/avatars/avatar-3.jpg')); ?>" class="avatar img-fluid rounded-circle" alt="Sharon Lessman">
                </div>
                <div class="col-10 ps-2">
                  <div class="text-dark">Sharon Lessman</div>
                  <div class="text-muted small mt-1">Aenean tellus metus, bibendum sed, posuere ac, mattis non.</div>
                  <div class="text-muted small mt-1">5h ago</div>
                </div>
              </div>
            </a>
          </div>
          <div class="dropdown-menu-footer">
            <a href="#" class="text-muted">Show all messages</a>
          </div>
        </div>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-flag dropdown-toggle" href="#" id="languageDropdown" data-bs-toggle="dropdown">
          <img src="<?php echo e(asset('assetsss/img/flags/in.png')); ?>" alt="हिन्दी" />
        </a>
        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="languageDropdown">
          <a class="dropdown-item" href="#">
            <img src="<?php echo e(asset('assetsss/img/flags/in.png')); ?>" alt="English" width="20" class="align-middle me-1" />
            <span class="align-middle">हिन्दी</span>
          </a>
          <a class="dropdown-item" href="#">
            <img src="<?php echo e(asset('assetsss/img/flags/us.png')); ?>" alt="English" width="20" class="align-middle me-1" />
            <span class="align-middle">English</span>
          </a>
  
          <a class="dropdown-item" href="#">
            <img src="<?php echo e(asset('assetsss/img/flags/es.png')); ?>" alt="Spanish" width="20" class="align-middle me-1" />
            <span class="align-middle">Spanish</span>
          </a>
          <a class="dropdown-item" href="#">
            <img src="<?php echo e(asset('assetsss/img/flags/ru.png')); ?>" alt="Russian" width="20" class="align-middle me-1" />
            <span class="align-middle">Russian</span>
          </a>
          <a class="dropdown-item" href="#">
            <img src="<?php echo e(asset('assetsss/img/flags/de.png')); ?>" alt="German" width="20" class="align-middle me-1" />
            <span class="align-middle">German</span>
          </a>
        </div>
      </li>
      <li class="nav-item">
        <a class="nav-icon js-fullscreen d-none d-lg-block" href="#">
          <div class="position-relative">
            <i class="align-middle" data-feather="maximize"></i>
          </div>
        </a>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-icon pe-md-0 dropdown-toggle" href="#" data-bs-toggle="dropdown">
          <img src="<?php echo e(asset('assets/img/a.png')); ?>"  class="avatar img-fluid rounded" alt="Charles Hall" />
        </a>
       
        <div class="dropdown-menu dropdown-menu-end">
          <a class='dropdown-item' href="<?php echo e(route('profile')); ?>"><i class="align-middle me-1" data-feather="user"></i> Profile</a>
          
          
          <a class="dropdown-item" href="<?php echo e(route('logout')); ?>"> <i data-feather="log-out" class="align-middle me-1"></i>Log out</a>
        </div>
      </li>
    </ul>
  </div>
</nav><?php /**PATH C:\wamp64\www\template1\resources\views/layouts/header.blade.php ENDPATH**/ ?>