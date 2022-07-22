const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
    .vue()
    .sass('resources/sass/app.scss', 'public/css')
    .styles([
        'public/plugins/fontawesome-free/css/all.min.css',
        'public/plugins/select2/css/select2.min.css',
        'public/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css',
        'public/plugins/toastr/toastr.min.css',
        'public/plugins/pace-progress/themes/black/pace-theme-flat-top.css',
        'public/plugins/summernote/summernote-bs4.css',
        'public/dist/css/adminlte.min.css'
    ], 'public/css/all.css')
    .styles([
        'public/plugins/datatables-bs4/css/dataTables.bootstrap4.css'
    ], 'public/css/tables.css')
    .styles([
        'public/plugins/datepicker/datepicker.css'
    ], 'public/css/datepicker.css')
    .scripts([
        'public/plugins/jquery/jquery.min.js',
        'public/plugins/bootstrap/js/bootstrap.bundle.min.js',
        'public/plugins/select2/js/select2.full.min.js',
        'public/plugins/datepicker/datepicker.js',
        'public/plugins/toastr/toastr.min.js',
        'public/plugins/pace-progress/pace.min.js',
        'public/plugins/summernote/summernote-bs4.min.js',
        'public/dist/js/adminlte.min.js'
    ], 'public/js/all.js')
    .scripts([
        'public/plugins/datepicker/datepicker.js'
    ], 'public/js/datepicker.js')
    .scripts([
        'public/plugins/datatables/jquery.dataTables.js',
        'public/plugins/datatables-bs4/js/dataTables.bootstrap4.js',
        'public/js/delete.js'
    ], 'public/js/tables.js');

