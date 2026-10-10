
<a href="{{route('detail.merchant.personal.info', $merchant->id)}}">
    <div class="user-info-panel d-flex gap-12 align-items-center">
        <div class="user-img">
            <img src="{{ getFileLink('80X80', $merchant->user->image_id) }}">
        </div>
        <div class="user-info">
            <div class="d-flex align-items-center gap-2 mb-1">
                <h4 class="mb-0" style="margin-bottom:0;">{{ $merchant->company }}</h4>
                @if($merchant->is_personal)
                    <span class="badge bg-info text-white" style="font-size: 10px; padding: 2px 6px;">Personal</span>
                @else
                    <span class="badge bg-success text-white" style="font-size: 10px; padding: 2px 6px;">Merchant</span>
                @endif
            </div>
            <span>{{$merchant->user->first_name.' '.$merchant->user->last_name}}</span>
            <span>{{ isDemoMode() ? '**************' : $merchant->user->email ?? '' }}</span>
            <span>{{ isDemoMode() ? '**************' : $merchant->phone_number ?? '' }}</span>
        </div>
    </div>
</a>
