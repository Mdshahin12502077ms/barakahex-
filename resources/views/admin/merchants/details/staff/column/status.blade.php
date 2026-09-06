
<div class="setting-check">
    <input type="checkbox" data-id="{{ $query->id }}" data-url="{{ route('admin.merchant-staff.update-status') }}" class=" {{ hasPermission('merchant_staff_update') ? 'status-change': ''}}" {{ $query->status==\App\Enums\StatusEnum::ACTIVE ? 'checked' :'' }} value="user-status/{{$query->id}}" id="customSwitch2-{{$query->id}}" class="status-change">
    <label for="customSwitch2-{{$query->id}}"></label>
</div>
