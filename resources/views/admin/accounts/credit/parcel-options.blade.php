<option value="">{{ __('select_parcel') }}</option>
@foreach($parcels as $parcel)
    <option value="{{ $parcel->id }}">{{ $parcel->parcel_no.' ' . __('of').' '. $parcel->merchant->company  }}</option>
@endforeach
