<div class="tab-content mt-4">
    <!-- Profile Form -->
    <div class="tab-pane fade show active" id="profile">
        <form id="profileForm" action="{{ route('profileUpdate') }}" method="post" enctype="multipart/form-data">
            {{ csrf_field() }}
            <input type="hidden" name="id" value="{{ $user->id }}">
            <input type="hidden" name="actiontype" value="profile">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Personal Information</h3>
                </div>
                <hr>
                <div class="panel-body p-b-0">
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control" value="{{ $user->name }}"
                                required="" placeholder="Enter Value">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Mobile</label>
                            <input type="number" {{ Myhelper::hasNotRole('admin') ? 'disabled=""' : 'name=mobile' }}
                                required="" value="{{ $user->mobile }}" class="form-control"
                                placeholder="Enter Value">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" value="{{ $user->email }}"
                                value="" required="" placeholder="Enter Value">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label>State</label>
                            <select name="state" class="form-control select" required="">
                                <option value="">Select State</option>
                                @foreach ($state as $state)
                                    <option value="{{ $state->state }}">{{ $state->state }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label>City</label>
                            <input type="text" name="city" class="form-control" value="{{ $user->city }}"
                                required="" placeholder="Enter Value">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Pincode</label>
                            <input type="number" name="pincode" class="form-control" value="{{ $user->pincode }}"
                                required="" maxlength="6" minlength="6" placeholder="Enter Value">
                        </div>
                    </div>

            
                    <h3 class="hrLine m-auto">UPI Credentials</h3>
                    <hr>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>Client Id</label>
                            <input type="text" name="clientId" class="form-control" value="{{ $user->clientId }}"
                                placeholder="Enter Value">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Client Secret</label>
                            <input type="text" name="clientSecret" class="form-control"
                                value="{{ $user->clientSecret }}" placeholder="Enter Value">
                        </div>
                    </div>
                

                    <h3 class="hrLine">Payout Credentials</h3>
                    <hr>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>Client Id</label>
                            <input type="text" name="pclientId" class="form-control" value="{{ $user->pclientId }}"
                                placeholder="Enter Value">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Client Secret</label>
                            <input type="text" name="pclientSecret" class="form-control"
                                value="{{ $user->pclientSecret }}" placeholder="Enter Value">
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-12">
                            <label>Address</label>
                            <textarea name="address" class="form-control" rows="3" required="" placeholder="Enter Value">{{ $user->address }}</textarea>
                        </div>
                    </div>
                    @if (Myhelper::hasRole('admin'))
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label>Security Pin</label>
                                <input type="password" name="mpin" autocomplete="off" class="form-control"
                                    required="">
                            </div>
                        </div>
                    @endif
                </div>
                @if ((Auth::id() == $user->id && Myhelper::can('profile_edit')) || Myhelper::can('member_profile_edit'))
                    <div class="panel-footer">
                        <button class="btn bg-success btn-raised legitRipple pull-right" type="submit"
                            data-loading-text="<i class='fa fa-spin fa-spinner'></i> Updating...">Update
                            Profile</button>
                    </div>
                @endif
            </div>
        </form>
    </div>

    <!-- KYC Form -->

    <div class="tab-pane fade" id="kycdata">
        <form id="kycForm" action="{{ route('profileUpdate') }}" method="post" enctype="multipart/form-data">
            {{ csrf_field() }}
            <input type="hidden" name="id" value="{{ $user->id }}">
            <input type="hidden" name="actiontype" value="profile">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Kyc Data</h3>
                </div>
                <div class="panel-body p-b-0">
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label>Shop Name</label>
                            <input type="text" name="shopname" class="form-control"
                                value="{{ $user->shopname }}" required="" placeholder="Enter Value">
                        </div>

                        <div class="form-group col-md-4">
                            <label>Gst Number</label>
                            <input type="text" name="gstin" class="form-control" value="{{ $user->gstin }}"
                                placeholder="Enter Value">
                        </div>

                        <div class="form-group col-md-4">
                            <label>Pancard Number</label>
                            <input type="text" name="pancard" class="form-control" value="{{ $user->pancard }}"
                                required="" placeholder="Enter Value"
                                @if (Myhelper::hasNotRole('admin') && $user->kyc == 'verified') disabled="" @endif>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label>Adhaarcard Number</label>
                            <input type="text" name="aadharcard" class="form-control"
                                value="{{ $user->aadharcard }}" required="" placeholder="Enter Value"
                                maxlength="12" minlength="12" @if (Myhelper::hasNotRole('admin') && $user->kyc == 'verified') disabled="" @endif>
                        </div>
                        <div class="form-group col-md-4">
                            @if ($user->pancardpic)
                                <div class="thumbnail col-md-6">
                                    <a href="{{ asset('public/kyc') }}/{{ $user->pancardpic }}" target="_blank">
                                        <img src="{{ asset('public/kyc') }}/{{ $user->pancardpic }}" alt="">
                                        Pancard Pic
                                    </a>
                                </div>
                            @endif

                            @if ($user->aadharcardpic)
                                <div class="thumbnail col-md-6">
                                    <a href="{{ asset('public/kyc') }}/{{ $user->aadharcardpic }}" target="_blank">
                                        <img src="{{ asset('public/kyc') }}/{{ $user->aadharcardpic }}"
                                            alt="">
                                        Aadharcard Pic
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        @if ($user->kyc != 'verified')
                            <div class="form-group col-md-4">
                                <label>Pancard Pic</label>
                                <input type="file" name="pancardpics" class="form-control" value=""
                                    placeholder="Enter Value">
                            </div>

                            <div class="form-group col-md-4">
                                <label>Adhaarcard Pic</label>
                                <input type="file" name="aadharcardpics" class="form-control" value=""
                                    placeholder="Enter Value">
                            </div>
                        @endif
                    </div>
                    @if (Myhelper::hasRole('admin'))
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label>Security Pin</label>
                                <input type="password" name="mpin" autocomplete="off" class="form-control"
                                    required="">
                            </div>
                        </div>
                    @endif
                </div>
                @if ((Auth::id() == $user->id && Myhelper::can('profile_edit')) || Myhelper::can('member_profile_edit'))
                    <div class="panel-footer">
                        <button class="btn bg-success btn-raised legitRipple pull-right" type="submit"
                            data-loading-text="<i class='fa fa-spin fa-spinner'></i> Updating...">Update
                            Profile</button>
                    </div>
                @endif
            </div>
        </form>
    </div>

    <div class="tab-pane fade" id="mapping">
        <form id="memberForm" action="{{ route('profileUpdate') }}" method="post">
            {{ csrf_field() }}
            <input type="hidden" name="id" value="{{ $user->id }}">
            <input type="hidden" name="actiontype" value="mapping">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Change Mapping</h3>
                </div>
                <div class="panel-body p-b-0">
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label>Parent Member</label>
                            <select name="parent_id" class="form-control select" required="">
                                <option value="">Select Member</option>
                                @foreach ($parents as $parent)
                                    <option value="{{ $parent->id }}">{{ $parent->name }} ({{ $parent->mobile }})
                                        ({{ $parent->role->name }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @if (Myhelper::hasRole('admin'))
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label>Security Pin</label>
                                <input type="password" name="mpin" autocomplete="off" class="form-control"
                                    required="">
                            </div>
                        </div>
                    @endif
                </div>
                <div class="panel-footer">
                    <button class="btn bg-success btn-raised legitRipple pull-right" type="submit"
                        data-loading-text="<i class='fa fa-spin fa-spinner'></i> Changing...">Change</button>
                </div>
            </div>
        </form>
    </div>

    @if (\Myhelper::hasRole('admin') || Auth::id() == $user->id)
        <div class="tab-pane fade" id="settings">
            <form id="passwordForm" action="{{ route('profileUpdate') }}" method="post"
                enctype="multipart/form-data">
                {{ csrf_field() }}
                <input type="hidden" name="id" value="{{ $user->id }}">
                <input type="hidden" name="actiontype" value="password">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title pull-left">Password Reset</h3>
                        @if (Myhelper::hasRole('admin'))
                            <p class="pull-right">Current Password - {{ $user->passwordold }}</p>
                        @endif
                        <div class="clearfix"></div>
                    </div>
                    <div class="panel-body p-b-0">
                        <div class="row">
                            @if (Auth::id() == $user->id || Myhelper::hasNotRole('admin'))
                                <div class="form-group col-md-4">
                                    <label>Old Password</label>
                                    <input type="password" name="oldpassword" class="form-control" required=""
                                        placeholder="Enter Value">
                                </div>
                            @endif

                            <div class="form-group col-md-4">
                                <label>New Password</label>
                                <input type="password" name="password" id="password" class="form-control"
                                    required="" placeholder="Enter Value">
                            </div>
                            @if (Auth::id() == $user->id || (Myhelper::hasNotRole('admin') && !Myhelper::can('member_password_reset')))
                                <div class="form-group col-md-4">
                                    <label>Confirmed Password</label>
                                    <input type="password" name="password_confirmation" class="form-control"
                                        required="" placeholder="Enter Value">
                                </div>
                            @endif
                        </div>
                        @if (Myhelper::hasRole('admin'))
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label>Security Pin</label>
                                    <input type="password" name="mpin" autocomplete="off" class="form-control"
                                        required="">
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="panel-footer">
                        <button class="btn bg-success btn-raised legitRipple pull-right" type="submit"
                            data-loading-text="<i class='fa fa-spin fa-spinner'></i> Resetting...">Password
                            Reset</button>
                    </div>
                </div>
            </form>
        </div>
    @endif

    <div class="tab-pane fade" id="pinChange">
        <form id="pinForm" action="{{ route('setpin') }}" method="post" enctype="multipart/form-data">
            {{ csrf_field() }}
            <input type="hidden" name="id" value="{{ $user->id }}">
            <input type="hidden" name="mobile" value="{{ $user->mobile }}">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">Pin Reset</h4>
                </div>
                <div class="panel-body p-b-0">
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label>New Pin</label>
                            <input type="password" name="pin" id="pin" class="form-control"
                                required="" placeholder="Enter Value">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Confirmed Pin</label>
                            <input type="password" name="pin_confirmation" class="form-control" required=""
                                placeholder="Enter Value">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Otp</label>
                            <input type="password" name="otp" class="form-control" Placeholder="Otp" required>
                        </div>
                        <a href="javascript:void(0)" onclick="OTPRESEND()" class="text-primary pull-right">Get
                            Otp</a>
                    </div>
                </div>
                <div class="panel-footer">
                    <button class="btn bg-success btn-raised legitRipple pull-right" type="submit"
                        data-loading-text="<i class='fa fa-spin fa-spinner'></i> Resetting...">Password Reset</button>
                </div>
            </div>
        </form>
    </div>
    @if (\Myhelper::hasRole('admin'))
        <div class="tab-pane fade" id="bankdata">
            <form id="bankForm" action="{{ route('profileUpdate') }}" method="post">
                {{ csrf_field() }}
                <input type="hidden" name="id" value="{{ $user->id }}">
                <input type="hidden" name="actiontype" value="bankdata">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Bank Details</h3>
                    </div>
                    <div class="panel-body p-b-0">
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label>Account Number1</label>
                                <input type="text" name="account" class="form-control"
                                    value="{{ $user->account }}" required="" placeholder="Enter Value">
                            </div>

                            <div class="form-group col-md-4">
                                <label>Bank Name1</label>
                                <input type="text" name="bank" class="form-control"
                                    value="{{ $user->bank }}" placeholder="Enter Value">
                            </div>

                            <div class="form-group col-md-4">
                                <label>Ifsc Code1</label>
                                <input type="text" name="ifsc" class="form-control"
                                    value="{{ $user->ifsc }}" required="" placeholder="Enter Value">
                            </div>
                        </div>
                    </div>
                    <div class="panel-body p-b-0">
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label>Account Number2</label>
                                <input type="text" name="account2" class="form-control"
                                    value="{{ $user->account2 }}" placeholder="Enter Value">
                            </div>

                            <div class="form-group col-md-4">
                                <label>Bank Name2</label>
                                <input type="text" name="bank2" class="form-control"
                                    value="{{ $user->bank2 }}" placeholder="Enter Value">
                            </div>

                            <div class="form-group col-md-4">
                                <label>Ifsc Code2</label>
                                <input type="text" name="ifsc2" class="form-control"
                                    value="{{ $user->ifsc2 }}" placeholder="Enter Value">
                            </div>
                        </div>
                    </div>
                    <div class="panel-body p-b-0">
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label>Account Number3</label>
                                <input type="text" name="account3" class="form-control"
                                    value="{{ $user->account3 }}" placeholder="Enter Value">
                            </div>

                            <div class="form-group col-md-4">
                                <label>Bank Name3</label>
                                <input type="text" name="bank3" class="form-control"
                                    value="{{ $user->bank3 }}" placeholder="Enter Value">
                            </div>

                            <div class="form-group col-md-4">
                                <label>Ifsc Code3</label>
                                <input type="text" name="ifsc3" class="form-control"
                                    value="{{ $user->ifsc3 }}" placeholder="Enter Value">
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer">
                        <button class="btn bg-success btn-raised legitRipple pull-right" type="submit"
                            data-loading-text="<i class='fa fa-spin fa-spinner'></i> Changing...">Change</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="tab-pane fade" id="rolemanager">
            <form id="roleForm" action="{{ route('profileUpdate') }}" method="post">
                {{ csrf_field() }}
                <input type="hidden" name="id" value="{{ $user->id }}">
                <input type="hidden" name="actiontype" value="rolemanager">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Role Manager</h3>
                    </div>
                    <div class="panel-body p-b-0">
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label>Member Role</label>
                                <select name="role_id" class="form-control select" required="">
                                    <option value="">Select Role</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @if (Myhelper::hasRole('admin'))
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label>Security Pin</label>
                                    <input type="password" name="mpin" autocomplete="off" class="form-control"
                                        required="">
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="panel-footer">
                        <button class="btn bg-success btn-raised legitRipple pull-right" type="submit"
                            data-loading-text="<i class='fa fa-spin fa-spinner'></i> Changing...">Change</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="tab-pane fade" id="mapping">
            <form id="memberForm" action="{{ route('profileUpdate') }}" method="post">
                {{ csrf_field() }}
                <input type="hidden" name="id" value="{{ $user->id }}">
                <input type="hidden" name="actiontype" value="mapping">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Change Mapping</h3>
                    </div>
                    <div class="panel-body p-b-0">
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label>Parent Member</label>
                                <select name="parent_id" class="form-control select" required="">
                                    <option value="">Select Member</option>
                                    @foreach ($parents as $parent)
                                        <option value="{{ $parent->id }}">{{ $parent->name }}
                                            ({{ $parent->mobile }}) ({{ $parent->role->name }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @if (Myhelper::hasRole('admin'))
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label>Security Pin</label>
                                    <input type="password" name="mpin" autocomplete="off" class="form-control"
                                        required="">
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="panel-footer">
                        <button class="btn bg-success btn-raised legitRipple pull-right" type="submit"
                            data-loading-text="<i class='fa fa-spin fa-spinner'></i> Changing...">Change</button>
                    </div>
                </div>
            </form>
        </div>
    @endif


</div>
<!-- End Tab Content -->

                </div>
            </div>
        </div>
    </div>  
    <style>
        /* ---- Panel Global Styling ---- */
        .panel {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            margin-bottom: 20px;
            background-color: #fff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }
    
        .panel-heading {
            padding: 20px 20px;
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            border-top-left-radius: 5px;
            border-top-right-radius: 5px;
        }
    
        .panel-heading h3, .panel-heading h4 {
            margin: 0;
            font-size: 15px;
            font-weight: 600;
            color: #333;
        }
    
        .panel-body {
            padding: 5px;
        }
    
        /* ---- Form Styling ---- */
        .form-group {
            margin-bottom: 25px;
        }
    
        .form-group label {
            font-weight: 500;
          
            display: block;
            color: #495057;
            font-size: 15px;
        }
    
        .form-control {
            padding: 10px 15px;
            font-size: 15px;
            border-radius: 6px;
            border: 1px solid #ced4da;
            transition: all 0.3s ease;
        }
    
    
        .select {
            height: 30px;
            padding: 5px 15px;
        }
    
        textarea.form-control {
            resize: vertical;
            min-height: 120px;
        }
    
        /* ---- HR Styling ---- */
        hr {
            margin: 10px 0;
            border: 0;
            border-top: 1px solid #dee2e6;
        }
    
        /* ---- Section Title Lines ---- */
        .hrLine {
            font-size: 18px;
            font-weight: 600;
           
            margin: 10px 0 10px;
            color: #040505;
            position: relative;
        }
    
      
    
        /* ---- Panel Footer ---- */
        .panel-footer {
            padding: 10px 15px;
            background-color: #f1f1f1;
            border-top: 1px solid #dee2e6;
            border-bottom-left-radius: 8px;
            border-bottom-right-radius: 8px;
            
    
        /* ---- Buttons ---- */
        .btn.bg-success {
            padding: 10px 20px;
            font-size: 16px;
            font-weight: 500;
            border-radius: 6px;
            background-color: #00c292;
            border: none;
            color: white;
            transition: background 0.3s ease;
        }
    
        .btn.bg-success:hover {
            background-color: #00a87d;
        }
    
        /* ---- Thumbnail Images (for KYC Uploads) ---- */
        .thumbnail {
            padding: 5px;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            text-align: center;
            background: #f8f9fa;
            margin-top: 10px;
        }
    
        .thumbnail img {
            width: 100%;
            height: auto;
            border-radius: 4px;
        }
    
        .thumbnail a {
            display: block;
            font-size: 14px;
            color: #007bff;
            margin-top: 5px;
        }
    
        /* ---- Tab Content Styling ---- */
        .tab-content {
            margin-top: 30px;
        }
    
        .tab-pane {
            padding-top: 30px;
        }
    
        /* ---- Link Styling (for Get OTP) ---- */
        .text-primary {
            color: #26c49d !important;
            font-weight: 500;
            font-size: 14px;
            margin-top: 10px;
            display: inline-block;
        }
    
        .text-primary:hover {
            color: #00a87d !important;
            text-decoration: underline;
        }
    </style>
    