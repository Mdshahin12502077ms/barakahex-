<a href="{{route('merchant.staff.personal.info', $query->id)}}">
    <div class="user-info-panel d-flex gap-12 align-items-center">
        <div class="user-img">
            <img class="selected-img" src="{{ getFileLink('80X80', $query->image_id) }}"
            alt="favicon">
        </div>
        <div class="user-info">
            <h4>{{$query->first_name.' '.$query->last_name}}</h4>
            <span>{{ isDemoMode() ? '**************' : $query->email ?? ''}}<br>{{ isDemoMode() ? '**************' :  $query->phone_number ?? '' }}
        </div>
    </div>
</a>
