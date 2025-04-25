 <!--start content-->
  <?php echo $__env->make('layouts.links', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
  
  <?php $__env->startSection('title', ucwords($user->name) . " Profile"); ?>
<?php $__env->startSection('bodyClass', "has-detached-left"); ?>
<?php $__env->startSection('pagetitle', ucwords($user->name) . " Profile"); ?>
          <main class="page-content">

            <!--breadcrumb-->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center">
              <div class="breadcrumb-title pe-3 text-white">Home</div>
              <div class="ps-3">
                <nav aria-label="breadcrumb">
                  <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt text-white"></i></a>
                    </li>
                    <li class="breadcrumb-item active text-white" aria-current="page">User Profile</li>
                  </ol>
                </nav>
              </div>
              
            </div>
            <!--end breadcrumb-->
           
            <div class="profile-cover " ></div> 

            <div class="row">
              <div class="col-12 col-lg-8">
                <div class="card shadow-sm border-0">
                  <div class="card-body">
                      <h5 class="mb-0">My Account</h5>
                      <hr>
                      <div class="card shadow-none border">
                        <div class="card-header">
                          <h6 class="mb-0">USER INFORMATION</h6>
                        </div>
                        <div class="card-body">
                          <form class="row g-3">
                             <div class="col-6">
                                <label class="form-label">Username</label>
                                <input type="text" class="form-control" value="<?php echo e(Auth::user()->name); ?>">
                             </div>
                             <div class="col-6">
                              <label class="form-label">Email address</label>
                              <input type="text" class="form-control" value="<?php echo e(Auth::user()->email); ?>">
                            </div>
                              <div class="col-6">
                                <label class="form-label">Role Name</label>
                                <input type="text" class="form-control" value="<?php echo e($user->role->name); ?>">
                            </div>
                           
                          </form>
                        </div>
                      </div>
                      <div class="card shadow-none border">
                        <div class="card-header">
                          <h6 class="mb-0">CONTACT INFORMATION</h6>
                        </div>
                        <div class="card-body">
                          <form class="row g-3">
                            <div class="col-12">
                              <label class="form-label">Address</label>
                              <input type="text" class="form-control" value="luckdown">
                             </div>
                             <div class="col-6">
                                <label class="form-label">City</label>
                                <input type="text" class="form-control" value="lucknow">
                             </div>
                             <div class="col-6">
                              <label class="form-label">Country</label>
                              <input type="text" class="form-control" value="<?php echo e(Auth::user()->email); ?>">
                            </div>
                              <div class="col-6">
                                <label class="form-label">Mobile</label>
                                <input type="text" class="form-control" value="<?php echo e(Auth::user()->mobile); ?>">
                            </div>
                           
                          </form>
                        </div>
                      </div>
                      
                  </div>
                </div>
              </div>
              <div class="col-12 col-lg-4">
                <div class="card shadow-sm border-0 overflow-hidden">
                  <div class="card-body">
                      <div class="profile-avatar text-center">
                        <img src="<?php echo e(asset('assetsss/images/avatars/avatar-1.png')); ?>" class="rounded-circle shadow" width="120" height="120" alt="">
                      </div>
                     
                      <div class="text-center mt-4">
                        <h4 class="mb-1"><?php echo e(Auth::user()->name); ?></h4>
                       
                      <hr>
                      <div class="text-start">
                        <h5 class="">About</h5>
                        <p class="mb-0">It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem.
                      </div>
                  </div>
                 
                </div>
              </div>
            </div><!--end row-->

          </main>
       <!--end page main-->


       <!--start overlay-->
        <div class="overlay nav-toggle-icon"></div>
       <!--end overlay-->

        <!--Start Back To Top Button-->
        <a href="javaScript:;" class="back-to-top"><i class='bx bxs-up-arrow-alt'></i></a>
        <!--End Back To Top Button-->
        
        <!--start switcher-->
       <div class="switcher-body">
        <button class="btn btn-primary btn-switcher shadow-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasScrolling" aria-controls="offcanvasScrolling"><i class="bi bi-paint-bucket me-0"></i></button>
        <div class="offcanvas offcanvas-end shadow border-start-0 p-2" data-bs-scroll="true" data-bs-backdrop="false" tabindex="-1" id="offcanvasScrolling">
          <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title" id="offcanvasScrollingLabel">Theme Customizer</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
          </div>
          <div class="offcanvas-body">
            <h6 class="mb-0">Theme Variation</h6>
            <hr>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="inlineRadioOptions" id="LightTheme" value="option1">
              <label class="form-check-label" for="LightTheme">Light</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="inlineRadioOptions" id="DarkTheme" value="option2">
              <label class="form-check-label" for="DarkTheme">Dark</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="inlineRadioOptions" id="SemiDarkTheme" value="option3">
              <label class="form-check-label" for="SemiDarkTheme">Semi Dark</label>
            </div>
            <hr>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="inlineRadioOptions" id="MinimalTheme" value="option3" checked>
              <label class="form-check-label" for="MinimalTheme">Minimal Theme</label>
            </div>
            <hr/>
            <h6 class="mb-0">Header Colors</h6>
            <hr/>
            <div class="header-colors-indigators">
              <div class="row row-cols-auto g-3">
                <div class="col">
                  <div class="indigator headercolor1" id="headercolor1"></div>
                </div>
                <div class="col">
                  <div class="indigator headercolor2" id="headercolor2"></div>
                </div>
                <div class="col">
                  <div class="indigator headercolor3" id="headercolor3"></div>
                </div>
                <div class="col">
                  <div class="indigator headercolor4" id="headercolor4"></div>
                </div>
                <div class="col">
                  <div class="indigator headercolor5" id="headercolor5"></div>
                </div>
                <div class="col">
                  <div class="indigator headercolor6" id="headercolor6"></div>
                </div>
                <div class="col">
                  <div class="indigator headercolor7" id="headercolor7"></div>
                </div>
                <div class="col">
                  <div class="indigator headercolor8" id="headercolor8"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
       </div>
       <!--end switcher-->

       

  </div>
  <!--end wrapper-->
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\pg\resources\views/profile/index.blade.php ENDPATH**/ ?>