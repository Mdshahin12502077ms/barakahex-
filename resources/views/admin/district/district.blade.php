<div class="modal fade" id="district" tabindex="-1" aria-labelledby="districtLabel" aria-hidden="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <h6 class="sub-title create_sub_title">{{ __('add_district') }}</h6>
            <h6 class="sub-title edit_sub_title d-none">{{ __('edit_district') }}</h6>
            <button type="button" class="btn-close modal-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <form action="{{ route('districts.store') }}" method="POST" class="form">
                @csrf
                <div class="row gx-20">
                    <div class="col-12">
                        <div class="mb-4">
                            <label for="districtName" class="form-label">{{ __('name') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control rounded-2" id="districtName"
                                   placeholder="{{ __('enter_name') }}" name="name" value="{{ old('name') }}">
                            <div class="nk-block-des text-danger">
                                <p class="name_error error"></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-4">
                            <label for="division_id" class="form-label">{{ __('division') }} <span class="text-danger">*</span></label>
                            <div class="select-type-v2">
                                <select id="division_id" class="form-select form-select-lg mb-3 with_search" name="division_id">
                                    <option value="" selected>{{ __('select_division') }}</option>
                                    @foreach($divisions as $division)
                                        <option value="{{ $division->id }}">{{ $division->name }}</option>
                                    @endforeach
                                </select>
                                <div class="nk-block-des text-danger">
                                    <p class="division_id_error error"></p>
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
