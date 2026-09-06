<div>
    {{$delivery_man->user->last_login != ""? date('M y, Y h:i a', strtotime($delivery_man->user->last_login)):''}}
</div>
