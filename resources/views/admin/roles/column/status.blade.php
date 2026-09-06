
@if(hasPermission('role_update'))
    <div class="setting-check">
        <input type="checkbox" data-id="{{$query->id}}" data-url="{{ route('admin.role.update-status') }}" class="status-change" data-id="{{ $query->id }}"
               value="role-status/{{$query->id}}"
               {{ $query->status == \App\Enums\StatusEnum::ACTIVE ? 'checked' : '' }}
               id="customSwitch2-{{$query->id}}">
        <label for="customSwitch2-{{ $query->id }}"></label>
    </div>
@endif
