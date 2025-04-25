 <!--start top header-->
<style>
  ..dropdown-menu {
    display: none !important;
}


</style>
      <header class="top-header">        
        <nav class="navbar navbar-expand">
          <div class="mobile-toggle-icon d-xl-none">
              <i class="bi bi-list"></i>
            </div>
            <div class="top-navbar d-none d-xl-block">
            <ul class="navbar-nav align-items-center">
              <li class="nav-item">
              <a class="nav-link" href="index.html">Dashboard</a>
              </li>
              <li class="nav-item">
              <a class="nav-link" href="app-emailbox.html">Email</a>
              </li>
              <li class="nav-item">
              <a class="nav-link" href="javascript:;">Projects</a>
              </li>
              <li class="nav-item d-none d-xxl-block">
              <a class="nav-link" href="javascript:;">Events</a>
              </li>
              <li class="nav-item d-none d-xxl-block">
              <a class="nav-link" href="app-to-do.html">Todo</a>
              </li>
            </ul>
            </div>
            <div class="search-toggle-icon d-xl-none ms-auto">
              <i class="bi bi-search"></i>
            </div>
            <form class="searchbar d-none d-xl-flex ms-auto">
                <div class="position-absolute top-50 translate-middle-y search-icon ms-3"><i class="bi bi-search"></i></div>
                <input class="form-control" type="text" placeholder="Type here to search">
                <div class="position-absolute top-50 translate-middle-y d-block d-xl-none search-close-icon"><i class="bi bi-x-lg"></i></div>
            </form>
            <div class="top-navbar-right ms-3">
              <ul class="navbar-nav align-items-center">
              <li class="nav-item dropdown dropdown-large">
                <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown">
                  <div class="user-setting d-flex align-items-center gap-1">
                   <img src="<?php echo e(asset('assetsss/images/avatars/avatar-1.png')); ?>" class="user-img" alt="Avatar">

                    <div class="user-name d-none d-sm-block"><?php echo e(Auth::user()->name); ?></div>
                  </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                  <li>
                     <a class="dropdown-item" href="#">
                       <div class="d-flex align-items-center">
                          <img src="assetsss/images/avatars/avatar-1.png" alt="" class="rounded-circle" width="60" height="60">
                          <div class="ms-3">
                            <h6 class="mb-0 dropdown-user-name"><?php echo e(Auth::user()->name); ?></h6>
                           
                          </div>
                       </div>
                     </a>
                   </li>
                   <li><hr class="dropdown-divider"></li>
                   <li>
                      <a class="dropdown-item" href="<?php echo e(route('profile')); ?>">
                         <div class="d-flex align-items-center">
                           <div class="setting-icon"><i class="bi bi-person-fill"></i></div>
                           <div class="setting-text ms-3"><span>Profile</span></div>
                         </div>
                       </a>
                    </li>
                   
                    <li>
                      <a class="dropdown-item" href="<?php echo e(route('home')); ?>">
                         <div class="d-flex align-items-center">
                           <div class="setting-icon"><i class="bi bi-speedometer"></i></div>
                           <div class="setting-text ms-3"><span>Dashboard</span></div>
                         </div>
                       </a>
                    </li>
                    
                    <li><hr class="dropdown-divider"></li>
                    <li>
                      <a class="dropdown-item" href="<?php echo e(route('logout')); ?>">
                         <div class="d-flex align-items-center">
                           <div class="setting-icon"><i class="bi bi-lock-fill"></i></div>
                           <div class="setting-text ms-3"><span>Logout</span></div>
                         </div>
                       </a>
                    </li>

                </ul>
              </li>
             <li class="nav-item dropdown dropdown-large">
                <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown">
                  <div class="projects">
                    <i class="bi bi-grid-3x3-gap-fill"></i>
                  </div>
                </a>
               
              </li>
              <li class="nav-item dropdown dropdown-large">
                <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown">
                  <div class="messages">
                    <span class="notify-badge">5</span>
                    <i class="bi bi-messenger"></i>
                  </div>
                </a>
               
              </li>
              <li class="nav-item dropdown dropdown-large d-none d-sm-block">
                <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown">
                  <div class="notifications">
                    <span class="notify-badge">8</span>
                    <i class="bi bi-bell-fill"></i>
                  </div>
                </a>
               
              </ul>
              </div>
        </nav>
      </header>

      <script type="text/javascript">
       document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".dropdown-toggle").forEach(function (dropdown) {
    dropdown.addEventListener("click", function (event) {
      event.preventDefault(); // Default behavior hataye
      event.stopPropagation(); // Parent elements par event bubble hone se roke
      let dropdownMenu = this.nextElementSibling;

      // Toggle class to show/hide
      if (dropdownMenu.classList.contains("show")) {
        dropdownMenu.classList.remove("show");
      } else {
        document.querySelectorAll(".dropdown-menu").forEach(function (menu) {
          menu.classList.remove("show"); // Baaki sare dropdown close karein
        });
        dropdownMenu.classList.add("show");
      }
    });
  });

  // Dropdown ko band karne ke liye document click event handle karein
  document.addEventListener("click", function (e) {
    document.querySelectorAll(".dropdown-menu").forEach(function (menu) {
      if (!menu.parentElement.contains(e.target)) {
        menu.classList.remove("show");
      }
    });
  });
});


      </script>
       <!--end top header-->
<?php /**PATH C:\wamp64\www\template\resources\views/layouts/header.blade.php ENDPATH**/ ?>