<script src="<?php echo e(asset('js/app.js')); ?>"></script>
<script src="<?php echo e(asset('js/all.js')); ?>"></script>
<script src="<?php echo e(asset('js/datepicker.js')); ?>"></script>

<script src="<?php echo e(asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js')); ?>"></script>
<script type="text/javascript">
    $(document).ready(function () {
        bsCustomFileInput.init();
        
        //Initialize Select2 Elements
        $('.select2').select2();

        $('.textarea').summernote()

        //Date range picker
        $('#date0').datepicker({
            locale: 'en'
        })
        $('#date1').datepicker({
            locale: 'en'
        })
        $('#date2').datepicker({
            locale: 'en'
        })

      
    });
</script><?php /**PATH C:\laragon\www\argostenancy\resources\views/inc/main/footer.blade.php ENDPATH**/ ?>