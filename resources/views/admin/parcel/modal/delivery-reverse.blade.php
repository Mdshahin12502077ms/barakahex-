<div class="modal fade" tabindex="-1" id="delivery-reverse">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__('backward')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{route('delivery-reverse')}}" method="POST" class="form-validate is-alter">
                    @csrf
                    <input type="hidden" name="id" value="" id="delivery-reverse-id">
                    <div class="mb-3">
                        <label class="form-label" for="area">{{__('status')}}  <span class="text-danger">*</span></label>
                            <select name="status" class="without_search form-control" id="reverse" required>
                            </select>
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
