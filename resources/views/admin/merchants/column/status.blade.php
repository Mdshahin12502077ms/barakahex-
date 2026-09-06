
    @if(hasPermission('merchant_update'))
        <div class="setting-check">
            <input type="checkbox" data-id="{{$merchant->id}}" data-url="{{ route('admin.merchant.status') }}" {{ $merchant->status == \App\Enums\StatusEnum::ACTIVE ? 'checked' : '' }} value="merchant/update-status/{{$merchant->id}}"
                   id="customSwitch2-{{$merchant->id}}" class="status-change">
            <label for="customSwitch2-{{$merchant->id}}"></label>
        </div>
    @endif


