@if(hasPermission('area_update') || hasPermission('delivery_zone_update') || hasPermission('upazila_update') || hasPermission('district_update') || hasPermission('division_update') || hasPermission('country_update'))
    <div class="setting-check">
        <input type="checkbox" class="status-change" data-id="{{$area->id}}" data-url="{{ route('admin.areas.areas-status') }}"
               {{ ($area->status == \App\Enums\StatusEnum::ACTIVE || $area->status == 'active') ? 'checked' : '' }}
               value="areas-status/{{$area->id}}"
               id="customSwitch-area-{{$area->id}}">
        <label for="customSwitch-area-{{ $area->id }}"></label>
    </div>
@endif
