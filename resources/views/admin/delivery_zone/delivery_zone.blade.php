<div class="modal fade" id="delivery_zone" tabindex="-1" aria-labelledby="deliveryZoneLabel" aria-hidden="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <h6 class="sub-title create_sub_title">{{ __('add_delivery_zone') }}</h6>
            <h6 class="sub-title edit_sub_title d-none">{{ __('edit_delivery_zone') }}</h6>
            <button type="button" class="btn-close modal-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <form action="{{ route('delivery-zones.store') }}" method="POST" class="form">
                @csrf
                <div class="row gx-20">
                    <div class="col-12">
                        <div class="mb-4">
                            <label for="deliveryZoneName" class="form-label">{{ __('name') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control rounded-2" id="deliveryZoneName"
                                   placeholder="{{ __('enter_name') }}" name="name" value="{{ old('name') }}">
                            <div class="nk-block-des text-danger">
                                <p class="name_error error"></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-4">
                            <label for="thana_id" class="form-label">{{ __('thana') }} <span class="text-danger">*</span></label>
                            <div class="select-type-v2">
                                <select id="thana_id" class="form-select form-select-lg mb-3 with_search" name="thana_id">
                                    <option value="" selected>{{ __('select_thana') }}</option>
                                    @foreach($thanas as $thana)
                                        <option value="{{ $thana->id }}">{{ $thana->name }}</option>
                                    @endforeach
                                </select>
                                <div class="nk-block-des text-danger">
                                    <p class="thana_id_error error"></p>
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
