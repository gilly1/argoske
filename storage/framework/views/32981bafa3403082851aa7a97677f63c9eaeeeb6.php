<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    
    <?php echo $__env->make('inc/main/head', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php $__env->startSection('headSection'); ?>
            <?php echo $__env->yieldSection(); ?>

</head>
<body class="hold-transition sidebar-mini">
<!-- Site wrapper -->
<div class="wrapper">


    <?php if(auth()->user() !== null && $_SERVER['HTTP_HOST'] != env('APP_URL_NAME','tenancy.test')): ?>
        <?php echo $__env->make('inc/navigation/nav', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        
        <?php echo $__env->make('inc/navigation/aside', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php else: ?>        
        <?php echo $__env->make('inc/navigation/aside_blank', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>

    <div id="app">
        <div class="content-section">
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">


                
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                
                            <h1>
                                <?php echo $__env->yieldContent("title"); ?>

                                <small> <?php echo $__env->yieldContent("subtitle"); ?> </small>
                            </h1>
                            
                            </div>
                            <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <?php echo $__env->yieldContent("level"); ?> 
                            </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>



                <!-- Main content -->
                <section class="content">                    
                    <?php $__env->startSection('content'); ?>
                        <?php echo $__env->yieldSection(); ?>
            
                </section>
                <!-- /.content -->
                

                
            </div>
            <!-- /.content-wrapper -->
                
        </div>
        
    </div>


    <?php echo $__env->make('inc/navigation/aside2', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    
    <?php echo $__env->make('inc/navigation/footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    

</div>
<!-- ./footer -->
<?php echo $__env->make('inc/main/footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <script>
        <?php if(session()->has('success') || session()->has('error') || session()->has('warning')): ?>
            <?php if(session()->has('success')): ?>
                toastr.success("<?php echo e(Session::get('success')); ?>");
            <?php elseif(session()->has('error')): ?>
                toastr.error("<?php echo e(Session::get('error')); ?>");
            <?php elseif(session()->has('warning')): ?>
                toastr.warning("<?php echo e(Session::get('warning')); ?>");
            <?php elseif(session()->has('message')): ?>
                toastr.info("<?php echo e(Session::get('message')); ?>");
            <?php endif; ?>
        <?php endif; ?>
        
</script>
        <?php $__env->startSection('footerSection'); ?>
            <?php echo $__env->yieldSection(); ?>

</body>
</html>
<?php /**PATH C:\laragon\www\argostenancy\resources\views/layouts/app.blade.php ENDPATH**/ ?>