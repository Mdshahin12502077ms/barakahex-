@if(hasPermission('division_update') || hasPermission('country_update'))
    <div class="setting-check">
        <input type="checkbox" class="status-change" data-id="{{$division->id}}" data-url="{{ route('admin.divisions.divisions-status') }}"
               {{ ($division->status == \App\Enums\StatusEnum::ACTIVE || $division->status == 'active') ? 'checked' : '' }}
               value="divisions-status/{{$division->id}}"
               id="customSwitch2-{{$division->id}}">
        <label for="customSwitch2-{{ $division->id }}"></label>
    </div>
@endif
