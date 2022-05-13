<?php for($i = 0; $i < count($names); $i++): ?>

    <?php if(is_array($names[$i])): ?>
        <?php if($names[$i][0]  == 'relationship' ): ?>
        <?php
            $relationship1 = $names[$i][1];
            $relationship2 = $names[$i][2];
        ?>
            <td><?php echo $coll->$relationship1->$relationship2 ?? ''; ?></td>                                            
        <?php elseif($names[$i][0]  == 'hasManyRelationship' ): ?>
        <?php
            $hasManyRelationship1 = $names[$i][1];
            $hasManyRelationship2 = $names[$i][2];
        ?>
        <td><?php echo isset($coll->$hasManyRelationship1) ?$coll->$hasManyRelationship1->first()->$hasManyRelationship2 : ''; ?></td>  
        <?php elseif($names[$i][0]  == 'enum' ): ?>
            <td><?php echo e($coll->{$names[$i][1] } == $names[$i][2] ? $names[$i][3] : $names[$i][4]); ?></td>                                      
        <?php elseif($names[$i][0]  == 'date' ): ?>
            <td><?php echo e(Carbon\Carbon::parse($coll->{$names[$i][1]})->format($names[$i][2])); ?></td>                                      
        <?php elseif($names[$i][0]  == 'view_link' ): ?>
            <?php if(isset($canView)): ?>
                <?php if(auth()->user()->can($canView)): ?>
                    <td> <a href="<?php echo e(route($route.'.show',[$subdomain,$coll->id])); ?>"><?php echo $coll->{$names[$i][1]}; ?></a> </td>                                                                                          
                <?php else: ?>
                    <td> <?php echo $coll->{$names[$i][1]}; ?> </td>   
                <?php endif; ?>
            <?php else: ?>
                <td> <?php echo $coll->{$names[$i][1]}; ?> </td>  
            <?php endif; ?>
        <?php elseif($names[$i][0]  == 'label' ): ?>
        <?php
            $the_status = null;
            if(isset($names[$i][3])){
                foreach ($names[$i][3] as $key => $value) {
                    if($coll->{$names[$i][2]} == $key){
                        $the_status = $value;
                    }
                }
            }
        ?>
            <td><span class="right badge badge-primary"><?php echo $the_status ?? $coll->{$names[$i][2]}; ?></span></td>
            
        <?php elseif($names[$i][0]  == 'dynamicrelationship' ): ?>
        <?php
            $relationship1 = $names[$i][1];
            $relationship2 = $names[$i][2];
            $relationship3 = $names[$i][3];
            $relationship4 = $names[$i][4];
            $relationship5 = $names[$i][5];
            $allModels = $relationship3::all();
            $selectedModel = $allModels->where('id',$coll->$relationship1->$relationship2 ?? '')->first();;
        ?>
            <td><?php echo $selectedModel->model::where('id',$coll->approver_model_id)->first()[$relationship5]; ?></td>  
        <?php else: ?>
        <td></td>
        <?php endif; ?>
    <?php else: ?>   
        <?php if($coll->{$names[$i]} || $coll->{$names[$i]} == 0): ?>
            <td><?php echo $coll->{$names[$i]}; ?></td>  
        <?php else: ?>                                             
            <td><?php echo $names[$i]; ?></td>                                    
        <?php endif; ?> 

        <?php if( is_numeric($coll->{$names[$i]}) ): ?>
            <?php
                isset($columnsSum[$names[$i]]) ? $columnsSum[$names[$i]] += $coll->{$names[$i]} : '';
            ?>
        <?php endif; ?>
    <?php endif; ?>
<?php endfor; ?><?php /**PATH C:\laragon\www\argostenancy\resources\views/main/tableColumns.blade.php ENDPATH**/ ?>