<div class="setting-check">
    <input type="checkbox" data-id="{{$notice->id}}" data-url="{{ route('admin.notice.status') }}" class="custom-control-input {{ hasPermission('notice_update') ? 'status-change-for' : '' }}" {{ $notice->merchant ? 'checked' :'' }} data-change-for="merchant" value="notice-status/{{$notice->id}}" id="customSwitch-merchant-{{$notice->id}}">
    <label for="customSwitch-merchant-{{$notice->id}}"></label>
</div>


