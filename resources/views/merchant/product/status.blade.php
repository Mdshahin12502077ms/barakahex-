<div class="setting-check">
    <input type="checkbox" data-id="{{$product->id}}" data-url="{{ route('merchant.products.status') }}" {{ ($product->status == \App\Enums\StatusEnum::ACTIVE) ? 'checked' :'' }} value="products-status/{{$product->id}}"
           id="customSwitch2-{{$product->id}}" class="custom-control-input status-change">
    <label for="customSwitch2-{{$product->id}}"></label>
</div>
