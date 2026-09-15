@extends('backend.layouts.master')

@section('title', __('delivery_time_set'))

@section('mainContent')
<div class="container-fluid">
    <div class="row gx-20">
        <div class="col-lg-12">
            <div class="header-top d-flex justify-content-between align-items-center mb-12">
                <h3 class="section-title">{{ __('delivery_time_set') }}</h3>
            </div>

            <div class="card bg-white redious-border p-20 p-sm-30">
                <form action="{{ route('admin.delivery-time-set.store') }}" method="POST" class="shipping-time-form">
                    @csrf
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label for="working_hours_start" class="form-label">{{ __('working_hours_start') }}</label>
                            <input type="time" class="form-control" name="working_hours_start" id="working_hours_start" value="{{ @$setting->working_hours_start ? \Carbon\Carbon::parse(@$setting->working_hours_start)->format('H:i') : '' }}">
                        </div>

                        <div class="col-md-6">
                            <label for="working_hours_end" class="form-label">{{ __('working_hours_end') }}</label>
                            <input type="time" class="form-control" name="working_hours_end" id="working_hours_end" value="{{ @$setting->working_hours_end ? \Carbon\Carbon::parse(@$setting->working_hours_end)->format('H:i') : '' }}">
                        </div>

                        @php
                            $selectedDays = @$setting->offday ? explode(',', $setting->offday) : [];
                        @endphp

                        <div class="col-md-12">
                            <label for="offday" class="form-label">{{ __('offday') }}</label>
                            <select class="form-control select2" name="offday[]" id="offday" multiple="multiple">
                                <option value="Friday" {{ in_array('Friday', $selectedDays) ? 'selected' : '' }}>Friday</option>
                                <option value="Saturday" {{ in_array('Saturday', $selectedDays) ? 'selected' : '' }}>Saturday</option>
                                <option value="Sunday" {{ in_array('Sunday', $selectedDays) ? 'selected' : '' }}>Sunday</option>
                                <option value="Monday" {{ in_array('Monday', $selectedDays) ? 'selected' : '' }}>Monday</option>
                                <option value="Tuesday" {{ in_array('Tuesday', $selectedDays) ? 'selected' : '' }}>Tuesday</option>
                                <option value="Wednesday" {{ in_array('Wednesday', $selectedDays) ? 'selected' : '' }}>Wednesday</option>
                                <option value="Thursday" {{ in_array('Thursday', $selectedDays) ? 'selected' : '' }}>Thursday</option>
                            </select>
                            <small class="text-muted">You can select multiple days.</small>
                        </div>

                        <div class="col-md-12 mt-4 text-end">
                            <a href="javascript:void(0)" class="btn sg-btn-primary px-5 store">{{ __('save') }}</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    $(document).ready(function() {
        $('.store').click(function(e) {
            e.preventDefault();
            $('.shipping-time-form').submit();
        });
    });
</script>
@endpush