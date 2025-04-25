@extends('layouts.app')
@section('title', 'Dashboard')
@section('pagetitle', 'Dashboard')
@section('content')




<div class="container-fluid p-0">

    <h1 class="h3 mb-3">Dashboard</h1>

    <div class="row">
        <div class="col-lg-4 col-md-6 col-12 d-flex ">
            <div class="card flex-fill">
                <div class="card-body">
                    <div class="row">
                        <div class="col mt-0">
                            <h5 class="card-title">UPI Wallet</h5>
                        </div>

                        <div class="col-auto">
                            <div class="stat">
                                <i class="fa-solid fa-indian-rupee-sign align-middle"></i>
                            </div>
                        </div>
                    </div>
                    <h4 class="mt-0 mb-1">₹{{Auth::user()->upiwallet  }}</h4>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-12 d-flex">
            <div class="card flex-fill">
                <div class="card-body">
                    <div class="row">
                        <div class="col mt-0">
                            <h5 class="card-title">Payout Wallet</h5>
                        </div>

                        <div class="col-auto">
                            <div class="stat" style="background: #F7931A; color: white;">
                                <i class="fa-solid fa-indian-rupee-sign align-middle"></i>
                            </div>
                        </div>
                    </div>
                    <h4 class="mt-0 mb-1">₹{{ Auth::user()->mainwallet }}</h4>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-12 d-flex">
            <div class="card flex-fill">
                <div class="card-body">
                    <div class="row">
                        <div class="col mt-0">
                            <h5 class="card-title">Dispute Wallet</h5>
                        </div>

                        <div class="col-auto">
                            <div class="stat" style="background: #345D9D; color: white;">
                                <i class="fa-solid fa-indian-rupee-sign align-middle"></i>
                            </div>
                        </div>
                    </div>
                    <h4 class="mt-0 mb-1">₹{{ Auth::user()->disputewallet }}</h4>
                </div>
            </div>
        </div>
        {{--  <div class="col-lg-3 col-md-6 col-12 d-flex">
            <div class="card flex-fill">
                <div class="card-body">
                    <div class="row">
                        <div class="col mt-0">
                            <h5 class="card-title">Other</h5>
                        </div>

                        <div class="col-auto">
                            <div class="stat" style="background: #627EEA; color: white;">
                                <i class="fa-solid fa-credit-card"></i>

                            </div>
                        </div>
                    </div>
                    <h4 class="mt-0 mb-1">0.07334</h4>

                    
                </div>
            </div>
        </div>  --}}
    
    </div>
    
    <div class="row">
        <div class="col-12 col-xl-6">
            <div class="card ">
                <div class="pay-heading-text mt-2 mx-3">
                    <h3>Pay-in Summary</h3>
                </div>
                <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th>Monthly Report </th>
                            <th class="text-end">Amount</th>
                        
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>This Month's Amount</td>
                            <td class="text-end" id="thisMonthPayinAmount">₹0</td>
                        </tr>
                        <tr>
                            <td>Last Month's Amount</td>
                            <td class="text-end" id="lastMonthPayinAmount">₹0</td>
                        </tr>
                        <tr>
                            <td>Today's Transactions</td>
                            <td class="text-end" id="todayPayinTxn">0</td>
                        </tr>
                        <tr>
                            <td>This Month's Transactions</td>
                            <td class="text-end" id="thisMonthPayinTxn">0</td>
                        </tr>
                        <tr>
                            <td>Last Months Transactions</td>
                            <td class="text-end" id="lastMonthPayinTxn">0</td>
                        </tr>
                        
                        
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-12 col-xl-6">
            <div class="card">
                <div class="pay-heading-text mt-2 mx-3">
                    <h3>Payout Summary</h3>
                </div>
                <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th> Monthly Report </th>
                            <th class="text-end">Amount</th>
                        
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>This Month's Amount</td>
                            <td class="text-end" id="thisMonthPayoutAmount">₹0</td>
                        </tr>
                        <tr>
                            <td>Last Month's Amount</td>
                            <td class="text-end" id="lastMonthPayoutAmount">₹0</td>
                        </tr>
                        <tr>
                            <td>Today's Transactions</td>
                            <td class="text-end" id="todayPayoutTxn">0</td>
                        </tr>
                        <tr>
                            <td>This Month's Transactions</td>
                            <td class="text-end" id="thisMonthPayoutTxn">0</td>
                        </tr>
                        <tr>
                            <td>Last Months Transactions</td>
                            <td class="text-end" id="lastMonthPayoutTxn">0</td>
                        </tr>
                        
                        
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    {{--  <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                
                    <div class="row">
                        <div class="col-xxl-4">
                          
                                <div class="pt-0 pb-2 d-flex justify-content-between align-items-center">
                                    <h4 class="header-title">Top 10 Transaction History</h4>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive-sm">
                                        <table class="table table-bordered mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Id</th>
                                                    <th>User</th>
                                                    <th>Account No.</th>
                                                    <th>Balance</th>
                                                    <th class="text-center">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>1</td>
                                                    <td>test</td>
                                                    <td>123456789</td>
                                                    <td>₹55</td>
                                                    <td class="text-center">
                                                        <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Active</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>2</td>
                                                    <td>test</td>
                                                    <td>123456789</td>
                                                    <td>₹55</td>
                                                    <td class="text-center">
                                                        <span class="badge bg-primary"><i class="fas fa-thumbs-up me-1"></i> Success</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>3</td>
                                                    <td>test</td>
                                                    <td>123456789</td>
                                                    <td>₹55</td>
                                                    <td class="text-center">
                                                        <span class="badge bg-warning text-dark"><i class="fas fa-hourglass-half me-1"></i> Pending</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>4</td>
                                                    <td>test</td>
                                                    <td>123456789</td>
                                                    <td>₹55</td>
                                                    <td class="text-center">
                                                        <span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i> Failed</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>5</td>
                                                    <td>test</td>
                                                    <td>123456789</td>
                                                    <td>₹55</td>
                                                    <td class="text-center">
                                                        <span class="badge bg-info text-dark"><i class="fas fa-spinner fa-spin me-1"></i> Processing</span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                
                           
                        </div> <!-- end col-->
    
                      
                    </div>
                 

                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div><!-- end col-->
    </div>  --}}
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
                          $('#lastMonthPayoutTxn').text(result.datas.payout[0].lastMonthTxnCount);
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