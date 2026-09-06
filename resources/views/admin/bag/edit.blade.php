@php
    $branches = \App\Models\Branch::where('status', 'active')->get();
@endphp
<h6 class="sub-title p-3 modal-title">{{ __('edit_bag') }}</h6>
<button type="button" class="btn-close modal-close" data-bs-dismiss="modal" aria-label="Close"></button>
<form action="{{ route('bag.update', $bag->id) }}" method="POST" class="form">
    @csrf
    <div class="row p-3">
        <div class="col-lg-6 mb-3">
            <label for="edit_from_branch_id" class="form-label">{{ __('from_branch') }} <span class="text-danger">*</span></label>
            <div class="select-type-v2">
                <select name="from_branch_id" id="edit_from_branch_id" class="form-select select2" required>
                    <option value="">{{ __('select_branch') }}</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ $bag->from_branch_id == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                    @endforeach
                </select>
                <div class="nk-block-des text-danger">
                    <p class="from_branch_id_error error"></p>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-3">
            <label for="edit_to_branch_id" class="form-label">{{ __('to_branch') }} <span class="text-danger">*</span></label>
            <div class="select-type-v2">
                <select name="to_branch_id" id="edit_to_branch_id" class="form-select select2" required>
                    <option value="">{{ __('select_branch') }}</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ $bag->to_branch_id == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                    @endforeach
                </select>
                <div class="nk-block-des text-danger">
                    <p class="to_branch_id_error error"></p>
                </div>
            </div>
        </div>
        <div class="col-lg-12 mb-3">
            <label for="edit_max_capacity" class="form-label">{{ __('max_capacity') }} <span class="text-danger">*</span></label>
            <input type="number" name="max_capacity" id="edit_max_capacity" class="form-control" value="{{ $bag->max_capacity }}" min="1" required>
            <div class="nk-block-des text-danger">
                <p class="max_capacity_error error"></p>
            </div>
        </div>
        <div class="col-lg-12 mb-3">
            <label for="edit_note" class="form-label">{{ __('note') }}</label>
            <textarea name="note" id="edit_note" class="form-control" rows="3">{{ $bag->note }}</textarea>
            <div class="nk-block-des text-danger">
                <p class="note_error error"></p>
            </div>
        </div>
    </div>
    <div class="d-flex justify-content-end align-items-center mt-30 p-3 bb">
        <button type="submit" class="btn sg-btn-primary">{{ __('submit') }}</button>
        @include('backend.common.loading-btn', ['class' => 'btn sg-btn-primary'])
    </div>
</form>
