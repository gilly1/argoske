
<?php $__currentLoopData = $inputName; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $obj): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    
    <?php if($obj->mainType==='text'): ?>

        <div class="form-group has-feedback col-md-<?php echo e($obj->col); ?>">
            <label for="<?php echo e($obj->id); ?>"><?php echo e(ucwords(str_replace("_", " ", $obj->title))); ?> <?php if($obj->span): ?><span class="text-danger">*</span> <?php endif; ?></label>

            <input type="<?php echo e($obj->type); ?>" class="form-control<?php echo e($errors->has($obj->id) ? ' is-invalid' : ''); ?>" id="<?php echo e($obj->id); ?>" 
                name="<?php echo e($obj->name); ?>" value="<?php if( $forEdit ): ?> <?php echo e($forEdit->{$obj->id}); ?><?php else: ?><?php echo e(old($obj->name)); ?><?php endif; ?>"  
                placeholder="<?php echo e($obj->placeHolder); ?>" <?php if($obj->required): ?> required <?php endif; ?>>

            <?php if($errors->has($obj->name)): ?>
                <span class="invalid-feedback" role="alert">
                    <strong><?php echo e($errors->first($obj->name)); ?></strong>
                </span>
            <?php endif; ?>
        </div>
    <?php elseif($obj->mainType==='password'): ?>

        <div class="form-group has-feedback col-md-<?php echo e($obj->col); ?>">
            <label for="<?php echo e($obj->id); ?>"><?php echo e(ucwords(str_replace("_", " ", $obj->title))); ?> <?php if($obj->span): ?><span class="text-danger">*</span> <?php endif; ?></label>

            <input type="<?php echo e($obj->type); ?>" class="form-control<?php echo e($errors->has($obj->id) ? ' is-invalid' : ''); ?>" id="<?php echo e($obj->id); ?>" 
                name="<?php echo e($obj->name); ?>" value=""  
                placeholder="<?php echo e($obj->placeHolder); ?>" <?php if(!$forEdit): ?> required <?php endif; ?>>

            <?php if($errors->has($obj->name)): ?>
                <span class="invalid-feedback" role="alert">
                    <strong><?php echo e($errors->first($obj->name)); ?></strong>
                </span>
            <?php endif; ?>
        </div>

        
    <?php elseif($obj->mainType==='dateTime'): ?>

        <div class="form-group has-feedback col-md-<?php echo e($obj->col); ?>">
            <label for="<?php echo e($obj->id); ?>"><?php echo e(ucwords(str_replace("_", " ", $obj->title))); ?> <?php if($obj->span): ?><span class="text-danger">*</span><?php endif; ?></label>
            <div class="input-group date">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                      <i class="far fa-calendar-alt"></i>
                    </span>
                </div>
                <input type="text" class="form-control pull-right<?php echo e($errors->has($obj->id) ? ' is-invalid' : ''); ?>"
                    value="<?php if( $forEdit  ): ?><?php echo e(\Carbon\Carbon::parse( $forEdit->{$obj->id} )->format('m/d/Y')); ?><?php else: ?><?php echo e(old($obj->name)); ?><?php endif; ?>"  
                    name="<?php echo e($obj->name); ?>" id="<?php echo e($obj->id); ?>"  placeholder="<?php echo e($obj->placeHolder); ?>" <?php if($obj->required): ?> required <?php endif; ?>>
                
            </div>
            <?php if($errors->has($obj->name)): ?>
                <span class="invalid-feedback" role="alert">
                    <strong><?php echo e($errors->first($obj->name)); ?></strong>
                </span>
            <?php endif; ?>
        </div>

    <?php elseif($obj->mainType==='checkbox'): ?>

        <div class="form-check has-feedback col-md-<?php echo e($obj->col); ?>">
            <div class="custom-control custom-<?php echo e($obj->type); ?> <?php echo e($obj->switch); ?>">
                <input type="<?php echo e($obj->type); ?>" class="custom-control-input <?php echo e($errors->has($obj->name) ? ' is-invalid' : ''); ?>" id="<?php echo e($obj->id); ?>" name="<?php echo e($obj->name); ?>"
                <?php if($forEdit): ?> <?php if($forEdit->{$obj->id}  == 1): ?> checked <?php endif; ?> <?php endif; ?> value="<?php if($forEdit): ?>checked <?php else: ?><?php echo e(old($obj->name)); ?><?php endif; ?>"
                <?php if($obj->required): ?> required <?php endif; ?>>
                
                <label class="custom-control-label" for="<?php echo e($obj->id); ?>"><?php echo e(ucwords(str_replace("_", " ", $obj->title))); ?> <?php if($obj->span): ?><span class="text-danger">*</span> <?php endif; ?></label>    
                <?php if($errors->has($obj->name)): ?>
                    <span class="invalid-feedback" role="alert">
                        <strong><?php echo e($errors->first($obj->name)); ?></strong>
                    </span>
                <?php endif; ?>
            </div>
        </div>

    <?php elseif($obj->mainType==='textarea'): ?>

        <div class="form-group has-feedback col-md-<?php echo e($obj->col); ?>">
            <label for="<?php echo e($obj->id); ?>"><?php echo e(ucwords(str_replace("_", " ", $obj->title))); ?> <?php if($obj->span): ?><span class="text-danger">*</span><?php endif; ?></label>
            <textarea class="textarea<?php echo e($errors->has($obj->id) ? ' is-invalid' : ''); ?>" name="<?php echo e($obj->name); ?>"  id="<?php echo e($obj->id); ?>" placeholder="<?php echo e($obj->placeHolder); ?>"
                style="width: 100%; height: 100px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"
                ><?php if($forEdit): ?><?php echo e($forEdit->{$obj->id}); ?><?php else: ?><?php echo e(old($obj->id)); ?><?php endif; ?>
            </textarea>
                <?php if($errors->has($obj->id)): ?>
                    <span class="invalid-feedback" role="alert">
                        <strong><?php echo e($errors->first($obj->id)); ?></strong>
                    </span>
                <?php endif; ?>
        </div>

        <?php elseif($obj->mainType==='file'): ?>
          
        <div class="form-group has-feedback col-md-<?php echo e($obj->col); ?>">
            <label for="<?php echo e($obj->id); ?>"><?php echo e(ucwords(str_replace("_", " ", $obj->title))); ?><span class="text-danger">[min 150 X 150 size and max 200kb] <?php if($obj->span): ?>*<?php endif; ?></span></label>
            <div class="input-group">
                 <div class="custom-file">
                     <input  type="<?php echo e($obj->type); ?>" class="custom-file-input" accept=".jpeg, .jpg, .png" name="<?php echo e($obj->name); ?>" <?php if($obj->required): ?> required <?php endif; ?>>
                     <label class="custom-file-label" for="<?php echo e($obj->id); ?>"><?php echo e($obj->placeHolder); ?></label>
                 </div>
                <?php if($errors->has($obj->name)): ?>
                    <span class="invalid-feedback" role="alert">
                        <strong><?php echo e($errors->first($obj->name)); ?></strong>
                    </span>
                <?php endif; ?>
            </div>
        </div>


    <?php elseif($obj->mainType==='select'): ?>
    <div class="form-group has-feedback col-md-<?php echo e($obj->col); ?>">
        <label for="<?php echo e($obj->id); ?>"><?php echo e(ucwords(str_replace("_", " ", $obj->title))); ?> <?php if($obj->span): ?><span class="text-danger">*</span> <?php endif; ?></label>
        <select class="form-control<?php echo e($errors->has($obj->name) ? ' is-invalid' : ''); ?> select2" name="<?php echo e($obj->name); ?>">
            <option selected disabled="disabled">Choose...</option>
            <?php $__currentLoopData = $obj->loop; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $coll): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if($forEdit && $obj->model): ?>
                <?php 
                    $name = $obj->model;     
                    foreach(array_keys($name->toArray()) as $keys){
                        $checkIfId = strpos($keys, 'id');
                        if($checkIfId !== false)
                        {
                            continue;
                        }
                        $name_key = $keys;
                        break;
                    }
                 ?>
                
                    <?php if($coll->id == $name->id): ?>
                        <option selected value="<?php echo e($name->id); ?>"><?php echo e($name->$name_key); ?> <?php echo e(isset($name->full_name) ? ' - '. $name->full_name : ''); ?></option>
                    <?php else: ?>
                        <option value="<?php echo e($coll->id); ?>"><?php echo e($coll->$name_key); ?> <?php echo e(isset($coll->full_name) ? ' - '. $coll->full_name : ''); ?></option>
                    <?php endif; ?>
    
                <?php else: ?>
                <?php
                    $array_key_first = array_key_first($coll->toArray());
                    $new_id = strpos($array_key_first, 'id');
                    if($new_id !== false){
                        $id = $array_key_first;
                    }else{
                        $id = 'id';
                    }
                    foreach(array_keys($coll->toArray()) as $keys){
                        $checkIfId = strpos($keys, 'id');
                        if($checkIfId !== false)
                        {
                            continue;
                        }
                        $name = $keys;
                        break;
                    }
                ?>
                    
                    <option value="<?php echo e($coll->$id); ?>"><?php echo e($coll->$name); ?> <?php echo e(isset($coll->full_name) ? ' - '. $coll->full_name : ''); ?></option>
                <?php endif; ?>
                
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    
        <?php if($errors->has($obj->name)): ?>
            <span class="invalid-feedback" role="alert">
                <strong><?php echo e($errors->first($obj->name)); ?></strong>
            </span>
        <?php endif; ?>
    </div>

    <?php endif; ?>

<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php /**PATH C:\laragon\www\argostenancy\resources\views/inc/inputs/typeOne/input.blade.php ENDPATH**/ ?>