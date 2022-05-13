


<?php $__env->startSection('headSection'); ?>

    <?php echo $__env->make('main/table/listHeader', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php $__env->stopSection(); ?>


<?php $__env->startSection('title'); ?><?php echo e($title); ?> <?php $__env->stopSection(); ?>


<?php $__env->startSection('level'); ?>

<li class="breadcrumb-item"><a href="<?php echo e(route('home',[$subdomain])); ?>">  Dashboard  </a></li>
<li class="breadcrumb-item"><a href="<?php echo e(route($route.'.index',[$subdomain])); ?>">  <?php echo e($title); ?>  </a></li>
<li class="breadcrumb-item active"> All </li>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    
    <?php if(isset($subColumns)): ?>
        <?php echo $__env->make('main/expandableTable', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php else: ?>
        <?php echo $__env->make('main/table', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('footerSection'); ?>

    <?php echo $__env->make('main/table/listFooter', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\argostenancy\resources\views/main/index.blade.php ENDPATH**/ ?>