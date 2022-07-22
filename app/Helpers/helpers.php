<?php

if (!function_exists('firstDateFormat')) {
    function firstDateFormat($value)
    {
        return Carbon\Carbon::parse($value)->format('d M Y - h:m:s');
    }
}
if (!function_exists('secondDateFormat')) {
    function secondDateFormat($value)
    {
        return Carbon\Carbon::parse($value)->format('d/m/Y');
    }
}
if (!function_exists('invoiceNo')) {
    function invoiceNo($value)
    {
        return Carbon\Carbon::parse($value)->format('Y').''.Carbon\Carbon::parse($value)->format('d').''.Carbon\Carbon::parse($value)->format('m');
    }
}
if (!function_exists('carbonnow')) {
    function carbonnow()
    {
        return Carbon\Carbon::now()->format('d/m/Y');
    }
}