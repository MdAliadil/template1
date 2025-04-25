<!DOCTYPE html>
<html lang="en" dir="ltr" data-bs-theme="light" data-color-theme="Blue_Theme" data-layout="vertical">

<head>
  <!-- Required meta tags -->
  <meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <!-- Favicon icon-->
  <link rel="shortcut icon" type="image/png" href="{{asset('')}}assets/newLogin/assets/images/logos/favicon.png" />

  <!-- Core Css -->
  <link rel="stylesheet" href="{{asset('')}}assets/newLogin/assets/css/styles.css" />
  
<script type="text/javascript" src="{{asset('')}}assets/js/plugins/loaders/pace.min.js"></script>
<script type="text/javascript" src="{{asset('')}}assets/js/core/libraries/jquery.min.js"></script>
<script type="text/javascript" src="{{asset('')}}assets/js/core/libraries/bootstrap.min.js"></script>
<script type="text/javascript" src="{{asset('')}}assets/js/plugins/loaders/blockui.min.js"></script>
<script type="text/javascript" src="{{asset('')}}assets/js/core/jquery.validate.min.js"></script>
<script type="text/javascript" src="{{asset('')}}assets/js/core/jquery.form.min.js"></script>
<script type="text/javascript" src="{{asset('')}}assets/js/core/sweetalert2.min.js"></script>
<script type="text/javascript" src="{{asset('')}}assets/js/plugins/forms/selects/select2.min.js"></script>
<script src="{{asset('')}}/assets/js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.js"></script>

  <title>TejasPe || Login </title>
  <style>
    .jq-toast-wrap {
        top: 20px !important;  /* Adjust the distance from the top */
        left: 20px !important; /* Adjust the distance from the left */
        left: auto !important; /* Remove any right positioning */
    }
</style>

</head>

<body>
  <!-- Preloader -->
  <div class="preloader">
    <img src="{{asset('')}}assets/newLogin/assets/images/logos/favicon.png" alt="loader" class="lds-ripple img-fluid" />
  </div>
  <div id="main-wrapper" class="p-0 bg-white auth-customizer-none">
    <div class="auth-login position-relative overflow-hidden d-flex align-items-center justify-content-center px-7 px-xxl-0 rounded-3 h-n20">
      <div class="auth-login-shape position-relative w-100">
        <div class="auth-login-wrapper card mb-0 container position-relative z-1 h-100 mh-n100" data-simplebar>
          <div class="card-body">
            <a href="main/index.html" class="">
              <img src="{{asset('')}}public/logos/logo1.jpg" class="light-logo" alt="Logo-Dark" />
              <img src="{{asset('')}}public/logos/logo1.jpg" class="dark-logo" alt="Logo-light" style="width: 150px;" />
            </a>
            <div class="row align-items-center justify-content-around pt-6 pb-5">
              <div class="col-lg-6 col-xl-5 d-none d-lg-block">
                <div class="text-center text-lg-start">
                  <img src="{{asset('')}}assets/newLogin/assets/images/backgrounds/login-security.png" alt="spike-img" class="img-fluid">
                </div>
              </div>
              <div class="col-lg-6 col-xl-5">
                <h2 class="mb-6 fs-8 fw-bolder">Welcome to TejasPe</h2>
                <p class="text-dark fs-4 mb-7">Automated Collection & Payment Solution for Growing Your Business!</p>
                <div class="d-flex align-items-center gap-3">
                  
                </div>
                <div class="position-relative text-center my-7">
                 
                </div>
                <form action="{{route('adminLogin')}}" method="post" id="login_form" class="loginform" name="loginForm" autocomplete="off">
                    <p style="color:red"><b class="errorText"></b></p>
                    <p style="color:teal"><b class="successText"></b></p>
                     {{ csrf_field() }}
                  <div class="mb-7">
                    <label for="exampleInputEmail1" class="form-label fw-bold">Username Or Mobile</label>
                    <input type="number" class="form-control py-6"  id="inputMobile" name="mobile" aria-describedby="emailHelp">
                  </div>
                  <div class="mb-9">
                    <label for="exampleInputPassword1" class="form-label fw-bold">Password</label>
                    <input id="inputPassword" type="password" name="password" class="form-control py-6" >
                  </div>
                  <div class="d-md-flex align-items-center justify-content-between mb-7 pb-1">
                    <div class="form-check mb-3 mb-md-0">
                      <input class="form-check-input primary" type="checkbox" value="" id="flexCheckChecked" checked>
                      <label class="form-check-label text-dark fs-3" for="flexCheckChecked">
                        Remeber this Device
                      </label>
                    </div>
                    
                  </div>
                  <button class="btn btn-primary w-100 mb-7 rounded-pill" id="sblf"  type="submit" value="Sign In" data-token="translatable.sblf">Login</button>
                  
                </form>
              </div>
            </div>
          </div>
        </div>
        <script>
  function handleColorTheme(e) {
    document.documentElement.setAttribute("data-color-theme", e);
  }
