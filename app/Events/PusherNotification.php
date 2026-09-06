<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use App\Models\User;
use Sentinel;
use App\Models\NotificationUser;
use Illuminate\Queue\SerializesModels;

class PusherNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;

    public $message;

    public $message_type;

    public $url;

    public $created_by;

    public $details;

    public $image;

    public $created_at;

    public $notification;

    public $notification_count;

    public $id;


    public function __construct($title, $user, $details = null, $notification_user, $message_type = 'success', $url = null, $message = null, $created_by)
    {
        $auth                      = Sentinel::getUser() ?? jwtUser();
        $this->notification        = User::where('id', $created_by)->with('image')->first();
        $this->created_by          = $this->notification->first_name. ' ' .$this->notification->last_name;
        $this->image               = $this->notification->image->image_small_two ?? '';
        $this->user                = $user->id;
        $this->message             = $title;
        $this->url                 = $url;
        $this->message_type        = $message_type;
        $this->created_at          = date('H A', strtotime($this->notification->created_at));
        $this->id                  = $notification_user;
        $this->notification_count  = NotificationUser::where('user_id', $auth->id)->where('is_read', 0)->get();
        $this->details             = $details;
    }

    public function broadcastOn()
    {

        return ["notification-send-{$this->user}"];
    }
}
