
@if(hasPermission('third_party_update'))
<div class="setting-check">
        <input type="checkbox" data-id="{{$third_party->id}}" data-url="{{ route('admin.third-party.status') }}" class="custom-control-input {{ hasPermission('third_party_update') ? 'status-change' : ''}}" {{ $third_party->status == \App\Enums\StatusEnum::ACTIVE ? 'checked' :'' }} value="third-party/status/{{$third_party->id}}" id="customSwitch2-{{$third_party->id}}">
        <label for="customSwitch2-{{$third_party->id}}"></label>
    </div>
@endif
