<?php

namespace App\Http\Controllers\notification;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\model\notification\notification;

class NotificationController extends Controller
{
    
    public function unread($subdomain,Request $request)
    {
        $type = 'unread';

        
        $user = auth()->user();

        //check if want to make read
        if($request->query->get('action', '') == "mark_as_read"){
            $user->unreadNotifications->markAsRead();

            if($request->ajax()) {
                return response()->json([]);
            }
            return redirect()->back();
        }
        //check if want to delete
        if($request->query->get('action', '') == "delete"){
            $user->unreadNotifications()->delete();

            if($request->ajax()) {
                return response()->json([]);
            }
            return redirect()->back();
        }


         $limit = $request->query->get('limit', 0);
         if($limit){
             $notifications = $user->unreadNotifications->take($limit);

         }
         else {
             $notifications = $user->unreadNotifications;

         }




         $messages = [];
         foreach ($notifications as $notification){
             $messages[] = [
                  "route" => $notification->data['route'],
                  "type" => $notification->data['type'],
                  "message" => $notification->data['message'],
                  "created_at" => $notification->created_at->format('M j,y h:i:s a')
                 ];
         }

        // check for ajax request here
        if($request->ajax()) {
            return response()->json($messages);
        }
        return view('notification/notification')->with('messages',$messages)->with('type',$type)->with('subdomain',$subdomain);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function read($subdomain,Request $request)
    {
        $type = 'read';
        
        
        $user = auth()->user();

        //check if want to delete
        if($request->query->get('action', '') == "delete"){
            $user->readNotifications()->delete();

            if($request->ajax()) {
                return response()->json([]);
            }
            return redirect()->back();
        }



        $limit = $request->query->get('limit', 0);
        if($limit){
            $notifications = $user->readNotifications->take($limit);

        }
        else {
            $notifications = $user->readNotifications;

        }

        $messages = [];
        foreach ($notifications as $notification){
            $messages[] = [
                "route" => $notification->data['route'],
                "type" => $notification->data['type'],
                "message" => $notification->data['message'],
                "created_at" => $notification->created_at->format('M j,y h:i:s a')
            ];
        }

        // check for ajax request here
        if($request->ajax()) {
            return response()->json($messages);
        }


        return view('notification/notification')->with('messages',$messages)->with('type',$type)->with('subdomain',$subdomain);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function all($subdomain,Request $request)
    {
        $type = 'all';

        $user = auth()->user();

        //check if want to delete
        if($request->query->get('action', '') == "delete"){
            $user->notifications()->delete();

            if($request->ajax()) {
                return response()->json([]);
            }
            return redirect()->back();
        }


        $notifications = $user->notifications;


        $messages = [];
        foreach ($notifications as $notification){
            $messages[] = [
                "route" => $notification->data['route'],
                "type" => $notification->data['type'],
                "message" => $notification->data['message'],
                "created_at" => $notification->created_at->format('M j,y h:i:s a')
            ];
        }

        // check for ajax request here
        if($request->ajax()) {
            return response()->json($messages);
        }
        return view('notification/notification')->with('messages',$messages)->with('type',$type)->with('subdomain',$subdomain);
    }
}
