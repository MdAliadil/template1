<div class="card border-0 shadow-sm mt-4" >
    <div class="card-header py-2 px-2 " style="background:#ebeef0;" >
        <h4 class="card-title text-muted mb-0 mx-2" ><i class="fas fa-filter"></i> Search Schedule</h4>
    </div>
    <form id="searchForm">
        <div class="card-body">
            <div class="row g-3">
                <!-- Date Input -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="mydate" class="fw-bold text-muted"><i class="far fa-calendar-alt"></i> Date</label>
                        <input type="text" class="form-control border-0 text-muted shadow-lg" id="mydate" placeholder="Select Date">
                    </div>
                </div>

                <!-- Search Text -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="searchtext" class="fw-bold text-muted"><i class="fas fa-search"></i> Search</label>
                        <input type="text" name="searchtext" class="form-control border-0 shadow-lg text-muted" placeholder="Enter search value">
                    </div>
                </div>

                <!-- Agent Input (Conditional) -->
                @if (Myhelper::hasNotRole(['retailer', 'apiuser', 'whitelable']))
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="agent" class="fw-bold text-muted"><i class="fas fa-user-tie"></i> Agent ID / Parent ID</label>
                        <input type="text" name="agent" class="form-control border-0 shadow-lg text-muted" placeholder="Enter Agent ID">
                    </div>
                </div>
                @endif

                <!-- Status Dropdown -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="status" class="fw-bold text-muted"><i class="fas fa-list-alt"></i> Status</label>
                        <select name="status" class="form-select border-0 shadow-lg text-muted">
                            <option value="" > Select {{ $status['type'] ?? '' }} Status</option>
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
                    <button type="button" class="btn  btn-light text-muted px-4 shadow-sm rounded-pill {{ isset($export) ? '' : 'd-none' }}" 
                            product="{{ $export ?? '' }}" id="reportExport" >
                            <i class="fas fa-download"></i> 
                            Export
                    </button>
                    <div class="margin-auto">
                    <button type="submit" class=" btn px-4 shadow-sm rounded-pill fw-bold" id="searchBtn" style=" background-color:#dfdbdb;">
                        <i class="fas fa-search"></i> Search
                    </button>
                    <button type="button" class="btn  px-4 shadow-sm rounded-pill" id="formReset" style=" background-color:#dfdbdb;">
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
