<div class="modal fade" tabindex="-1" id="parcel-cancel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__('cancel_parcel')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{route('parcel-cancel')}}" method="POST" class="form-validate is-alter">
                    @csrf
                    <input type="hidden" name="id" value="" id="cancel-parcel-id">
                    <div class="mb-3">
                        <label class="form-label" for="predefined_reason">{{__('predefined_reasons')}}  <span class="text-danger">*</span></label>
                            <select name="predefined_reason" class="without_search form-control" required>
                                <option value="">{{ __('select_reason') }}</option>
                                @foreach(\Config::get('parcel.cancel_predefined_reasons') as $reason)
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
                        <label class="form-label" for="area">{{ __('cancel_note') }}</label>
                        <textarea name="cancel_note" class="form-control">{{ old('cancel_note') }}</textarea>
                    </div>
                    <div class="text-right">
                        <button type="submit" class="btn sg-btn-primary resubmit">{{__('submit')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
