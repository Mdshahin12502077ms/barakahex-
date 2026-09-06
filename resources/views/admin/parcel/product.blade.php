<option value="">{{ __('select_product') }}</option>
@foreach($warehouses_stock as $product)
    <option value="{{ $product->product->id }}">{{ __( $product->product->name) }}</option>
@endforeach
