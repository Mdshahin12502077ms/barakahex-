<div class="modal fade" tabindex="-1" id="re-schedule-pickup">

    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__('re_schedule_pickup')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{route('re-schedule.pickup')}}" method="POST" class="form-validate is-alter" id="re-schedule-pickup-assign-form">
                    @csrf
                    <input type="hidden" name="id" value="" id="re-schedule-pickup-parcel-id">
                    <div class="mb-3">
                        <label class="form-label" for="area">{{__('pickup_man')}}  <span class="text-danger">*</span></label>
                            <select id="re-schedule-pickup-assign-man" name="pickup_man" class="form-control" required>

                            </select>
                    </div>
                    <div class="mb-3">
                            <label class="form-label" for="outlined-date-picker">{{ __('date') }}</label>
                            <input type="text" class="form-control outlined-date-picker reschedule-pickup-date" id="outlined-date-picker" placeholder="YYYY-MM-DD" name="date">
                    </div>
                    <div class="time">
                            <input type="text" class="form-control form-control-outlined time-picker" id="outlined-time-picker" name="time">
                            <label class="form-label-outlined" for="outlined-time-picker">{{ __('time') }}</label>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="predefined_reason">{{__('predefined_reasons')}}  <span class="text-danger">*</span></label>
                            <select name="predefined_reason" class="without_search form-control" required>
                                <option value="">{{ __('select_reason') }}</option>
                                @foreach(\Config::get('parcel.pickup_re_schedule_reasons') as $reason)
                                    <option value="{{ $reason }}">{{ __($reason) }}</option>
                                @endforeach
                            </select>
                        @if($errors->has('predefined_reason'))
                            <div class="invalid-feedback help-block">
                                <p>{{ $errors->first('predefined_reason') }}</p>
                            </div>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="area">{{ __('note') }}</label>
                        <textarea name="note" id="re-schedule-pickup-note" class="form-control">{{ old('note') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <div class="preview-block">
                            <div class="custom-control custom-checkbox">
                                <label class="custom-control-label" for="customCheck2">
                                <input type="checkbox" class="custom-control-input" id="customCheck2" name="notify_pickup_man" value="notify">
                                <span>{{__('notify_pickup_man')}}</span>
                            </label>
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <button type="submit" class="btn sg-btn-primary resubmit">{{__('submit')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
