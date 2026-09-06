<option value="">{{ __('select_pickup_man') }}</option>
@foreach ($delivery_men as $delivery_man)
    <option value="{{ $delivery_man->id }}" {{ $parcel->pickup_man_id == $delivery_man->id ? 'selected' : '' }}>
        {{ @$delivery_man->user->first_name . ' ' . @$delivery_man->user->last_name }}</option>
@endforeach
