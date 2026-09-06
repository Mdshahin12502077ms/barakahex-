    <div class="setting-check">
        <input type="checkbox" data-id="{{$query->id}}" data-url="{{ route('merchant.staff.user-status') }}" {{ $query->status==\App\Enums\StatusEnum::ACTIVE ? 'checked' :'' }} value="user-status/{{$query->id}}"
               id="customSwitch2-{{$query->id}}" class="custom-control-input status-change">
        <label for="customSwitch2-{{$query->id}}"></label>
    </div>
