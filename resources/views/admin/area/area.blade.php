<div class="modal fade" id="area" tabindex="-1" aria-labelledby="areaLabel" aria-hidden="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <h6 class="sub-title create_sub_title">{{ __('add') }} {{ __('area') }}</h6>
            <h6 class="sub-title edit_sub_title d-none">{{ __('edit') }} {{ __('area') }}</h6>
            <button type="button" class="btn-close modal-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <form action="{{ route('areas.store') }}" method="POST" class="form">
                @csrf
                <div class="row gx-20">
                    <div class="col-12">
                        <div class="mb-4">
                            <label for="delivery_zone_id" class="form-label">{{ __('delivery_zone') }} <span class="text-danger">*</span></label>
                            <div class="select-type-v2">
                                <select id="delivery_zone_id" class="form-select form-select-lg mb-3 with_search" name="delivery_zone_id">
                                    <option value="" selected>{{ __('select_delivery_zone') }}</option>
                                    @foreach($deliveryZones as $zone)
                                        <option value="{{ $zone->id }}">
                                            {{ $zone->name }} 
                                            @if($zone->thana) ({{ $zone->thana->name }}) @endif
                                        </option>
                                    @endforeach
                                </select>
                                <div class="nk-block-des text-danger">
                                    <p class="delivery_zone_id_error error"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-4">
                            <label for="areaName" class="form-label">{{ __('name') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control rounded-2" id="areaName"
                                   placeholder="{{ __('enter_name') }}" name="name" value="{{ old('name') }}">
                            <div class="nk-block-des text-danger">
                                <p class="name_error error"></p>
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
