<!--start content-->
@extends('layouts.app')
@section('title', ucwords($user->name) . ' Profile')
@section('bodyClass', 'has-detached-left')
@section('pagetitle', ucwords($user->name) . ' Profile')
@section('content')
<main class="page-content">
    <h2> Update Profile</h2>
  
    <div class="container my-5">
        <div class="accordion" id="profileAccordion">

            {{-- User Information --}}
            <div class="card shadow-sm mb-3">
                <div class="card-header" data-bs-toggle="collapse" data-bs-target="#userInfo" style="cursor: pointer;">
                    <h5 class="mb-0"><b>User Information</b></h5>
                </div>
                <div id="userInfo" class="collapse show" data-bs-parent="#profileAccordion">
                    <div class="card-body row">
                        <div class="col-md-4 mb-3"><label>Username</label><input type="text" class="form-control" name="username" value="{{ Auth::user()->name }}"></div>
                        <div class="col-md-4 mb-3"><label>Email address</label><input type="email" class="form-control" name="email" value="{{ Auth::user()->email }}"></div>
                        <div class="col-md-4 mb-3"><label>Role Name</label><input type="text" class="form-control" name="role" value="{{ Auth::user()->role->name }}"></div>
                    </div>
                </div>
            </div>
        
            {{-- Personal Information --}}
            <div class="card shadow-sm mb-3">
                <div class="card-header" data-bs-toggle="collapse" data-bs-target="#personalInfo" style="cursor: pointer;">
                    <h5 class="mb-0"><b>Personal Information</b></h5>
                </div>
                <div id="personalInfo" class="collapse show" data-bs-parent="#profileAccordion">
                    <div class="card-body row">
                        <div class="col-md-4 mb-3"><label>Name</label><input type="text" class="form-control" name="name" value="{{ Auth::user()->name }}"></div>
                        <div class="col-md-4 mb-3"><label>Mobile</label><input type="text" class="form-control" name="mobile" value="{{ Auth::user()->mobile }}"></div>
                        <div class="col-md-4 mb-3"><label>Email</label><input type="email" class="form-control" name="email_confirm" value="{{ Auth::user()->email }}"></div>
                        <div class="col-md-4 mb-3"><label>State</label><input type="text" class="form-control" name="state" value="{{ Auth::user()->state }}"></div>
                        <div class="col-md-4 mb-3"><label>City</label><input type="text" class="form-control" name="city" value="{{ Auth::user()->city }}"></div>
                        <div class="col-md-4 mb-3"><label>Pincode</label><input type="text" class="form-control" name="pincode" value="{{ Auth::user()->pincode }}"></div>
                        <div class="col-md-4 mb-3"><label>Address</label><input type="text" class="form-control" name="address" value="{{ Auth::user()->address }}"></div>
                    </div>
                </div>
            </div>
        
            {{-- UPI Credentials --}}
            <div class="card shadow-sm mb-3">
                <div class="card-header" data-bs-toggle="collapse" data-bs-target="#upiCreds" style="cursor: pointer;">
                    <h5 class="mb-0"><b>UPI Credentials</b></h5>
                </div>
                <div id="upiCreds" class="collapse" data-bs-parent="#profileAccordion">
                    <div class="card-body row">
                        <div class="col-md-6 mb-3"><label>UPI Id</label><input type="text" class="form-control" name="upi_client_id" value="{{ Auth::user()->upi_client_id }}"></div>
                        <div class="col-md-6 mb-3"><label>UPI Ref. Secret</label><input type="text" class="form-control" name="upi_client_secret" value="{{ Auth::user()->upi_client_secret }}"></div>
                    </div>
                </div>
            </div>
        
            {{-- Payout Credentials --}}
            <div class="card shadow-sm mb-3">
                <div class="card-header" data-bs-toggle="collapse" data-bs-target="#payoutCreds" style="cursor: pointer;">
                    <h5 class="mb-0"><b>Payout Credentials</b></h5>
                </div>
                <div id="payoutCreds" class="collapse" data-bs-parent="#profileAccordion">
                    <div class="card-body row">
                        <div class="col-md-4 mb-3"><label>Client Id</label><input type="text" class="form-control" name="payout_client_id" value="{{ Auth::user()->payout_client_id }}"></div>
                        <div class="col-md-4 mb-3"><label>Client Secret</label><input type="text" class="form-control" name="payout_client_secret" value="{{ Auth::user()->payout_client_secret }}"></div>
                    </div>
                </div>
            </div>
        
        </div>  
        <div class="mb-4 d-flex gap-3">
            @if (Myhelper::hasRole('admin'))
            <button class="btn btn-info" type="button" onclick="toggleCard('kycCard')">KYC Details</button>
            <button class="btn btn-info" type="button" onclick="toggleCard('passwordCard')">Password Manager</button>
            <button class="btn btn-info" type="button" onclick="toggleCard('mappingCard')">Mapping</button>
            <button class="btn btn-info" type="button" onclick="toggleCard('pinCard')">Pin</button>
            <button class="btn btn-info" type="button" onclick="toggleCard('bankCard')">Bank</button>
            @elseif(Myhelper::hasRole('Employee'))
            <button class="btn btn-info" type="button" onclick="toggleCard('roleCard')">Role</button>
            @elseif(Myhelper::hasRole(['merchant']))
            <button class="btn btn-info" type="button" onclick="toggleCard('merchantCard')">Merchant</button>
            @endif
            <button class="btn btn-info" type="button" onclick="toggleCard('passwordCard')">Password Manager</button>
            <button class="btn btn-info" type="button" onclick="toggleCard('kycCard')">KYC Details</button>
            <button class="btn btn-danger" type="button" onclick="toggleCard('logoutCard')">Logout</button>
        </div>

        {{-- KYC Card --}}
        <div id="kycCard" class="card p-4 mb-3 d-none">
            <h5>KYC Verification</h5>
            <p>Fill the relevant KYC information below:</p>
            <div class="row">
                <div class="col-md-4">
                    <label class="form-label">Aadhaar Card Number</label>
                    <input type="text" name="aadhaar_card" class="form-control" value="{{ old('aadhaar_card', Auth::user()->aadhaar_card) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">PAN Card Number</label>
                    <input type="text" name="pan_card" class="form-control" value="{{ old('pan_card', Auth::user()->pan_card) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Passport Number</label>
                    <input type="text" name="passport" class="form-control" value="{{ old('passport', Auth::user()->passport) }}">
                </div>
                @if(Myhelper::hasRole(['Admin']))
                <button class="btn btn-success mt-2">Save</button>
                @endif
            </div>
        </div>

        {{-- Password Manager Card --}}
        <div id="passwordCard" class="card p-4 mb-3 d-none">
            <h5>Update Password</h5>
            <form id="passwordForm" action="{{ route('profileUpdate') }}" method="POST" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="row">
                    <div class="col-md-4">
                        <input type="password" class="form-control mb-2" placeholder="Current Password" name="oldpassword" id="oldpassword" required>
                    </div>
                    <div class="col-md-4">
                        <input type="password" class="form-control mb-2" placeholder="New Password" name="password" id="password" required minlength="8">
                    </div>
                    <div class="col-md-4">
                        <input type="password" class="form-control mb-2" placeholder="Confirm New Password" name="password_confirmation" id="password_confirmation" required minlength="8" equalTo="#password">
                    </div>
                </div>
                <button class="btn btn-success mt-2" type="submit">Change Password</button>
            </form>
        </div>

        {{-- Logout Card --}}
        <div id="logoutCard" class="card p-4 mb-3 d-none">
            <h5>Logout Confirmation</h5>
            <p>Are you sure you want to logout?</p>
            <a class="dropdown-item" href="{{ route('logout') }}">
                Logout
            </a>
        </div>
    
    </div>
</main>


@endsection

@push('script')
<script>
    function toggleCard(id) {
        const cards = ['kycCard', 'passwordCard', 'logoutCard'];
        cards.forEach(card => {
            document.getElementById(card).classList.add('d-none');
        });
        document.getElementById(id).classList.remove('d-none');
    }

    $(document).ready(function() {
        $("#passwordForm").validate({
            rules: {
                oldpassword: {
                    required: true
                },
                password: {
                    required: true,
                    minlength: 8
                },
                password_confirmation: {
                    required: true,
                    minlength: 8,
                    equalTo: "#password"
                }
            },
            messages: {
                oldpassword: {
                    required: "Please enter your current password"
                },
                password: {
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
                        Swal.close();
                        showError(xhr.responseJSON, $form.find('.panel-body'));
                    }
                });
            }
        });
    });
</script>
@endpush
