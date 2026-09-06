@if(hasPermission('deliveryman_update'))
    <div class="setting-check">
        <input type="checkbox" data-id="{{$delivery_man->user_id}}" {{ $delivery_man->status == \App\Enums\StatusEnum::ACTIVE ? 'checked' : '' }}  data-url="{{route('admin.delivery-man.update-status')}}" value="delivery-man/update-status/{{$delivery_man->user_id}}"
               id="customSwitch2-{{$delivery_man->id}}" class="status-change">
        <label for="customSwitch2-{{$delivery_man->id}}"></label>
    </div>
@endif


