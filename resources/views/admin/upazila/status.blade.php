@if(hasPermission('upazila_update') || hasPermission('district_update') || hasPermission('division_update') || hasPermission('country_update'))
    <div class="setting-check">
        <input type="checkbox" class="status-change" data-id="{{$upazila->id}}" data-url="{{ route('admin.upazilas.upazilas-status') }}"
               {{ ($upazila->status == \App\Enums\StatusEnum::ACTIVE || $upazila->status == 'active') ? 'checked' : '' }}
               value="upazilas-status/{{$upazila->id}}"
               id="customSwitch2-{{$upazila->id}}">
        <label for="customSwitch2-{{ $upazila->id }}"></label>
    </div>
@endif
