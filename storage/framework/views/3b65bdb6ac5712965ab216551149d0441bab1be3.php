<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>SurakshaPay | Login Page</title>
<?php echo $__env->make('layouts.links', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
   
    <style>
        .divider:after, .divider:before {
            content: "";
            flex: 1;
            height: 1px;
            background: #eee;
        }
        .h-custom { height: calc(100% - 73px); }
        @media (max-width: 450px) { .h-custom { height: 100%; } }
    </style>
</head>
<body>

<section class="vh-100 d-flex align-items-center justify-content-center">
    <div class="container">
        <div class="row d-flex align-items-center justify-content-center">
            <!-- Left Side: Image -->
            <div class="col-md-6 d-flex justify-content-center">
                <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-login-form/draw2.webp"
                     class="img-fluid" alt="Sample image">
            </div>

            <!-- Right Side: Form -->
            <div class="col-md-6">
                <div class="card p-4 shadow">
                    <h3 class="text-center mb-4">Login</h3>

                    <form id="login_form" class="loginform">
                        <?php echo e(csrf_field()); ?>


                        <!-- Email Input -->
                        <div class="mb-3">
                            <label class="form-label">Email address</label>
                            <input type="email" class="form-control" name="email" placeholder="Enter your email" required>
                        </div>

                        <!-- Password Input -->
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" placeholder="Enter password" required>
                        </div>

                        <!-- Remember Me & Forgot Password -->
                        <div class="d-flex justify-content-between">
                            <div>
                                <input type="checkbox" id="rememberMe">
                                <label for="rememberMe">Remember me</label>
                            </div>
                            <a href="#" class="text-primary">Forgot password?</a>
                        </div>

                        <!-- Login Button -->
                        <div class="mt-4 text-center">
                            <button type="submit" class="btn btn-primary w-100">Login</button>
                        </div>

                        <!-- Error Message -->
                        <p class="text-danger text-center mt-3 errorText"></p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- jQuery & Bootstrap JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- jQuery Validation, SweetAlert2 & Toast -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Custom jQuery Code -->
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
            url: "<?php echo e(route('authCheck')); ?>",
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
</script>

</body>
</html>
<?php /**PATH C:\wamp64\www\template\resources\views/welcome.blade.php ENDPATH**/ ?>