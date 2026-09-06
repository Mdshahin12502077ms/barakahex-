<div class="modal fade" id="upazila" tabindex="-1" aria-labelledby="upazilaLabel" aria-hidden="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <h6 class="sub-title create_sub_title">{{ __('add_upazila') }}</h6>
            <h6 class="sub-title edit_sub_title d-none">{{ __('edit_upazila') }}</h6>
            <button type="button" class="btn-close modal-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <form action="{{ route('upazilas.store') }}" method="POST" class="form">
                @csrf
                <div class="row gx-20">
                    <div class="col-12">
                        <div class="mb-4">
                            <label for="upazilaName" class="form-label">{{ __('name') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control rounded-2" id="upazilaName"
                                   placeholder="{{ __('enter_name') }}" name="name" value="{{ old('name') }}">
                            <div class="nk-block-des text-danger">
                                <p class="name_error error"></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-4">
                            <label for="district_id" class="form-label">{{ __('district') }} <span class="text-danger">*</span></label>
                            <div class="select-type-v2">
                                <select id="district_id" class="form-select form-select-lg mb-3 with_search" name="district_id">
                                    <option value="" selected>{{ __('select_district') }}</option>
                                    @foreach($districts as $district)
                                        <option value="{{ $district->id }}">{{ $district->name }}</option>
                                    @endforeach
                                </select>
                                <div class="nk-block-des text-danger">
                                    <p class="district_id_error error"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end align-items-center mt-30">
                    <button type="submit" class="btn sg-btn-primary">{{ __('submit') }}</button>
                    @include('backend.common.loading-btn', ['class' => 'btn sg-btn-primary'])
                </div>
            </form>
        </div>
    </div>
</div>
