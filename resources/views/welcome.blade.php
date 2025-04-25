<!DOCTYPE html>
<html lang="en">



<!-- Added by HTTrack --><meta http-equiv="content-type" content="text/html;charset=UTF-8" /><!-- /Added by HTTrack -->
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="Responsive Admin &amp; Dashboard Template based on Bootstrap 5">
	<meta name="author" content="AdminKit">
	<meta name="keywords" content="adminkit, bootstrap, bootstrap 5, admin, dashboard, template, responsive, css, sass, html, theme, front-end, ui kit, web">

	<link rel="preconnect" href="https://fonts.gstatic.com/">
	<link rel="shortcut icon" href="{{ asset('assetsss/img/icons/icon-48x48.png') }}" />

	<link rel="canonical" href="pages-sign-in-2.html" />

	<title>Sign In | AdminKit Demo</title>

	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&amp;display=swap" rel="stylesheet">

	<!-- Choose your prefered color scheme -->
	<!-- <link href="css/light.css" rel="stylesheet"> -->
	<!-- <link href="css/dark.css" rel="stylesheet"> -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

	<!-- BEGIN SETTINGS -->
	<!-- Remove this after purchasing -->
	<link class="js-stylesheet" href="{{ asset('assetsss/css/light.css') }}" rel="stylesheet">
	<script src="{{ asset('assetsss/js/settings.js') }}"></script>
	<style>
		body {
			opacity: 0;
		}
               .divider:after, .divider:before {
                content: "";
                flex: 1;
                height: 1px;
                background: #eee;
            }
            .h-custom { height: calc(100% - 73px); }
            @media (max-width: 450px) { .h-custom { height: 100%; } }
        </style>
	<!-- END SETTINGS -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-120946860-10"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-120946860-10', { 'anonymize_ip': true });
</script>
</head>

<body data-theme="default" data-layout="fluid" data-sidebar-position="left" data-sidebar-layout="default" >
	<main class="d-flex w-100 h-100"  >
		<div class="container d-flex flex-column" >
			<div class="row vh-100">
				<div class="col-sm-10 col-md-8 col-lg-6 col-xl-5 mx-auto d-table h-100">
					<div class="d-table-cell align-middle">

						<div class="text-center mt-4">
							<h1 class="h2">Welcome back!</h1>
							<p class="lead">
								Sign in to your account to continue
							</p>
						</div>

						<div class="card" style="background-color:grey;">
							<div class="card-body">
								<div class="m-sm-3">
									<div class="d-grid gap-2 mb-3">
										<a class='btn btn-google btn-lg' href=''><i class="fab fa-fw fa-google"></i> Sign in with Google</a>
										<a class='btn btn-facebook btn-lg' href=''><i class="fab fa-fw fa-facebook-f"></i> Sign in with Facebook</a>
										<a class='btn btn-microsoft btn-lg' href=''><i class="fab fa-fw fa-microsoft"></i> Sign in with Microsoft</a>
									</div>
									<div class="row">
										<div class="col">
											<hr>
										</div>
										<div class="col-auto text-uppercase d-flex align-items-center">Or</div>
										<div class="col">
											<hr>
										</div>
									</div>
                                    <form id="login_form" class="loginform">
                                        {{ csrf_field() }}
										<div class="mb-3">
											<label class="form-label">Email</label>
											<input class="form-control form-control-lg" type="email" name="email" placeholder="Enter your email" />
										</div>
										<div class="mb-3">
											<label class="form-label">Password</label>
											<input class="form-control form-control-lg" type="password" name="password" placeholder="Enter your password" />
											<small>
												<a href='pages-reset-password.html'>Forgot password?</a>
											</small>
										</div>
										<div>
											<div class="form-check align-items-center">
												<input id="customControlInline" type="checkbox" class="form-check-input" value="remember-me" name="remember-me"
													checked>
												<label class="form-check-label text-small" for="customControlInline">Remember me</label>
											</div>
										</div>
										<div class="d-grid gap-2 mt-3">
											<button class='btn btn-lg btn-primary'> Sign in </button>
                                            <div class="text-danger errorText mb-2"></div>

										</div>
									</form>
								</div>
							</div>
						</div>
						<div class="text-center mb-3">
							Dont have an account? <a href="#">Sign up</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</main>
</body>
</html>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

	<script src="{{ asset('assetsss/js/app.js') }}"></script>

<script>
    $(document).ready(function () {

        // AJAX Login
        $("#login_form").on("submit", function (e) {
            e.preventDefault();
            let form = $(this);
            let formData = form.serialize();
    
            // Show loading alert
            Swal.fire({
                title: 'Logging in...',
                text: 'Please wait while we verify your credentials.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
    
            // AJAX Call
            $.ajax({
                url: "{{ route('authCheck') }}",
                type: "POST",
                data: formData,
                dataType: "json",
                success: function (response) {
                    Swal.close();
                    if (response.status === "Login") {
                        Swal.fire({
                            icon: 'success',
                            title: 'Login Successful',
                            text: 'Redirecting to dashboard...',
                            showConfirmButton: false,
                            timer: 2000
                        }).then(() => {
                            window.location.href = "/dashboard";  // Redirect to dashboard
                        });
                    } else if (response.status === "otpsent" || response.status === "preotp") {
                        $(".errorText").text("Please enter the OTP sent to your email.");
                    } else {
                        $(".errorText").text("Invalid credentials. Please try again.");
                    }
                },
                error: function (xhr) {
                    Swal.close();
                    $(".errorText").text("Error: " + (xhr.responseJSON.message || "Something went wrong."));
                }
            });
        });
    
        // Disable inspect element
        document.onkeydown = function (e) {
            if (e.keyCode === 123 || (e.ctrlKey && e.shiftKey && (e.keyCode === 'I'.charCodeAt(0) || e.keyCode === 'C'.charCodeAt(0)))) {
                return false;
            }
        };
        document.addEventListener('contextmenu', function (e) {
            e.preventDefault();
        });
    
    });
  document.addEventListener("DOMContentLoaded", function(event) { 
    setTimeout(function(){
      if(localStorage.getItem('popState') !== 'shown'){
        window.notyf.open({
          type: "success",
          message: "Get access to all 500+ components and 45+ pages with AdminKit PRO. <u><a class=\"text-white\" href=\"https://adminkit.io/pricing\" target=\"_blank\">More info</a></u> 🚀",
          duration: 10000,
          ripple: true,
          dismissible: false,
          position: {
            x: "left",
            y: "bottom"
          }
        });

        localStorage.setItem('popState','shown');
      }
    }, 15000);
  });
</script>