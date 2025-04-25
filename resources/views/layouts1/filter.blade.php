<div class="card card-outline card-primary mt-3">
    <div class="card-header">
        <h3 class="card-title">Filter Schedule</h3>
    </div>
    <form id="searchForm">
        <div class="card-body">
            <div class="row">
                <!-- Date Input -->
                <div class="col-12 col-md-6 col-lg-3 mb-3">
                    <div class="form-group">
                        <label for="mydate">Date</label>
                        <div class="input-group">
                            <input type="hidden" name="from_date" />
                            <input type="hidden" name="to_date" />
                            <input type="text" class="form-control" id="mydate" placeholder="Select Date">
                            
                        </div>
                    </div>
                </div>

                <!-- Search Text -->
                <div class="col-12 col-md-6 col-lg-3 mb-3">
                    <div class="form-group">
                        <label for="searchtext">Search Value</label>
                        <input type="text" name="searchtext" class="form-control" placeholder="Search Value">
                    </div>
                </div>

                <!-- Agent Input (Conditional) -->
                @if (Myhelper::hasNotRole(['retailer', 'apiuser', 'whitelable']))
                    <div class="col-12 col-md-6 col-lg-3 mb-3">
                        <div class="form-group">
                            <label for="agent">Agent Id / Parent Id</label>
                            <input type="text" name="agent" class="form-control" placeholder="Agent Id / Parent Id">
                        </div>
                    </div>
                @endif

                <!-- Status Dropdown -->
                <div class="col-12 col-md-6 col-lg-3 mb-3">
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select name="status" class="form-control">
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
                    <div class="col-12 col-md-6 col-lg-3 mb-3">
                        <div class="form-group">
                            <label for="product">Product</label>
                            <select name="product" class="form-control">
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

            <!-- Button Row -->
            <div class="row">
                <div class="col-12 text-right">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-search"></i> Search
                    </button>
                    <button type="button" class="btn btn-warning" id="formReset">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                    <button type="button" class="btn btn-primary {{ isset($export) ? '' : 'd-none' }}" 
                            product="{{ $export ?? '' }}" id="reportExport">
                        <i class="fas fa-cloud-download-alt"></i> Export
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>