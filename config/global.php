<?php


if (! function_exists('the_sub_domain')) {
    /**
     * Assign high numeric IDs to a config item to force appending.
     *
     * @param  array  $array
     * @return array
     */
    function the_sub_domain()
    {
        if(!isset($_SERVER['HTTP_HOST']))
        {
            return ;
        }
        $domain = $_SERVER['HTTP_HOST'];
        $the_sub_domain = '';


        if($domain != env('APP_URL_NAME','tenancy.test')){
            list($the_sub_domain, $rest) = explode('.', $_SERVER['SERVER_NAME'], 2);
        }

        return $the_sub_domain;
    }
}