</script>
        <button class="btn btn-primary p-3 rounded-circle d-flex align-items-center justify-content-center customizer-btn" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample" aria-controls="offcanvasExample">
          <i class="icon ti ti-settings fs-7"></i>
        </button>

        <div class="offcanvas customizer offcanvas-end" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
          <div class="d-flex align-items-center justify-content-between p-3 border-bottom">
            <h4 class="offcanvas-title fw-semibold" id="offcanvasExampleLabel">
              Settings
            </h4>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
          </div>
          <div class="offcanvas-body h-n80" data-simplebar>
            <h6 class="fw-semibold fs-4 mb-2">Theme</h6>

            <div class="d-flex flex-row gap-3 customizer-box" role="group">
              <input type="radio" class="btn-check light-layout" name="theme-layout" id="light-layout" autocomplete="off" />
              <label class="btn p-9 btn-outline-primary" for="light-layout">
                <i class="icon ti ti-brightness-up fs-7 me-2"></i>Light
              </label>

              <input type="radio" class="btn-check dark-layout" name="theme-layout" id="dark-layout" autocomplete="off" />
              <label class="btn p-9 btn-outline-primary" for="dark-layout">
                <i class="icon ti ti-moon fs-7 me-2"></i>Dark
              </label>
            </div>

            <h6 class="mt-5 fw-semibold fs-4 mb-2">Theme Direction</h6>
            <div class="d-flex flex-row gap-3 customizer-box" role="group">
              <input type="radio" class="btn-check" name="direction-l" id="ltr-layout" autocomplete="off" />
              <label class="btn p-9 btn-outline-primary" for="ltr-layout">
                <i class="icon ti ti-text-direction-ltr fs-7 me-2"></i>LTR
              </label>

              <input type="radio" class="btn-check" name="direction-l" id="rtl-layout" autocomplete="off" />
              <label class="btn p-9 btn-outline-primary" for="rtl-layout">
                <i class="icon ti ti-text-direction-rtl fs-7 me-2"></i>RTL
              </label>
            </div>

            <h6 class="mt-5 fw-semibold fs-4 mb-2">Theme Colors</h6>

            <div class="d-flex flex-row flex-wrap gap-3 customizer-box color-pallete" role="group">
              <input type="radio" class="btn-check" name="color-theme-layout" id="Blue_Theme" autocomplete="off" />
              <label class="btn p-9 btn-outline-primary d-flex align-items-center justify-content-center" onclick="handleColorTheme('Blue_Theme')" for="Blue_Theme" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="BLUE_THEME">
                <div class="color-box rounded-circle d-flex align-items-center justify-content-center skin-1">
                  <i class="ti ti-check text-white d-flex icon fs-5"></i>
                </div>
              </label>

              <input type="radio" class="btn-check" name="color-theme-layout" id="Aqua_Theme" autocomplete="off" />
              <label class="btn p-9 btn-outline-primary d-flex align-items-center justify-content-center" onclick="handleColorTheme('Aqua_Theme')" for="Aqua_Theme" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="AQUA_THEME">
                <div class="color-box rounded-circle d-flex align-items-center justify-content-center skin-2">
                  <i class="ti ti-check text-white d-flex icon fs-5"></i>
                </div>
              </label>

              <input type="radio" class="btn-check" name="color-theme-layout" id="Purple_Theme" autocomplete="off" />
              <label class="btn p-9 btn-outline-primary d-flex align-items-center justify-content-center" onclick="handleColorTheme('Purple_Theme')" for="Purple_Theme" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="PURPLE_THEME">
                <div class="color-box rounded-circle d-flex align-items-center justify-content-center skin-3">
                  <i class="ti ti-check text-white d-flex icon fs-5"></i>
                </div>
              </label>

              <input type="radio" class="btn-check" name="color-theme-layout" id="green-theme-layout" autocomplete="off" />
              <label class="btn p-9 btn-outline-primary d-flex align-items-center justify-content-center" onclick="handleColorTheme('Green_Theme')" for="green-theme-layout" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="GREEN_THEME">
                <div class="color-box rounded-circle d-flex align-items-center justify-content-center skin-4">
                  <i class="ti ti-check text-white d-flex icon fs-5"></i>
                </div>
              </label>

              <input type="radio" class="btn-check" name="color-theme-layout" id="cyan-theme-layout" autocomplete="off" />
              <label class="btn p-9 btn-outline-primary d-flex align-items-center justify-content-center" onclick="handleColorTheme('Cyan_Theme')" for="cyan-theme-layout" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="CYAN_THEME">
                <div class="color-box rounded-circle d-flex align-items-center justify-content-center skin-5">
                  <i class="ti ti-check text-white d-flex icon fs-5"></i>
                </div>
              </label>

              <input type="radio" class="btn-check" name="color-theme-layout" id="orange-theme-layout" autocomplete="off" />
              <label class="btn p-9 btn-outline-primary d-flex align-items-center justify-content-center" onclick="handleColorTheme('Orange_Theme')" for="orange-theme-layout" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="ORANGE_THEME">
                <div class="color-box rounded-circle d-flex align-items-center justify-content-center skin-6">
                  <i class="ti ti-check text-white d-flex icon fs-5"></i>
                </div>
              </label>
            </div>

            <h6 class="mt-5 fw-semibold fs-4 mb-2">Layout Type</h6>
            <div class="d-flex flex-row gap-3 customizer-box" role="group">
              <div>
                <input type="radio" class="btn-check" name="page-layout" id="vertical-layout" autocomplete="off" />
                <label class="btn p-9 btn-outline-primary" for="vertical-layout">
                  <i class="icon ti ti-layout-sidebar-right fs-7 me-2"></i>Vertical
                </label>
              </div>
              <div>
                <input type="radio" class="btn-check" name="page-layout" id="horizontal-layout" autocomplete="off" />
                <label class="btn p-9 btn-outline-primary" for="horizontal-layout">
                  <i class="icon ti ti-layout-navbar fs-7 me-2"></i>Horizontal
                </label>
              </div>
            </div>

            <h6 class="mt-5 fw-semibold fs-4 mb-2">Container Option</h6>

            <div class="d-flex flex-row gap-3 customizer-box" role="group">
              <input type="radio" class="btn-check" name="layout" id="boxed-layout" autocomplete="off" />
              <label class="btn p-9 btn-outline-primary" for="boxed-layout">
                <i class="icon ti ti-layout-distribute-vertical fs-7 me-2"></i>Boxed
              </label>

              <input type="radio" class="btn-check" name="layout" id="full-layout" autocomplete="off" />
              <label class="btn p-9 btn-outline-primary" for="full-layout">
                <i class="icon ti ti-layout-distribute-horizontal fs-7 me-2"></i>Full
              </label>
            </div>

            <h6 class="fw-semibold fs-4 mb-2 mt-5">Sidebar Type</h6>
            <div class="d-flex flex-row gap-3 customizer-box" role="group">
              <a href="javascript:void(0)" class="fullsidebar">
                <input type="radio" class="btn-check" name="sidebar-type" id="full-sidebar" autocomplete="off" />
                <label class="btn p-9 btn-outline-primary" for="full-sidebar">
                  <i class="icon ti ti-layout-sidebar-right fs-7 me-2"></i>Full
                </label>
              </a>
              <div>
                <input type="radio" class="btn-check " name="sidebar-type" id="mini-sidebar" autocomplete="off" />
                <label class="btn p-9 btn-outline-primary" for="mini-sidebar">
                  <i class="icon ti ti-layout-sidebar fs-7 me-2"></i>Collapse
                </label>
              </div>
            </div>

            <h6 class="mt-5 fw-semibold fs-4 mb-2">Card With</h6>

            <div class="d-flex flex-row gap-3 customizer-box" role="group">
              <input type="radio" class="btn-check" name="card-layout" id="card-with-border" autocomplete="off" />
              <label class="btn p-9 btn-outline-primary" for="card-with-border">
                <i class="icon ti ti-border-outer fs-7 me-2"></i>Border
              </label>

              <input type="radio" class="btn-check" name="card-layout" id="card-without-border" autocomplete="off" />
              <label class="btn p-9 btn-outline-primary" for="card-without-border">
                <i class="icon ti ti-border-none fs-7 me-2"></i>Shadow
              </label>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="dark-transparent sidebartoggler"></div>
  </div>
  <!-- Import Js Files -->
  <script src="{{asset('')}}assets/newLogin/assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="{{asset('')}}assets/newLogin/assets/libs/simplebar/dist/simplebar.min.js"></script>
  <script src="{{asset('')}}assets/newLogin/assets/js/theme/app.init.js"></script>
  <script src="{{asset('')}}assets/newLogin/assets/js/theme/theme.js"></script>
  <script src="{{asset('')}}assets/newLogin/assets/js/theme/app.min.js"></script>
  <script src="{{asset('')}}assets/newLogin/assets/js/theme/feather.min.js"></script>

  <!-- solar icons -->
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
  <script>
  document.onkeydown = function(e) {
    if (e.keyCode == 123) { // F12 key
        return false;
    }
    if (e.ctrlKey && e.shiftKey && (e.keyCode == 'I'.charCodeAt(0) || e.keyCode == 'C'.charCodeAt(0) || e.keyCode == 'J'.charCodeAt(0) || e.keyCode == 'K'.charCodeAt(0))) {
        return false; 
    }
    if (e.ctrlKey && (e.keyCode == 'U'.charCodeAt(0))) {
        return false; 
    }
};

