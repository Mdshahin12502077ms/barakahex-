<div class="setting-check">
    <input type="checkbox" data-id="{{$notice->id}}" data-url="{{ route('admin.notice.status') }}" class="custom-control-input {{ hasPermission('notice_update') ? 'status-change-for' : '' }}" {{ $notice->staff ? 'checked' :'' }}  data-change-for="staff" value="notice-status/{{$notice->id}}" id="customSwitch-staff-{{$notice->id}}">
    <label for="customSwitch-staff-{{$notice->id}}"></label>
</div>
