

<?php $__env->startSection('headSection'); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('title'); ?><?php echo e($title); ?> <?php $__env->stopSection(); ?>


<?php $__env->startSection('level'); ?>

<li class="breadcrumb-item"><a href="<?php echo e(route('home',[$subdomain])); ?>">  Dashboard  </a></li>
<li class="breadcrumb-item"><a href="<?php echo e(route($route.'.index',[$subdomain])); ?>">  <?php echo e($title); ?>  </a></li>
<li class="breadcrumb-item active"> Amotization Schedule   </li>

<?php $__env->stopSection(); ?>


<?php $__env->startSection('content'); ?>


    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-9">
            <div class="card-body">
                <div class="row">
                    <div class="col-12 col-md-12 col-lg-8 order-2 order-md-1">
                    <div class="row">
                        <div class="col-12 col-sm-3">
                        <div class="info-box bg-light">
                            <div class="info-box-content">
                            <span class="info-box-text text-center text-muted">Total Value</span>
                            <span class="info-box-number text-center text-muted mb-0"><?php echo e($currency); ?> <?php echo e(round($total_cash_pay,2)); ?></span>
                            </div>
                        </div>
                        </div>
                        <div class="col-12 col-sm-4">
                        <div class="info-box bg-light">
                            <div class="info-box-content">
                            <span class="info-box-text text-center text-muted">Estimated Intrest</span>
                            <span class="info-box-number text-center text-muted mb-0"><?php echo e($currency); ?> <?php echo e(round($intrest_for_one_month * $duration,2)); ?> <span>
                            </div>
                        </div>
                        </div>
                        <div class="col-12 col-sm-5">
                        <div class="info-box bg-light">
                            <div class="info-box-content">
                            <span class="info-box-text text-center text-muted">Estimated Payment Period</span>
                            <span class="info-box-number text-center text-muted mb-0"><?php echo e(ceil($duration)); ?> Months</span>
                            </div>
                        </div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-1"></div>
        <div class="col-md-1"></div>
        <div class="col-md-9">

            <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Amotization Schedule</h3>
                  <a href="<?php echo e(route($route.'.create',[$subdomain])); ?>" class="btn btn-warning float-right">New Calculations</a>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                  <table id="example2" class="table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Amount</th>
                        <th>Intrest</th>
                        <th>Principle</th>
                        <th>Loading</th>
                        <th>Balance</th>
                    </tr>
                    </thead>
                    <tbody>

                        <?php
                        function createSchedule($amount,$installment,$intrest,$loading,$principle_for_one_month,$i){
                            $i++;
                            if($amount > $installment ){
                                $balance = $amount -  $installment;
                                ?>

                                <tr>
                                    <td><?php echo e($i); ?></td>
                                    <td><?php echo e($installment); ?></td>
                                    <td><?php echo e($intrest); ?></td>
                                    
                                    <td><?php echo e($principle_for_one_month); ?></td>
                                    <td><?php echo e($loading); ?></td>
                                    <td><?php echo e($balance); ?></td>
                                </tr>
                                <?php
                                createSchedule($balance,$installment,$intrest,$loading,$principle_for_one_month,$i);
                            }else {
                                
                                $intrest_ratio = $intrest/$installment;
                                $loading_ratio = $loading/$installment;
                                $principle_ratio =1-($intrest_ratio+$loading_ratio);
                                ?>
                                <tr>
                                    <td><?php echo e($i); ?></td>
                                    <td><?php echo e(round($amount,2)); ?></td>
                                    <td><?php echo e($amount * $intrest_ratio); ?></td>
                                    <td><?php echo e($amount * $principle_ratio); ?></td>
                                    <td><?php echo e($amount * $loading_ratio); ?></td>
                                    <td>0</td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                        <?php echo createSchedule( $total_cash_pay,$installments,$intrest_for_one_month,$loading,$principle_for_one_month,0 ); ?>


                    </tbody>
                    <tfoot>
                    <tr>
                        <th>#</th>
                        <th>Amount</th>
                        <th>Intrest</th>
                        <th>Principle</th>
                        <th>Loading</th>
                        <th>Balance</th>
                    </tr>
                    </tfoot>
                  </table>
                </div>
                <!-- /.card-body -->
              </div>
            <!-- /.box -->
        </div>
        <div class="col-md-6"></div>

    </div>
<?php $__env->stopSection(); ?>


<?php $__env->startSection('footerSection'); ?>


<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\argostenancy\resources\views/argos/amotization_table.blade.php ENDPATH**/ ?>