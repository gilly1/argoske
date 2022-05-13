

<?php $__env->startSection('title'); ?>Error - 404 <?php $__env->stopSection(); ?>

<?php $__env->startSection('level'); ?>

    <li class="breadcrumb-item"><a href="#">  Dashboard  </a></li>
    <li class="breadcrumb-item"><a href="#">  Error  </a></li>
    <li class="breadcrumb-item active"> 404 - Page Not Found</li>

<?php $__env->stopSection(); ?>


<?php $__env->startSection('content'); ?>


    <div class="row">

      <?php if(isset($response)): ?>
        <div class="col-md-1"></div>
        <div class="card card-primary col-md-10"> 
          <div class="card-header">
            <h3>Git Response</h3>  
          </div>       
          <div class="card-body">
                <p><?php echo e($response); ?></p>
          </div>
          <div class="card-footer">
            <a class="btn btn-primary" href="<?php echo e(route('git.index',[$subdomain])); ?>">return to Git Console</a>
          </div>
        </div>        
      <?php else: ?>
        <div class="error-page">
          <h2 class="headline text-warning"> 404</h2>

          <div class="error-content">
            <h3><i class="fas fa-exclamation-triangle text-warning"></i> Oops! Page not found.</h3>

            <p>
              We could not find the page you were looking for.
              Meanwhile, you may <a href="<?php echo e(route('home',[$subdomain])); ?>">return to dashboard</a> .
            </p>
          </div>
          <!-- /.error-content -->
        </div>
      <?php endif; ?>

        

    </div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\argostenancy\resources\views/errors/404.blade.php ENDPATH**/ ?>