@extends('layouts.app')
@section('title', 'Dashboard')
@section('pagetitle', 'Dashboard')
@section('content')
    <div class="row">
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-2 row-cols-xl-3">
            <div class="col">
                <div class="card radius-10">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="mb-0 text-secondary">UPI Wallet</p>
                                <h4 class="my-1">₹ {{ Auth::user()->upiwallet }}</h4>
                                {{--  <p class="mb-0 font-13 text-success"><i class="bi bi-caret-up-fill"></i> 5% from last week
                                </p>  --}}
                            </div>
                            <div class="widget-icon-large bg-gradient-purple text-white ms-auto"><i
                                    class="bi bi-basket2-fill"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card radius-10">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="mb-0 text-secondary">Payout Wallet</p>
                                <h4 class="my-1">₹ {{ Auth::user()->mainwallet }}</h4>
                                {{--  <p class="mb-0 font-13 text-success"><i class="bi bi-caret-up-fill"></i> 4.6 from last
                                    week</p>  --}}
                            </div>
                            <div class="widget-icon-large bg-gradient-success text-white ms-auto"><i
                                    class="bi bi-currency-exchange"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card radius-10">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="mb-0 text-secondary">Total Customers</p>
                                <h4 class="my-1">₹ {{ Auth::user()->disputewallet }}</h4>
                                {{--  <p class="mb-0 font-13 text-danger"><i class="bi bi-caret-down-fill"></i> 2.7 from last
                                    week</p>  --}}
                            </div>
                            <div class="widget-icon-large bg-gradient-danger text-white ms-auto"><i
                                    class="bi bi-people-fill"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div><!--end row-->

