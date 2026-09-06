<div class="modal fade" tabindex="-1" id="return-assign-tomerchant">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__('return_assign_to_merchant')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{route('return.assign.to.merchant')}}" method="POST" class="form-validate is-alter">
                    @csrf
                    <input type="hidden" name="ids" value="" id="return-merchant-parcel-id">
                    <div class="mb-3">
                        <label class="form-label" for="area">{{__('delivery_man')}} <span
                                class="text-danger">*</span></label>
                        <select name="delivery_man" id="return_assigned_delivery_man"
                            class="form-control delivery-man-live-search"
                            data-url="{{ route('get-delivery-man-live') }}" required>
                            <option value="">{{ __('select_delivery_man') }}</option>
                        </select>
                        @if($errors->has('delivery_man'))
                            <div class="invalid-feedback help-block">
                                <p>{{ $errors->first('delivery_man') }}</p>
                            </div>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="area">{{ __('note') }}</label>
                        <textarea name="note" class="form-control">{{ old('note') }}</textarea>
                    </div>
                    <div class="text-right">
                        <button type="submit" class="btn sg-btn-primary resubmit">{{__('submit')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>