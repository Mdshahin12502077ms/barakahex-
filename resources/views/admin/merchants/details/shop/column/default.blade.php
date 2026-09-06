<div class="setting-check">
    <input type="checkbox" class="custom-control-input {{ hasPermission('merchant_shop_update') ? 'default-change': ''}}" {{ $shop->default ? 'checked' :'' }} value="{{$shop->id}}"  data-url="{{ route('admin.merchant.default.shop') }}" id="customSwitch2-{{$shop->id}}" class="status-change">
    <label class="custom-control-label" for="customSwitch2-{{$shop->id}}"></label>
</div>
