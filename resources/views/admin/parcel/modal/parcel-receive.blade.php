<div class="modal fade" tabindex="-1" id="parcel-receive">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__('parcel_received_by_warehouse')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{route('parcel-receive')}}" method="POST" class="form-validate is-alter">
                    @csrf
                    <input type="hidden" name="ids" value="" id="receive-parcel-id">
                    <div class="mb-3">
                        <label class="form-label" for="area">{{__('branch')}} <span class="text-danger">*</span></label>
                        <select name="branch" class="without_search form-control" required>
                            <option value="">{{ __('select_branch') }}</option>
                            @if (!empty($branchs))
                                @foreach($branchs as $branch)
                                    <option value="{{ $branch->id }}" {{ $branch->id == Sentinel::getUser()->branch_id ? 'selected' : '' }}>{{ @$branch->name . ' (' . @$branch->address }})</option>
                                @endforeach
                            @endif
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