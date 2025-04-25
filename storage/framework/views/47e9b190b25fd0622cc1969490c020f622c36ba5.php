<!--start content-->

<?php $__env->startSection('title', ucwords($user->name) . ' Profile'); ?>
<?php $__env->startSection('bodyClass', 'has-detached-left'); ?>
<?php $__env->startSection('pagetitle', ucwords($user->name) . ' Profile'); ?>
<?php $__env->startPush('style'); ?>
    <style>
        .profile-sidebar-pagecreate-admin .profile-sidebar {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
        }

        .profile-sidebar-pagecreate-admin .profile-sidebar img {
            border-radius: 50%;
            width: 100px;
            height: 100px;
        }

        .profile-sidebar-pagecreate-admin .profile-sidebar .btn {
            width: 100%;
            margin-bottom: 10px;
            text-align: left;
        }

        .profile-sidebar-pagecreate-admin .account-settings {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
        }

        .profile-sidebar-pagecreate-admin .account-settings .form-control {
            border-radius: 5px;
        }

        .profile-sidebar-pagecreate-admin .save-btn {
            background-color: #3366ff;
            color: white;
        }

        .profile-sidebar-pagecreate-admin .tab-content>div {
            display: none;
        }

        .profile-sidebar-pagecreate-admin .tab-content>.active {
            display: block;
        }

        .profile-sidebar-pagecreate-admin .tab-btn {
            background: linear-gradient(135deg, #4a90e2, #007bff);
            color: white;
            font-weight: 500;
            padding: 10px 20px;
            border: none;
            border-radius: 30px;
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
            transition: all 0.3s ease-in-out;
        }

        .profile-sidebar-pagecreate-admin .tab-btn:hover {
            background: linear-gradient(135deg, #3a78d3, #006ae6);
            transform: scale(1.05);
            box-shadow: 0 6px 15px rgba(0, 123, 255, 0.4);
        }

        .profile-sidebar-pagecreate-admin .tab-btn.active {
            background: linear-gradient(135deg, #003d99, #0056cc);
            box-shadow: 0 6px 20px rgba(0, 91, 187, 0.5);
        }

        .profile-sidebar-pagecreate-admin .logout-btn {
            background: linear-gradient(135deg, #ff4b5c, #ff0000);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 30px;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(255, 0, 0, 0.3);
            transition: all 0.3s ease-in-out;
            width: 100%;
            text-align: center;
        }

        .profile-sidebar-pagecreate-admin .logout-btn:hover {
            background: linear-gradient(135deg, #d1001f, #b80000);
            transform: scale(1.05);
            box-shadow: 0 6px 15px rgba(255, 0, 0, 0.4);
        }

        .profile-sidebar-pagecreate-admin .tab-content>.active {
            display: block;
            animation: fadeIn 0.3s ease-in;
        }

        @keyframes  fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
<?php $__env->stopPush(); ?>
<?php $__env->startSection('content'); ?>


    <div class="container-fluid p-0">

        <div class="d-flex align-items-center mb-3">
            <span class="me-2 h3 card-title">Home</span>
            <span class="h3 text-muted card-title">/</span>
            <span class="h3 ms-2 card-title">Profile Page</span>
        </div>

        <div class="profile-sidebar-pagecreate-admin">
            <div class="row">

                <!-- Sidebar -->
                <div class="col-md-4">
                    <div class="profile-sidebar text-center shadow-sm">
                        <img src="<?php echo e(asset('assets/img/a.png')); ?>" style="width:40%;" alt="User">
                        <h4 class="mt-3"><?php echo e(Auth::user()->name); ?></h4>
                        <p class="text-muted"><?php echo e(Auth::user()->city.",".Auth::user()->state); ?></p>

                        
                        <?php if(Myhelper::hasRole('admin')): ?>
                        <button class="btn btn-secondary tab-btn" data-target="mapping">Mapping</button>
                        <button class="btn btn-secondary tab-btn" data-target="pin">Pin</button>

                        <?php elseif(Myhelper::hasRole('Employee')): ?>
                        <button class="btn btn-secondary tab-btn" data-target="bank">Bank</button>
                        <?php elseif(Myhelper::hasRole(['merchant'])): ?>
                        <button class="btn btn-secondary tab-btn" data-target="merchant">Merchant</button>
                        
                        <?php endif; ?>
                        <button class="btn btn-secondary tab-btn" data-target="profile">Profile Details</button>
                        
                        <button class="btn btn-secondary tab-btn" data-target="password">Password Manager</button>
                        
                        
                        
                    </div>
                </div>

                <!-- Forms -->
                <div class="col-md-8">
                    <div class="account-settings shadow-sm">
                        <h5 class="mb-4">My Account</h5>
                        <div class="tab-content">

                            <!-- Profile Form -->
                            <div id="profile" class="active">
                                <form>
                                    <div class="mb-3 row">
                                        <div class="col-md-6">
                                            <label class="form-label">Username</label>
                                            <input type="text" class="form-control" value="<?php echo e(Auth::user()->name); ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Email </label>
                                            <input type="email" class="form-control" value="<?php echo e(Auth::user()->email); ?>">
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <div class="col-md-6">
                                            <label class="form-label">Mobile</label>
                                            <input type="text" class="form-control" value="<?php echo e(Auth::user()->mobile); ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">City</label>
                                            <input type="text" class="form-control" value="<?php echo e(Auth::user()->city); ?>">
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <div class="col-md-6">
                                            <label class="form-label">State</label>
                                            <input type="text" class="form-control" value="<?php echo e(Auth::user()->state); ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Pin Code</label>
                                            <input type="text" class="form-control" value="<?php echo e(Auth::user()->pincode); ?>">
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <div class="col-md-6">
                                            <label class="form-label">Gender</label>
                                            <input type="text" class="form-control" value="<?php echo e(Auth::user()->gender); ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">KYC Status</label>
                                            <input type="text" class="form-control" value="<?php echo e(Auth::user()->kyc); ?>" readonly>
                                        </div>
                                    </div>
                                    <?php if(Myhelper::hasRole('admin')): ?>
                                    <button type="submit" class="btn save-btn">Save Changes</button>
                                    <?php endif; ?>
                                </form>
                            </div>

                            <!-- KYC Form -->
                            <div id="kyc">
                                <form>
                                    <div class="mb-3">
                                        <label class="form-label">Aadhaar Number</label>
                                        <input type="text" class="form-control" value="<?php echo e(Auth::user()->aadharcard); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">PAN Number</label>
                                        <input type="text" class="form-control" value="<?php echo e(Auth::user()->pancard); ?>">
                                    </div>
                                    <?php if(Myhelper::hasRole('admin')): ?>
                                    <button type="submit" class="btn save-btn">Submit KYC</button>
                                    <?php endif; ?>
                                </form>
                            </div>

                            <!-- Password Form -->
                            <div id="password">
                                <form id="passwordForm" action="<?php echo e(route('profileUpdate')); ?>" method="POST" enctype="multipart/form-data">
                                    <?php echo e(csrf_field()); ?>

                                    <div class="row">
                                        <input type="hidden" name="id" value="<?php echo e(auth()->id()); ?>">
                                        <input type="hidden" name="actiontype" value="password">

                                    <div class="mb-3">
                                        <label class="form-label">Current Password</label>
                                        <input type="password" name="oldpassword" id="oldpassword" class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">New Password</label>
                                        <input type="password" class="form-control" name="newpassword" id="newpassword">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Confirm Password</label>
                                        <input type="password" class="form-control" name="password_confirmation" id="password_confirmation">
                                    </div>
                                    </div>
                                    <button type="submit" class="btn save-btn">Change Password</button>
                                </form>
                            </div>
                            <div id="bank">
                                <form>
                                    <div class="mb-3">
                                        <label class="form-label">Bank Name</label>
                                        <input type="text" class="form-control " >
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Branch</label>
                                        <input type="text" class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Customer ID</label>
                                        <input type="number" class="form-control">
                                    </div>
                                    <button type="submit" class="btn save-btn">Change Password</button>
                                </form>
                            </div>
                            <div id="role">
                                <form>
                                    <div class="mb-3">
                                        <label class="form-label">Your Role</label>
                                        <input type="text" class="form-control " value="<?php echo e(Auth::user()->role->name); ?>" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Brc</label>
                                        <input type="text" class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Role ID</label>
                                        <input type="number" class="form-control" value="<?php echo e(Auth::user()->role_id); ?>" readonly>
                                    </div>
                                    
                                </form>
                            </div>
                           
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>


<?php $__env->stopSection(); ?>

<?php $__env->startPush('script'); ?>



    <script> 
        $(document).ready(function() {
        $("#passwordForm").validate({
            rules: {
                oldpassword: {
                    required: true
                },
                newpassword: {
                    required: true,
                    minlength: 8
                },
                password_confirmation: {
                    required: true,
                    minlength: 8,
                    equalTo: "#newpassword"
                }
            },
            messages: {
                oldpassword: {
                    required: "Please enter your current password"
                },
                newpassword: {
                    required: "Please enter a new password",
                    minlength: "Your new password should be at least 8 characters"
                },
                password_confirmation: {
                    required: "Please confirm your new password",
                    minlength: "Password confirmation should be at least 8 characters",
                    equalTo: "Passwords do not match"
                }
            },
            errorElement: "p",
            errorClass: "text-danger small",
            submitHandler: function(form) {
                var $form = $(form);
                Swal.fire({
                    title: 'Updating Password',
                    text: 'Please wait while we update your password...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading()
                    }
                });

                $form.ajaxSubmit({
                    dataType: 'json',
                    success: function(data) {
                        Swal.close();
                        if (data.status === "success") {
                            Swal.fire({
                                title: "Success!",
                                text: "Password successfully changed",
                                icon: "success",
                                confirmButtonColor: "#3461ff"
                            });
                            $form[0].reset();
                        } else {
                            Swal.fire({
                                title: "Error!",
                                text: data.status,
                                icon: "error",
                                confirmButtonColor: "#3461ff"
                            });
                        }
                    },
                    error: function(xhr) {
                    
                    
                        Swal.fire({
                            title: "Error!",
                            text: xhr.responseJSON?.message || "An error occurred while processing your request.",
                            icon: "error",
                            confirmButtonColor: "#3461ff"
                        });
                    }
                    
                });
            }
        });
    });
</script>
    <script>
       
        const tabButtons = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content > div');

        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                const target = button.getAttribute('data-target');

                // Deactivate all tabs & buttons
                tabContents.forEach(content => content.classList.remove('active'));
                tabButtons.forEach(btn => btn.classList.remove('active'));

                // Activate current tab & button
                document.getElementById(target).classList.add('active');
                button.classList.add('active');

                
            });
        });

        // Activate the first tab by default on load
        window.addEventListener('DOMContentLoaded', () => {
            tabButtons[0].classList.add('active');
            tabContents[0].classList.add('active');
        });
    </script>

<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\template1\resources\views/profile/index.blade.php ENDPATH**/ ?>