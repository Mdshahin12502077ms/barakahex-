@if(hasPermission('branch_update'))
    <div class="setting-check">
        <input type="checkbox"
            class="custom-control-input {{ hasPermission('branch_update') ? 'default-branch' : ''}} {{ $query->default ? 'disabled' : '' }}"
            {{ $query->default ? 'checked disabled' : '' }} value="{{$query->id}}"
            data-url="{{ route('admin.default.branch') }}" id="customSwitch3-{{$query->id}}">
        <label class="custom-control-label" for="customSwitch3-{{$query->id}}"></label>
    </div>
@endif