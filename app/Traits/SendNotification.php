<?php

namespace App\Traits;

use App\Events\PusherNotification;
use App\Models\Notification;
use App\Models\NotificationUser;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Sentinel;

trait SendNotification
{
    public function sendNotification($title = null, $users = [], $details = null, $permissions = [], $message_type = 'success', $url = null, $message = null): bool
    {

        try {

            $jwt                        = jwtUser();
            $notification               = new Notification();
            $notification->title        = $title;
            $notification->description  = $details;
            $notification->url          = $url;
            $notification->created_by   = Sentinel::getUser()->id ?? $jwt->id;
            $notification->save();

            foreach ($users as $user) {

                $userPermission = $user->permissions;
                $status =  false;
                foreach ($permissions as $permission) {

                    if (hasNotification($permission, $userPermission) || $user->user_type == 'merchant' || in_array('notify_pickup_man', $permissions)) {
                        $status= true;
                    }
                }
                if($status){
                    $notification_user                  = new NotificationUser();
                    $notification_user->user_id         = $user->id;
                    $notification_user->notification_id = $notification->id;
                    $notification_user->save();
                    if (setting('is_pusher_notification_active')) {
                        event(new PusherNotification($title, $user, $details, $notification_user->id, $message_type, $url, $message, $notification->created_by));
                    }
                }

            }

        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

}
