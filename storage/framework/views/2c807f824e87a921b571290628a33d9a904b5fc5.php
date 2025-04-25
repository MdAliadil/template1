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
                <?php if(Myhelper::hasNotRole(['retailer', 'apiuser', 'whitelable'])): ?>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="agent" class="fw-bold"><i class="fas fa-user-tie"></i> Agent ID / Parent ID</label>
                        <input type="text" name="agent" class="form-control border-0 shadow-sm" placeholder="Enter Agent ID">
                    </div>
                </div>
                <?php endif; ?>

                <!-- Status Dropdown -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="status" class="fw-bold"><i class="fas fa-list-alt"></i> Status</label>
                        <select name="status" class="form-select border-0 shadow-sm">
                            <option value="">Select <?php echo e($status['type'] ?? ''); ?> Status</option>
                            <?php if(isset($status['data']) && sizeof($status['data']) > 0): ?>
                                <?php $__currentLoopData = $status['data']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>

                <!-- Product Dropdown (Conditional) -->
                <?php if(isset($product)): ?>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="product" class="fw-bold"><i class="fas fa-box"></i> Product</label>
                        <select name="product" class="form-select border-0 shadow-sm">
                            <option value="">Select <?php echo e($product['type'] ?? ''); ?></option>
                            <?php if(isset($product['data']) && sizeof($product['data']) > 0): ?>
                                <?php $__currentLoopData = $product['data']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Buttons -->
            <div class="row mt-4">
                <div class="col-12 text-center">
                    <button type="submit" class="btn btn px-4 shadow-sm" style="background-color:#3447ff8c; color:#fff;">
                        <i class="fas fa-search"></i> Search
                    </button>
                    <button type="button" class="btn btn-secondary px-4 shadow-sm" id="formReset">
                        <i class="fas fa-sync-alt"></i> Reset
                    </button>
                    <button type="button" class="btn btn-success px-4 shadow-sm <?php echo e(isset($export) ? '' : 'd-none'); ?>" 
                            product="<?php echo e($export ?? ''); ?>" id="reportExport">
                        <i class="fas fa-download"></i> Export
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
<?php /**PATH C:\wamp64\www\pg\resources\views/layouts/filter.blade.php ENDPATH**/ ?>