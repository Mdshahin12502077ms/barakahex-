<div class="modal fade assign-delivery" tabindex="-1" id="assign-delivery">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__('assign_delivery_man')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{route('assign.delivery.man')}}" method="POST" class="form-validate is-alter">
                    @csrf
                    <input type="hidden" name="ids" value="" id="assign-delivery-parcel-id">
                    <div class="mb-3">
                        <label class="form-label" for="area">{{__('delivery_man')}} <span
                                class="text-danger">*</span></label>
                        <select name="delivery_man" id="assign_delivery_man"
                            class="form-control delivery-man-live-search"
                            data-url="{{ route('get-delivery-man-live') }}" required>
                            <option value="">{{ __('select_pickup_man') }}</option>
                        </select>
                        @if($errors->has('delivery_man'))
                            <div class="invalid-feedback help-block">
                                <p>{{ $errors->first('delivery_man') }}</p>
                            </div>
                        @endif
                    </div>
                    <div class="d-none third-party mb-3">
                        <label class="form-label" for="third_party">{{__('partner')}}</label>
                        <select name="third_party" class="without_search form-control">
                            <option value="">{{ __('select_partner') }}</option>
                            @if (!empty($third_parties))
                                @foreach($third_parties as $third_party)
                                    <option value="{{ $third_party->id }}">
                                        {{ $third_party->name . ' (' . $third_party->address . ')' }}</option>
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