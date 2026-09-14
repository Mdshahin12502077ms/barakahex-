<div class="modal fade" tabindex="-1" id="parcel-delivered-partially">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__('parcel').' '.__('partially_delivery')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{route('partially-delivered')}}" method="POST" class="form-validate is-alter" id="partial-delivery-form">
                    @csrf
                    <input type="hidden" name="id" value="" id="delivery-parcel-partially-id">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="partial_delivered_quantity">{{__('delivered_quantity')}} <span class="text-danger">*</span></label>
                            <input type="number" min="1" step="any" class="form-control" id="partial_delivered_quantity" name="delivered_quantity" value="{{ old('delivered_quantity', 1) }}" placeholder="{{__('delivered_quantity')}}" required>
                            @if($errors->has('delivered_quantity'))
                                <div class="invalid-feedback help-block">
                                    <p>{{ $errors->first('delivered_quantity') }}</p>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="partial_return_quantity">{{__('return_quantity')}}</label>
                            <input type="number" min="0" step="any" class="form-control" id="partial_return_quantity" name="return_quantity" value="{{ old('return_quantity', 0) }}" placeholder="{{__('return_quantity')}}">
                            @if($errors->has('return_quantity'))
                                <div class="invalid-feedback help-block">
                                    <p>{{ $errors->first('return_quantity') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="cod">{{__('collected_cod_amount')}} / {{__('cod')}} <span class="text-danger">*</span></label>
                            <input type="number" step="any" class="form-control cod" id="cod" value="{{ old('cod') }}" name="cod" placeholder="{{__('cod')}}" required>
                            @if($errors->has('cod'))
                                <div class="invalid-feedback help-block">
                                    <p>{{ $errors->first('cod') }}</p>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="partial_payment_method">{{__('payment_method')}}</label>
                            <select name="payment_method" id="partial_payment_method" class="form-control">
                                <option value="cash">{{__('cash')}}</option>
                                <option value="bkash">bKash</option>
                                <option value="nagad">Nagad</option>
                                <option value="card">{{__('card')}}</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="area">{{ __('note') }}</label>
                        <textarea name="note" class="form-control" placeholder="{{ __('note') }}">{{ old('note') }}</textarea>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn sg-btn-primary resubmit">{{__('submit')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
