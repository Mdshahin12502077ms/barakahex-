
<a href="{{route('detail.merchant.personal.info', $merchant->id)}}">
    <div class="user-info-panel d-flex gap-12 align-items-center">
        <div class="user-img">
            <img src="{{ getFileLink('80X80', $merchant->user->image_id) }}">
        </div>
        <div class="user-info">
            <h4>{{ $merchant->company }}</h4>
            <span>{{$merchant->user->first_name.' '.$merchant->user->last_name}}</span>
            <span>{{ isDemoMode() ? '**************' : $merchant->user->email ?? '' }}</span>
            <span>{{ isDemoMode() ? '**************' : $merchant->phone_number ?? '' }}</span>
        </div>
    </div>
</a>
