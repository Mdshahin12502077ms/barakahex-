<div class="modal fade" tabindex="-1" id="re-schedule-delivery">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__('re_schedule_delivery')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{route('re-schedule.delivery')}}" method="POST" class="form-validate is-alter" id="re-schedule-delivery-assign-form">
                    @csrf
                    <input type="hidden" name="id" value="" id="re-schedule-delivery-parcel-id">
                    <div class="mb-3">
                        <label class="form-label" for="area">{{__('delivery_man')}}  <span class="text-danger">*</span></label>
                            <select id="re-schedule-delivery-assign-man" name="delivery_man" class="form-control delivery-man-live-search" data-url="{{ route('get-delivery-man-live') }}" required>
                            </select>
                    </div>
                    <div class="d-none third-party mb-3">
                        <label class="form-label" for="third_party">{{__('third_party')}}</label>
                            <select name="third_party" id="re-schedule-third-party" class="without_search form-control">
                                <option value="">{{ __('select_third_party') }}</option>
                                @if (!empty($third_parties))
                                @foreach($third_parties as $third_party)
                                    <option value="{{ $third_party->id }}">{{ $third_party->name.' ('.$third_party->address.')' }}</option>
                                @endforeach
                                @endif
                            </select>
                        @if($errors->has('third_party'))
                            <div class="invalid-feedback help-block">
                                <p>{{ $errors->first('third_party') }}</p>
                            </div>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="outlined-date-picker">{{ __('date') }}</label>
                        <input type="text" class="form-control reschedule-date" id="outlined-date-picker" name="date" placeholder="YYYY-MM-DD">
                    </div>
                    <div class="time">
                            <input type="time" class="form-control form-control-xl form-control-outlined time-picker" id="outlined-time-picker" name="time">
                            <label class="form-label-outlined" for="outlined-time-picker">{{ __('time') }}</label>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="predefined_reason">{{__('predefined_reasons')}}  <span class="text-danger">*</span></label>
                            <select name="predefined_reason" class="without_search form-control" required>
                                <option value="">{{ __('select_reason') }}</option>
                                @foreach(\Config::get('parcel.delivery_re_schedule_reasons') as $reason)
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
                        <textarea name="note" id="re-schedule-delivery-note" class="form-control">{{ old('note') }}</textarea>
                    </div>
                    <div class="text-right">
                        <button type="submit" class="btn sg-btn-primary resubmit">{{__('submit')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
