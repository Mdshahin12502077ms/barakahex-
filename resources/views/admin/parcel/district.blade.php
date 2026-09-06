<option value="">{{ __('select_district') }}</option>
@foreach($districts as $district)
    <option value="{{ $district->district_name }}">{{ $district->district_name }}</option>
@endforeach
