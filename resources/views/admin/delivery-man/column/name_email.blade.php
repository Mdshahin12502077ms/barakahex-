
<a href="{{route('detail.delivery.man.personal.info', $delivery_man->id)}}">
    <div class="user-info-panel d-flex gap-12 align-items-center">
        <div class="user-img">
            <img src="{{ getFileLink('80X80', $delivery_man->user->image_id) }}">
        </div>
        <div class="user-info">
            <h4>{{$delivery_man->user->first_name.' '.$delivery_man->user->last_name}}</h4>
            <span>{{  isDemoMode() ? '**************' : $delivery_man->user->email ?? ''}}<br>{{ isDemoMode() ? '**************' : $delivery_man->phone_number ?? '' }}</span>
        </div>
    </div>
</a>