// Disable right-click
document.addEventListener('contextmenu', function(e) {
    e.preventDefault();
});

    $( ".loginform" ).validate({
                rules: {
                    mobile: {
                        required: true
                    },
                    password: {
                        required: true,
                    }
                },
                messages: {
                    mobile: {
                        required: "Please enter agent number"
                    },
                    password: {
                        required: "Please enter password",
                    }
                },
                errorElement: "p",
                errorPlacement: function ( error, element ) {
                    if ( element.prop("tagName").toLowerCase() === "select" ) {
                        error.insertAfter( element.closest( ".form-group" ).find(".select2") );
                    } else {
                        error.insertAfter( element );
                        $
                    }
                },
                submitHandler: function () {
                    var form = $('.loginform');
                    form.ajaxSubmit({
                        dataType:'json',
                        beforeSubmit:function(){
                            swal({
                                title: 'Wait!',
                                text: 'We are checking your login credential',
                                onOpen: () => {
                                    swal.showLoading()
                                },
                                allowOutsideClick: () => !swal.isLoading()
                            });
                        },
                        success:function(data){
                            swal.close();
                            if(data.status == "emp"){
                                //alert(data.status);
                                Match(data.fingdata);
                               
                                
                            }else if(data.status == "Login"){
                              $.toast({
                                    heading: 'Success',
                                    text: 'Login Successfull',
                                    showHideTransition: 'fade',
                                    icon: 'success',
                                    bgColor : '#fb977d', 
                                    position: 'top-right' // Set the position to top-left
                                });
                                window.location.reload();
                            }else if(data.status == "otpsent" || data.status == "preotp"){
                                $('div.formdata').html(`<div class="form-group has-feedback has-feedback-left">
                                <input type="password" class="form-control" placeholder="Enter Otp" name="otp" required>
                                <div class="form-control-feedback">
                                    <i class="icon-lock2 text-muted"></i>
                                </div>
                                <a href="javascript:void(0)" onclick="OTPRESEND()" class="text-primary pull-right">Resend Otp</a>
                                <div class="clearfix"></div>
                            </div> `);

                                if(data.status == "preotp"){
                                    $('b.successText').text('Please use previous otp sent on your mobile.');
                                    setTimeout(function(){
                                        $('b.successText').text('');
                                    }, 5000);
                                }
                            }
                        },
                        error: function(errors) {
                            swal.close();
                            if(errors.status == '400'){
                                $('b.errorText').text(errors.responseJSON.status);
                                setTimeout(function(){
                                    $('b.errorText').text('');
                                }, 5000);
                            }else{
                                $('b.errorText').text('Something went wrong, try again later.');
                                setTimeout(function(){
                                    $('b.errorText').text('');
                                }, 5000);
                            }
                        }
                    });
                }
            });
  
       


</script>
</body>

</html>