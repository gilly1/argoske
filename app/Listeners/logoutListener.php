<?php

namespace App\Listeners;

use App\Helpers\AppHelper;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class logoutListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $message = auth()->user()->name." logged Out"; 
        AppHelper::logs('critical',$message);
    }
}
