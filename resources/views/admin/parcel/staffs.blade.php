<option value="">{{ __('created_by') }}</option>
@foreach($staffs as $staff)
    <option value="{{ $staff->id }}">{{$staff->first_name.' '.$staff->last_name}}</option>
@endforeach
