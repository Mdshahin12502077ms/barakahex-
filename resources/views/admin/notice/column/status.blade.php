<div class="setting-check">
    <input type="checkbox" data-id="{{$notice->id}}" data-url="{{ route('admin.notice.status') }}" class="custom-control-input {{ hasPermission('notice_update') ? 'status-change-for' : '' }}" {{ $notice->status==\App\Enums\StatusEnum::ACTIVE ? 'checked' :'' }} data-change-for="status" value="notice-status/{{$notice->id}}" id="customSwitch-{{$notice->id}}">
    <label for="customSwitch-{{$notice->id}}"></label>
</div>


