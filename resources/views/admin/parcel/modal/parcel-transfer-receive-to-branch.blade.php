<div class="modal fade" tabindex="-1" id="parcel-transfer-receive-to-branch">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__('transfer_receive_to_branch')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{route('transfer-receive-to-branch')}}" method="POST" class="form-validate is-alter">
                    @csrf
                    <input type="hidden" name="id" value="" id="transfer-receive-to-branch-id">
                    <div class="mb-3">
                        <label class="form-label" for="area">{{ __('note') }} </label>
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
