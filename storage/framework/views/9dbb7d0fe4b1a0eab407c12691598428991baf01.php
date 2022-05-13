

<?php $__env->startSection('headSection'); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('title'); ?><?php echo e($title); ?> <?php $__env->stopSection(); ?>


<?php $__env->startSection('level'); ?>

<li class="breadcrumb-item"><a href="<?php echo e(route('home',[$subdomain])); ?>">  Dashboard  </a></li>
<li class="breadcrumb-item"><a href="<?php echo e(route($route.'.index',[$subdomain])); ?>">  <?php echo e($title); ?>  </a></li>
<li class="breadcrumb-item active"> <?php if($data): ?> Update <?php echo e($title); ?> <?php else: ?> Add <?php echo e($title); ?> <?php endif; ?>  </li>

<?php $__env->stopSection(); ?>


<?php $__env->startSection('content'); ?>


    <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-9">
            <!-- general form elements -->
            <div class="card card-primary">
                <div class="card-header with-border">
                    <h3 class="card-title"><?php if($data): ?> Update <?php echo e($title); ?> <?php else: ?> Add <?php echo e($title); ?> <?php endif; ?>  </h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form method="POST" action="<?php if($data): ?><?php echo e(route($route.'.update',[$subdomain,$data->id])); ?><?php else: ?> <?php echo e(route($route.'.store',[$subdomain])); ?> <?php endif; ?> " enctype="multipart/form-data" >
                        <?php echo csrf_field(); ?>
                        <?php if($data): ?> <?php echo e(method_field('PATCH')); ?> <?php endif; ?> 

                        <div class="card-body row">

                            <?php
                                $forEdit = $data;
                            ?>

                            <?php echo $__env->make('inc/inputs/typeOne/input', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            
                            <?php if(isset($vue)): ?>
                                <?php echo $vue; ?>

                            <?php endif; ?>
                            

                        </div>
                        
                    <!-- /.box-body -->

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary"><?php if($data): ?> Update <?php else: ?> Add <?php endif; ?>  </button>&nbsp;&nbsp;&nbsp;
                        <a href="<?php echo e(route($route.'.index',[$subdomain])); ?>" class="btn btn-warning">Back</a> 
                    </div>
                </form>
            </div>
            <!-- /.box -->
        </div>
        <div class="col-md-6"></div>

    </div>
<?php $__env->stopSection(); ?>


<?php $__env->startSection('footerSection'); ?>


<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\argostenancy\resources\views/main/view.blade.php ENDPATH**/ ?>