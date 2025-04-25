<div class="card border-0 shadow-sm mt-4" style="background: rgba(52, 97, 255, 0.1); color: #212529;">
    <div class="card-header text-white" >
        <h4 class="card-title mb-0" style="color:black;"><i class="fas fa-filter"></i> Search Schedule</h4>
    </div>
    <form id="searchForm">
        <div class="card-body">
            <div class="row g-3">
                <!-- Date Input -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="mydate" class="fw-bold"><i class="far fa-calendar-alt"></i> Date</label>
                        <input type="text" class="form-control border-0 shadow-sm" id="mydate" placeholder="Select Date">
                    </div>
                </div>

                <!-- Search Text -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="searchtext" class="fw-bold"><i class="fas fa-search"></i> Search</label>
                        <input type="text" name="searchtext" class="form-control border-0 shadow-sm" placeholder="Enter search value">
                    </div>
                </div>

                <!-- Agent Input (Conditional) -->
                @if (Myhelper::hasNotRole(['retailer', 'apiuser', 'whitelable']))
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="agent" class="fw-bold"><i class="fas fa-user-tie"></i> Agent ID / Parent ID</label>
                        <input type="text" name="agent" class="form-control border-0 shadow-sm" placeholder="Enter Agent ID">
                    </div>
                </div>
                @endif

                <!-- Status Dropdown -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="status" class="fw-bold"><i class="fas fa-list-alt"></i> Status</label>
                        <select name="status" class="form-select border-0 shadow-sm">
                            <option value="">Select {{ $status['type'] ?? '' }} Status</option>
                            @if (isset($status['data']) && sizeof($status['data']) > 0)
                                @foreach ($status['data'] as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>

                <!-- Product Dropdown (Conditional) -->
                @if (isset($product))
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="product" class="fw-bold"><i class="fas fa-box"></i> Product</label>
                        <select name="product" class="form-select border-0 shadow-sm">
                            <option value="">Select {{ $product['type'] ?? '' }}</option>
                            @if (isset($product['data']) && sizeof($product['data']) > 0)
                                @foreach ($product['data'] as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                @endif
            </div>

            <!-- Buttons -->
            <div class="row mt-4">
                <div class="col-12 d-flex justify-content-between">
                    <button type="button" class="rounded-pill btn btn-success fw-bold btn-sm px-4 shadow-sm {{ isset($export) ? '' : 'd-none' }}" 
                            product="{{ $export ?? '' }}" id="reportExport" style="background-color: #95e7a8; border:none;>
                        <i class="fas fa-upload"></i> Export
                    </button>
                    <div class="margin-auto">
                    <button type="submit" class=" btn px-4 shadow-sm rounded-pill fw-bold" id="searchBtn" style=" background-color:#fff;">
                        <i class="fas fa-search"></i> Search
                    </button>
                    <button type="button" class="btn btn-secondary px-4 shadow-sm rounded-pill" id="formReset">
                        <i class="fas fa-sync-alt"></i> Reset
                    </button>
                    </div>
                    
                </div>
            </div>
        </div>
    </form>
</div>
<div id="loadingOverlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(255,255,255,0.8); z-index:9999; text-align:center;">
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
        
        <h3  style="color:black;">Loading Just a sec...</h3>
    </div>
</div>
@section('script')
<!-- jQuery and jQuery UI -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>

<script>
    $(document).ready(function () {
        var url = "{{ url('statement/fetch') }}/payoutstatement/{{ $id }}";

        // Setup datepicker
        $("#mydate").datepicker({
            dateFormat: "yy-mm-dd"
        });

        // Show loader when submitting the search form
        $('#searchForm').on('submit', function (e) {
            e.preventDefault(); // prevent default form submit
            $('#loadingOverlay').show();
            issearchclicked = true;
            $('#dataTables').DataTable().ajax.reload();
        });

        // Reset form
        $('#formReset').on('click', function () {
            $('#searchForm')[0].reset();
            $('#mydate').val('');
            issearchclicked = false;
            $('#dataTables').DataTable().ajax.reload();
        });

        // Loader hide callback
        var onDraw = function () {
            $('#loadingOverlay').hide();
        };

        // Initialize datatable
        datatableSetup(url, options, onDraw);
    });
</script>

@endsection
