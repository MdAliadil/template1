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
                <?php if(Myhelper::hasNotRole(['retailer', 'apiuser', 'whitelable'])): ?>
                    <div class="col-12 col-md-6 col-lg-3 mb-3">
                        <div class="form-group">
                            <label for="agent">Agent Id / Parent Id</label>
                            <input type="text" name="agent" class="form-control" placeholder="Agent Id / Parent Id">
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Status Dropdown -->
                <div class="col-12 col-md-6 col-lg-3 mb-3">
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select name="status" class="form-control">
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
                    <div class="col-12 col-md-6 col-lg-3 mb-3">
                        <div class="form-group">
                            <label for="product">Product</label>
                            <select name="product" class="form-control">
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

            <!-- Button Row -->
            <div class="row">
                <div class="col-12 text-right">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-search"></i> Search
                    </button>
                    <button type="button" class="btn btn-warning" id="formReset">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                    <button type="button" class="btn btn-primary <?php echo e(isset($export) ? '' : 'd-none'); ?>" 
                            product="<?php echo e($export ?? ''); ?>" id="reportExport">
                        <i class="fas fa-cloud-download-alt"></i> Export
                    </button>
                </div>
            </div>
        </div>
    </form>
</div><?php /**PATH C:\wamp64\www\pg\resources\views/layouts1/filter.blade.php ENDPATH**/ ?>