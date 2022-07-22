<script src="{{asset('js/app.js')}}"></script>
<script src="{{asset('js/all.js')}}"></script>
<script src="{{asset('js/datepicker.js')}}"></script>

<script src="{{asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js')}}"></script>
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
</script>