{{--  
        <div class="row">
            <div class="col-12 col-lg-6 col-xl-6 d-flex">
                <div class="card radius-10 w-100">
                    <div class="card-header bg-transparent">
                        <div class="row g-3 align-items-center">
                            <div class="col">
                                <h5 class="mb-0">Statistics</h5>
                            </div>
                            <div class="col">
                                <div class="d-flex align-items-center justify-content-end gap-3 cursor-pointer">
                                    <div class="dropdown">
                                        <a class="dropdown-toggle dropdown-toggle-nocaret" href="#"
                                            data-bs-toggle="dropdown" aria-expanded="false"><i
                                                class="bx bx-dots-horizontal-rounded font-22 text-option"></i>
                                        </a>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="javascript:;">Action</a>
                                            </li>
                                            <li><a class="dropdown-item" href="javascript:;">Another action</a>
                                            </li>
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            <li><a class="dropdown-item" href="javascript:;">Something else here</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-lg-flex align-items-center justify-content-center gap-4">
                            <div id="chart3"></div>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item"><i class="bi bi-circle-fill text-purple me-1"></i> Visitors:
                                    <span class="me-1">89</span>
                                </li>
                                <li class="list-group-item"><i class="bi bi-circle-fill text-info me-1"></i> Subscribers:
                                    <span class="me-1">45</span>
                                </li>
                                <li class="list-group-item"><i class="bi bi-circle-fill text-pink me-1"></i> Contributor:
                                    <span class="me-1">35</span>
                                </li>
                                <li class="list-group-item"><i class="bi bi-circle-fill text-success me-1"></i> Author:
                                    <span class="me-1">62</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6 col-xl-6 d-flex">
                <div class="card radius-10 w-100">
                    <div class="card-body">
                        <div class="row row-cols-1 row-cols-lg-2 g-3 align-items-center">
                            <div class="col">
                                <h5 class="mb-0">Product Actions</h5>
                            </div>
                            <div class="col">
                                <div class="d-flex align-items-center justify-content-sm-end gap-3 cursor-pointer">
                                    <div class="font-13"><i class="bi bi-circle-fill text-primary"></i><span
                                            class="ms-2">Views</span></div>
                                    <div class="font-13"><i class="bi bi-circle-fill text-pink"></i><span
                                            class="ms-2">Clicks</span></div>
                                </div>
                            </div>
                        </div>
                        <div id="chart4"></div>
                    </div>
                </div>
            </div>
        </div>  --}}
        <!--end row-->

        <div class="card radius-10">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12 col-lg-4 col-xl-4 d-flex">
                        <div class="card mb-0 radius-10 border shadow-none w-100">
                            <div class="card-body">
                                <h5 class="card-title">Pay-in Summary</h5>
                                <h4 class="mt-4"><i class="flag-icon flag-icon-in"></i></h4>
                                <p class="mb-0 text-secondary font-13">Our Most Customers in India</p>
                                <ul class="list-group list-group-flush mt-3">
                                    <li class="list-group-item border-top">
                                        <div class="d-flex align-items-center gap-2">
                                            <div></div>
                                            <div>Today’s Amount </div>
                                            <div class="ms-auto"><span id="todayPayinAmount" class="fw-bold">₹0</div>
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="d-flex align-items-center gap-2">
                                            <div></div>
                                            <div>This Month’s Amount</div>
                                            <div class="ms-auto"><span id="lastMonthPayinAmount"
                                                    class="fw-bold">₹0</span></div>
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="d-flex align-items-center gap-2">
                                            <div></div>
                                            <div> Last Month’s Amount</div>
                                            <div class="ms-auto"><span id="lastMonthPayinAmount"
                                                    class="fw-bold">₹0</span></div>
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="d-flex align-items-center gap-2">
                                            <div> </div>
                                            <div>Today’s Transactions</div>
                                            <div class="ms-auto"><span id="todayPayinTxn" class="fw-bold">0</span>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="d-flex align-items-center gap-2">
                                            <div></div>
                                            <div>This Month’s Transactions </div>
                                            <div class="ms-auto"><span id="thisMonthPayinTxn" class="fw-bold">0</span>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="d-flex align-items-center gap-2">
                                            <div></div>
                                            <div>Last Month’s Transactions</div>
                                            <div class="ms-auto"><span id="lastMonthPayinTxn" class="fw-bold">0</span>
                                            </div>
                                        </div>
                                    </li>

                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-8 col-xl-8 d-flex">
                        <div class="card mb-0 radius-10 border shadow-none w-100">
                            <div class="card-body">
                                <h5 class="card-title">Payout Summary</h5>
                                <h4 class="mt-4"><i class="flag-icon flag-icon-in"></i></h4>
                                <p class="mb-0 text-secondary font-13">Our Most Customers in India</p>
                                <ul class="list-group list-group-flush mt-3">
                                    <li class="list-group-item border-top">
                                        <div class="d-flex align-items-center gap-2">
                                            <div></div>
                                            <div>Today’s Amount </div>
                                            <div class="ms-auto"><span id="todayPayoutAmount" class="fw-bold">₹0</span>
                                            </div>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="d-flex align-items-center gap-2">
                                            <div></div>
                                            <div>This Month’s Amount</div>
                                            <div class="ms-auto"> <span id="thisMonthPayoutAmount"
                                                    class="fw-bold">₹0</span> </div>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="d-flex align-items-center gap-2">
                                            <div></div>
                                            <div> Last Month’s Amount</div>
                                            <div class="ms-auto"><span id="lastMonthPayoutAmount"
                                                    class="fw-bold">₹0</span></div>
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="d-flex align-items-center gap-2">
                                            <div></div>
                                            <div>Today’s Transactions</div>
                                            <div class="ms-auto"><span id="todayPayoutTxn" class="fw-bold">0</span>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="d-flex align-items-center gap-2">
                                            <div></div>
                                            <div>This Month’s Transactions </div>
                                            <div class="ms-auto"><span id="thisMonthPayoutTxn" class="fw-bold">0</span>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="d-flex align-items-center gap-2">
                                            <div></div>
                                            <div>Last Month’s Transactions</div>
                                            <div class="ms-auto"><span id="lastMonthPayoutTxn" class="fw-bold">0</span>
                                            </div>
                                        </div>
                                    </li>

                                </ul>
                            </div>
                        </div>
                    </div><!--end row-->

                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            function fetchDashboardData() {
                $.ajax({
                    url: "{{ route('getdatas') }}",
                    type: "GET",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    success: function(result) {
                        $('#todayPayinAmount').text('₹ ' + result.datas.payin[0].todayAmount);
                        $('#thisMonthPayinAmount').text('₹ ' + result.datas.payin[0].thisMonthAmount);
                        $('#lastMonthPayinAmount').text('₹ ' + result.datas.payin[0].lastMonthAmount);
                        $('#todayPayinTxn').text(result.datas.payin[0].todayTxnCount);
                        $('#thisMonthPayinTxn').text(result.datas.payin[0].thisMonthTxnCount);
                        $('#lastMonthPayinTxn').text(result.datas.payin[0].lastMonthTxnCount);

                        $('#todayPayoutAmount').text('₹ ' + result.datas.payout[0].todayAmount);
                        $('#thisMonthPayoutAmount').text('₹ ' + result.datas.payout[0].thisMonthAmount);
                        $('#lastMonthPayoutAmount').text('₹ ' + result.datas.payout[0].lastMonthAmount);
                        $('#todayPayoutTxn').text(result.datas.payout[0].todayTxnCount);
                        $('#thisMonthPayoutTxn').text(result.datas.payout[0].thisMonthTxnCount);
                        {{--  $('#lastMonthPayoutTxn').text(result.datas.payout[0].lastMonthTxnCount);  --}}
                    },
                    error: function() {
                        console.error("Error fetching data");
                    }
                });
            }

            fetchDashboardData();
        });
    </script>
@endpush
