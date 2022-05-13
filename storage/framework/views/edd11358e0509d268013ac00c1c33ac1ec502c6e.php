       <div class="card card-primary ">
                <div class="card-header" >
                  <h3 class="card-title"><?php echo e($table_name); ?></h3>
                  <?php if(isset($canCreate)): ?>
                       <?php if(auth()->user()->can($canCreate)): ?>
                            <a href="<?php echo e(route($route.'.create',[$subdomain])); ?>" class="btn btn-warning float-right">Add <?php echo e($table_name); ?></a>
                        <?php endif; ?>
                  <?php endif; ?>
                  
                  <div  class="form-group float-right mr-1">
                        <?php if(isset($canCreate) && isset($canImport)): ?>                     
                            <?php if(auth()->user()->can($canImport)): ?>  
                                <form action="<?php echo e(route($route.'.import',[$subdomain])); ?>" class="form-inline" method="post" enctype="multipart/form-data">
                                    <?php echo csrf_field(); ?>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="exampleInputFile" name="file">
                                            <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                                        </div>
                                        <div class="input-group-append">
                                            <input class="input-group-text" type="submit" value="Upload">
                                        </div>
                                    </div>
                                </form>
                                <span><a class="btn btn-xs btn-info float-left mr-1 mt-1" href="<?php echo e(route($route.'.sample',[$subdomain])); ?>">Download Sample Excel</a></span>  
                            <?php endif; ?>
                        <?php endif; ?>
                            <?php if(isset($canExport)): ?>
                                <?php if(auth()->user()->can($canExport)): ?>
                                    <span><a class="btn btn-xs btn-success float-left mt-1" href="<?php echo e(route($route.'.export',[$subdomain,'xlsx'])); ?>">Export Excel</a></span>
                                    <span><a class="btn btn-xs btn-success float-left mt-1 ml-1" href="<?php echo e(route($route.'.export',[$subdomain,'pdf'])); ?>">Export PDF</a></span>
                                    <span><a class="btn btn-xs btn-success float-left mt-1 ml-1" href="<?php echo e(route('reports.index',[$subdomain,'name'=>$route])); ?>">Custom Report</a></span>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                   
                </div>
            
                  <!-- /.box-header -->
                  <div class="card-body">
                    <table id="listDataTable" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <?php $names=[] ?>

                                <?php $__currentLoopData = $columns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $column): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <th><?php echo e($key); ?></th>

                                    <?php array_push($names, $column)?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <th>Action</th>
                            </tr>
                        </thead>
                      <tbody>

                        <?php $__currentLoopData = $collection; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $coll): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($loop->iteration); ?></td>
                                <?php echo $__env->make('main/tableColumns', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                
                             <td>
                                 <?php if(isset($canEdit)): ?>
                                    <?php if(auth()->user()->can($canEdit)): ?>
                                        <a href="<?php echo e(route($route.'.edit',[$subdomain,$coll->id])); ?>">
                                            <button class="btn btn-primary mr-1 btn-xs item" data-toggle="tooltip" data-placement="top" title="Edit">
                                                <i class="fa fa-edit fa-fw"></i>
                                            </button>
                                        </a>                                     
                                    <?php endif; ?>                                     
                                 <?php endif; ?>


                                 <?php if(isset($canView)): ?>
                                    <?php if(auth()->user()->can($canView)): ?>
                                        <a href="<?php echo e(route($route.'.show',[$subdomain,$coll->id])); ?>">
                                            <button class="btn btn-primary mr-1 btn-xs item" data-toggle="tooltip" data-placement="top" title="view">
                                                <i class="fa fa-eye fa-fw"></i>
                                            </button>
                                        </a>                                    
                                    <?php endif; ?>                                     
                                 <?php endif; ?>

                                
                                 <?php if(isset($canDelete)): ?>
                                    <?php if(auth()->user()->can($canDelete)): ?>
                                        <div class="btn-group">
                                            <form  class="myAction" method="POST" action="<?php echo e(URL::route($route.'.destroy', [$subdomain,$coll->id])); ?>">
                                                <?php echo csrf_field(); ?>
                                                <input name="_method" type="hidden" value="DELETE">
                                                <button type="submit" class="btn btn-danger btn-xs" data-toggle="tooltip" data-placement="top" title="Delete">
                                                    <i class="fa fa-fw fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>                                     
                                    <?php endif; ?>                                     
                                 <?php endif; ?>
                                
                             </td>

                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


                        <?php if(isset($columnsSum)): ?>

                            <tr>
                                
                                <td>Total :</td> 
                                <?php for($i = 0; $i < count($names); $i++): ?>
                                    
                                    <?php if( !is_array( $names[$i] ) ): ?>
                                        <?php if( isset( $columnsSum[$names[$i]] ) ): ?>
                                            <td><?php echo $columnsSum[$names[$i]]; ?></td>  
                                        <?php else: ?>                              
                                            <td></td>
                                        <?php endif; ?>
                                    <?php else: ?>                              
                                        <td></td>
                                    <?php endif; ?>
                                    
                                <?php endfor; ?>
                                <td></td>
                            </tr>
                            
                        <?php endif; ?>
            
            
                      </tbody>
                    </table>
                  </div>
                  <!-- /.box-body -->
                </div>
            
                
<?php /**PATH C:\laragon\www\argostenancy\resources\views/main/table.blade.php ENDPATH**/ ?>