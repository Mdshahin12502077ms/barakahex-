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
                    <div class="mb-3">
                        <label class="form-label" for="cod" >{{__('cod')}}  <span class="text-danger">*</span></label>
                            <input type="text" class="form-control cod" id="cod" value="{{ old('cod') }}" name="cod" placeholder="{{__('cod')}}" required>
                        @if($errors->has('cod'))
                            <div class="invalid-feedback help-block">
                                <p>{{ $errors->first('cod') }}</p>
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
