@if(hasPermission('district_update') || hasPermission('division_update') || hasPermission('country_update'))
    <div class="setting-check">
        <input type="checkbox" class="status-change" data-id="{{$district->id}}" data-url="{{ route('admin.districts.districts-status') }}"
               {{ ($district->status == \App\Enums\StatusEnum::ACTIVE || $district->status == 'active') ? 'checked' : '' }}
               value="districts-status/{{$district->id}}"
               id="customSwitch2-{{$district->id}}">
        <label for="customSwitch2-{{ $district->id }}"></label>
    </div>
@endif
