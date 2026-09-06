@if(hasPermission('delivery_zone_update') || hasPermission('upazila_update') || hasPermission('district_update') || hasPermission('division_update') || hasPermission('country_update'))
    <div class="setting-check">
        <input type="checkbox" class="status-change" data-id="{{$deliveryZone->id}}" data-url="{{ route('admin.delivery-zones.delivery-zones-status') }}"
               {{ ($deliveryZone->status == \App\Enums\StatusEnum::ACTIVE || $deliveryZone->status == 'active') ? 'checked' : '' }}
               value="delivery-zones-status/{{$deliveryZone->id}}"
               id="customSwitch2-{{$deliveryZone->id}}">
        <label for="customSwitch2-{{ $deliveryZone->id }}"></label>
    </div>
@endif
