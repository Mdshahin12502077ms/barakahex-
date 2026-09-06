<option value="">{{ __('select_shop') }}</option>
@foreach($shops as $shop)
    <option value="{{ $shop->id }}" {{ $shop->default ? 'selected':'' }}>{{ __($shop->shop_name) }}</option>
@endforeach
