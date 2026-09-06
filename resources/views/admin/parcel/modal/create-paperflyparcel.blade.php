<div class="modal fade" tabindex="-1" id="create-paperflyparcel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__('Paperfly Parcel Create')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{route('admin.create-paperfly-parcel')}}" method="POST" class="form-validate is-alter">
                    @csrf
                    <input type="hidden" name="id" value="" id="create-paperfly-parcel-id">
                    <div class="mb-3">
                        <label class="form-label" for="area">{{__('district')}}  <span class="text-danger">*</span></label>
                            <select name="district" class="form-control get-thana-union select2" data-url="{{ route('admin.get-thana-union') }}" required>
                                <option value="">{{ __('select_district') }}</option>
                            </select>
                        @if($errors->has('district'))
                            <div class="invalid-feedback help-block">
                                <p>{{ $errors->first('district') }}</p>
                            </div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="area">{{__('thana_union')}}  <span class="text-danger">*</span></label>
                            <select name="thana_union" class="form-control thana-union select2" required>
                                <option value="">{{ __('select_thana_union') }}</option>
                            </select>
                        @if($errors->has('thana_union'))
                            <div class="invalid-feedback help-block">
                                <p>{{ $errors->first('thana_union') }}</p>
                            </div>
                        @endif
                    </div>
                    <div class="text-right">
                        <button type="submit" class="btn sg-btn-primary resubmit">{{__('submit')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
