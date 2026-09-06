<option value="">{{ __('select_warehouse') }}</option>
@foreach($warehouses as $warehouse)
    <option value="{{ $warehouse->id }}">{{ __( $warehouse->name) }}</option>
@endforeach
