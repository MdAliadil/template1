<div class="card border-0 shadow-sm mt-4" style="background: rgba(52, 97, 255, 0.1); color: #212529;">
    <div class="card-header text-white" >
        <h4 class="card-title mb-0" style="color:black;"><i class="fas fa-filter"></i> Search Schedule</h4>
    </div>
    <form id="searchForm">
        <div class="card-body bg-white">
            <div class="row g-3">
                <!-- Date Input -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="mydate" class="fw-bold"  style="color:black;;"><i class="far fa-calendar-alt"></i> Date</label>
                        <input type="text" class="form-control border-0 shadow-sm" id="mydate" placeholder="Select Date">
                    </div>
                </div>

                <!-- Search Text -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="searchtext" class="fw-bold"  style="color:black;"><i class="fas fa-search"></i> Search</label>
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
                        <label for="status" class="fw-bold" style="color:black;"><i class="fas fa-list-alt"></i> Status</label>
                        <select name="status" class="form-select border-0 shadow-sm">
                            <option value=""  style="color:black;">Select {{ $status['type'] ?? '' }} Status</option>
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
                <div class="col-12 text-center">
                    <button type="submit" class="btn btn px-4 shadow-sm btn-dark" style=" color:#fff;">
                        <i class="fas fa-search"></i> Search
                    </button>
                    <button type="button" class="btn btn-danger px-4 shadow-sm" id="formReset">
                        <i class="fas fa-sync-alt"></i> Reset
                    </button>
                    <button type="button" class="btn btn-success px-4 shadow-sm {{ isset($export) ? '' : 'd-none' }}" 
                            product="{{ $export ?? '' }}" id="reportExport">
                        <i class="fas fa-download"></i> Export
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
