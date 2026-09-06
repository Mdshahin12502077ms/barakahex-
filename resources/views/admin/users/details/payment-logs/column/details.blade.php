<span>{{ __($statement->details) }}</span><br>
@if ($statement->parcel != '')
    {{ __('id') }}:<span>#{{ __(@$statement->parcel->parcel_no) }}</span>
@endif
