{{-- Update Profile modal --}}

<div class="modal fade" tabindex="-1" id="update-profile">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('update_profile') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form
                    action="{{ Sentinel::getUser()->user_type == 'merchant' ? route('merchant.update.profile') : route('merchant.staff.update.profile') }}"
                    class="form-validate is-alter" method="POST" enctype="multipart/form-data"
                    id="update-profile-form">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label" for="first_name">{{ __('first_name') }}</label>
                        <input type="text" hidden name="id" id="id"
                            value="{{ \Sentinel::getUser()->id }}">
                        @if (Sentinel::getUser()->user_type == 'merchant_staff')
                            <input type="text" hidden name="merchant" id="merchant"
                                value="{{ \Sentinel::getUser()->merchant_id }}">
                        @endif
                        <input type="text" name="first_name" class="form-control" id="first_name"
                            value="{{ \Sentinel::getUser()->first_name }}" placeholder="{{ __('first_name') }}"
                            required>
                    </div>
                    @if ($errors->has('first_name'))
                        <div class="invalid-feedback help-block">
                            <p>{{ $errors->first('first_name') }}</p>
                        </div>
                    @endif
                    <div class="mb-3">
                        <label class="form-label" for="last_name">{{ __('last_name') }}</label>
                        <input type="text" name="last_name" class="form-control"
                            value="{{ \Sentinel::getUser()->last_name }}" id="last_name"
                            placeholder="{{ __('last_name') }}">
                    </div>
                    @if ($errors->has('last_name'))
                        <div class="invalid-feedback help-block">
                            <p>{{ $errors->first('last_name') }}</p>
                        </div>
                    @endif
                    <div class="mb-3">
                        <label class="form-label" for="email">{{ __('email') }}</label>
                        <input type="email" name="email" class="form-control"
                            value="{{ \Sentinel::getUser()->email }}" id="email" placeholder="{{ __('email') }}"
                            required>
                    </div>
                    @if ($errors->has('email'))
                        <div class="invalid-feedback help-block">
                            <p>{{ $errors->first('email') }}</p>
                        </div>
                    @endif
                    @if (Sentinel::getUser()->user_type == 'merchant_staff')
                        <div class="mb-3">
                            <label class="form-label" for="phone_number">{{ __('phone_number') }} <span class="text-danger">*</span></label>
                            <input type="text" name="phone_number" class="form-control"
                                value="{{ \Sentinel::getUser()->phone_number }}" id="phone_number"
                                placeholder="{{ __('phone_number') }}" required>
                        </div>
                        @if ($errors->has('phone_number'))
                            <div class="invalid-feedback help-block">
                                <p>{{ $errors->first('phone_number') }}</p>
                            </div>
                        @endif
                    @endif
                    <div class="mb-3 input_file_div">
                        <div class="mb-3 mt-2">
                            <label class="form-label mb-1">{{ __('profile') }}</label>
                            <input class="form-control sp_file_input file_picker" type="file" id="profilePhoto"
                                name="image_id" accept="image/*">
                                @if ($errors->has('image'))
                                <div class="invalid-feedback help-block">
                                    <p>{{ $errors->first('image') }}</p>
                                </div>
                            @endif
                        </div>
                        <div class="selected-files d-flex flex-wrap gap-20">
                            <div class="selected-files-item">
                                <img class="selected-img"
                                 src="{{ getFileLink('80X80', \Sentinel::getUser()->image_id) }}"
                                 alt="favicon">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="mb-3 text-right mt-3">
                            <button type="submit" class="btn sg-btn-primary">{{ __('update') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Update Merchant Details modal --}}
<div class="modal fade" tabindex="-1" id="update-merchant">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('update_merchant_profile') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body modal-body-lg">
                <form
                    action="{{ Sentinel::getUser()->user_type == 'merchant' ? route('merchant.update.merchant') : route('merchant.staff.update.merchant') }}"
                    method="POST" enctype="multipart/form-data" id="update-merchant-form"
                    class="form-validate is-alter">
                    @csrf
                    @php $merchant = Sentinel::getUser()->user_type == 'merchant' ? \Sentinel::getUser()->merchant : \Sentinel::getUser()->staffMerchant @endphp
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="company">{{ __('company_name') }}</label>
                                <input type="text" name="merchant" hidden id="merchant"
                                    value="{{ $merchant->id }}">
                                <input type="text" name="company" class="form-control"
                                    value="{{ old('company') ?? $merchant->company }}" id="company"
                                    placeholder="{{ __('company_name') }}" required>
                            </div>
                            @if ($errors->has('company'))
                                <div class="invalid-feedback help-block">
                                    <p>{{ $errors->first('company') }}</p>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="phone_number">{{ __('phone') }}</label>
                                <input type="text" name="phone_number" class="form-control"
                                    value="{{ old('phone_number') ?? $merchant->phone_number }}" id="phone_number"
                                    placeholder="{{ __('phone') }}" required>
                            </div>
                            @if ($errors->has('phone_number'))
                                <div class="invalid-feedback help-block">
                                    <p>{{ $errors->first('phone_number') }}</p>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="website">{{ __('website') }}
                                    @if (!blank($merchant->website))
                                        <a href="{{ $merchant->website }}" target="_blank"> <i
                                                class="icon  las la-external-link-alt"></i></a>
                                    @endif
                                </label>
                                <input type="text" name="website" class="form-control"
                                    value="{{ old('website') ?? $merchant->website }}" id="city"
                                    placeholder="{{ __('website') }}">
                            </div>
                            @if ($errors->has('website'))
                                <div class="invalid-feedback help-block">
                                    <p>{{ $errors->first('website') }}</p>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="city">{{ __('city') }}</label>
                                <input type="text" name="city" class="form-control"
                                    value="{{ old('city') ?? $merchant->city }}" id="city"
                                    placeholder="{{ __('city') }}">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label" for="address">{{ __('address') }}</label>
                                <input type="text" name="address" class="form-control"
                                    value="{{ old('address') ?? $merchant->address }}" id="phone_number"
                                    placeholder="{{ __('address') }}">
                            </div>
                            @if ($errors->has('address'))
                                <div class="invalid-feedback help-block">
                                    <p>{{ $errors->first('address') }}</p>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="city">{{ __('zip') }}</label>
                                <input type="text" name="zip" class="form-control"
                                    value="{{ old('zip') ?? $merchant->zip }}" id="zip"
                                    placeholder="{{ __('zip') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"
                                    for="billing_street">{{ __('billing') . ' ' . 'street' }}</label>
                                <input type="text" name="billing_street" class="form-control"
                                    value="{{ old('billing_street') ?? $merchant->billing_street }}"
                                    id="billing_street" placeholder="{{ __('billing') . ' ' . 'street' }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"
                                    for="billing_city">{{ __('billing') . ' ' . __('city') }}</label>
                                <input type="text" name="billing_city" class="form-control"
                                    value="{{ old('billing_city') ?? $merchant->billing_city }}" id="billing_city"
                                    placeholder="{{ __('billing') . ' ' . __('city') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="billing_zip">{{ __('billing') . ' ' . __('zip') }}</label>
                                <input type="text" name="billing_zip" class="form-control"
                                    value="{{ old('billing_zip') ?? $merchant->billing_zip }}" id="billing_zip"
                                    placeholder="{{ __('billing') . ' ' . __('zip') }}">
                            </div>
                        </div>



                        <div class="col-lg-6 input_file_div">
                            <div class="mb-3 mt-2">
                                <label class="form-label mb-1">{{ __('trade_license') }}</label>
                                <input class="form-control sp_file_input file_picker" type="file" id="trade_license"
                                    name="trade_license">
                                <div class="invalid-feedback help-block">
                                    <p class="image_error error">{{ $errors->first('trade_license') }}</p>
                                </div>
                            </div>
                            <div class="selected-files d-flex flex-wrap gap-20">
                                <div class="selected-files-item">
                                    <img class="selected-img" src="{{ getFileLink('80X80', $merchant->trade_license) }}" alt="favicon">
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6 input_file_div">
                            <div class="mb-3 mt-2">
                                <label class="form-label mb-1">{{ __('nid') }}</label>
                                <input class="form-control sp_file_input file_picker" type="file" id="image" name="nid">
                                <div class="invalid-feedback help-block">
                                    <p class="image_error error">{{ $errors->first('nid') }}</p>
                                </div>
                            </div>
                            <div class="selected-files d-flex flex-wrap gap-20">
                                <div class="selected-files-item">
                                    <img class="selected-img" src="{{ getFileLink('80X80', $merchant->nid) }}"
                                    alt="favicon">
                                </div>
                            </div>
                        </div>
                        <div class="col-12 pt-3">
                            <div class="mb-3 text-right">
                                <button type="submit" class="btn sg-btn-primary">{{ __('update') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- add shop modal --}}
<div class="modal fade" tabindex="-1" id="add-shop">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('add_shop') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form
                    action="{{ Sentinel::getUser()->user_type == 'merchant' ? route('merchant.add.shop') : route('merchant.staff.add.shop') }}"
                    method="POST" class="form-validate is-alter" id="add-shop-form">
                    @csrf
                    <input type="hidden" name="id" value="" id="delivery-parcel-id">
                    <div class="mb-3">
                        <label class="form-label" for="shop_name">{{ __('shop_name') }}</label>
                        <input type="hidden" name="merchant" hidden id="merchant" value="{{ $merchant->id }}">
                        <input type="text" name="shop_name" class="form-control" value="{{ old('shop_name') }}"
                            id="shop_name" placeholder="{{ __('shop_name') }}" required>
                        @if ($errors->has('shop_name'))
                            <div class="invalid-feedback help-block">
                                <p>{{ $errors->first('shop_name') }}</p>
                            </div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="contact_number">{{ __('contact_number') }}</label>
                        <input type="text" name="contact_number" class="form-control"
                            value="{{ old('contact_number') }}" id="contact_number"
                            placeholder="{{ __('contact_number') }}" required>
                        @if ($errors->has('contact_number'))
                            <div class="invalid-feedback help-block">
                                <p>{{ $errors->first('contact_number') }}</p>
                            </div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="pickup_branch">{{__('pickup_branch')}}</label>
                        <select class="without_search form-control" name="pickup_branch">
                            <option value="">{{ __('select_branch') }}</option>
                            @foreach (@$branchs as $branch)
                                <option value="{{ @$branch->id }}">
                                    {{ __(@$branch->name) . ' (' . $branch->address . ')' }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('pickup_branch'))
                            <div class="invalid-feedback help-block">
                                <p>{{ $errors->first('pickup_branch') }}</p>
                            </div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="shop_phone_number">{{ __('pickup_number') }}</label>
                        <input type="text" name="shop_phone_number" class="form-control"
                            value="{{ old('shop_phone_number') }}" id="shop_phone_number"
                            placeholder="{{ __('pickup_number') }}" required>
                        @if ($errors->has('shop_phone_number'))
                            <div class="invalid-feedback help-block">
                                <p>{{ $errors->first('shop_phone_number') }}</p>
                            </div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="address">{{ __('pickup_address') }}</label>
                        <textarea name="address" class="form-control">{{ old('address') }}</textarea>
                        @if ($errors->has('address'))
                            <div class="invalid-feedback help-block">
                                <p>{{ $errors->first('address') }}</p>
                            </div>
                        @endif
                    </div>

                    <div class="mb-3 text-right mt-3">
                        <button type="submit" class="btn sg-btn-primary resubmit">{{ __('submit') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- warehouse modal --}}
<div class="modal fade" tabindex="-1" id="add_warehouse">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('add_warehouse') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form
                    action="{{ route('merchant.warehouse.store') }}"
                    method="POST" class="form" id="add-shop-form">
                    @csrf
                    <input type="hidden" name="merchant" hidden id="merchant" value="{{ $merchant->id }}">
                    <div class="mb-3">
                        <label class="form-label" for="warehouse_name">{{ __('warehouse_name') }}</label>
                        <input type="text" name="warehouse_name" class="form-control" value="{{ old('warehouse_name') }}"
                            id="warehouse_name" placeholder="{{ __('warehouse_name') }}">
                        <div class="nk-block-des text-danger">
                            <p class="warehouse_name_error error"></p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="phone_number">{{ __('contact_number') }}</label>
                        <input type="text" name="phone_number" class="form-control"
                            value="{{ old('phone_number') }}" id="phone_number"
                            placeholder="{{ __('phone_number') }}" >
                        <div class="nk-block-des text-danger">
                            <p class="phone_number_error error"></p>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="email">{{ __('email') }}</label>
                        <input type="email" name="email" class="form-control"
                            value="{{ old('email') }}" id="email"
                            placeholder="{{ __('email') }}" >
                        <div class="nk-block-des text-danger">
                            <p class="email_error error"></p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="address">{{ __('address') }}</label>
                        <textarea name="address" class="form-control">{{ old('address') }}</textarea>
                        <div class="nk-block-des text-danger">
                            <p class="address_error error"></p>
                        </div>
                    </div>

                    <div class="mb-3 text-right mt-3">
                        <button type="submit" class="btn sg-btn-primary resubmit">{{ __('submit') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


{{-- add product modal --}}
<div class="modal fade" tabindex="-1" id="add_product">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('add_product') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form
                    action="{{ route('merchant.products.store') }}"
                    method="POST" class="form" id="add-shop-form">
                    @csrf
                    <input type="hidden" name="merchant" hidden id="merchant" value="{{ $merchant->id }}">
                    <div class="mb-3">
                        <label class="form-label" for="name">{{ __('product_name') }} <span
                                class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                            id="name" placeholder="{{ __('product_name') }}">
                        <div class="nk-block-des text-danger">
                            <p class="name_error error"></p>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="description">{{ __('description') }}</label>
                        <textarea name="description" class="form-control">{{ old('description') }}</textarea>
                        <div class="nk-block-des text-danger">
                            <p class="description_error error"></p>
                        </div>
                    </div>
                    <div class="mb-3 text-right mt-3">
                        <button type="submit" class="btn sg-btn-primary resubmit">{{ __('submit') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


{{-- edit shop --}}
<div class="modal fade" tabindex="-1" id="edit-shop">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('edit_shop') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="shop_update" class="shop_update">

                </div>
            </div>
        </div>
    </div>
</div>
@push('script')
    <script type="text/javascript">
        $(document).on('click','.shop-update',function(e){
            e.preventDefault();
            var route = $(this).attr('data-url');
            var url = "{{ url('') }}" + route;
            var shop_id = $(this).attr('data-id');

            var formData = {
                shop_id: shop_id
            }
            $.ajax({
                type: "GET",
                dataType: 'html',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                success: function(data) {
                    $('#shop_update').html(data);
                },
                error: function(data) {}
            });
        });
    </script>
{{--    @if ($errors->any())--}}
{{--        <script>--}}
{{--            document.addEventListener("DOMContentLoaded", function () {--}}
{{--                const errorModal = document.getElementById('add_warehouse');--}}
{{--                if (errorModal) {--}}
{{--                    const modal = new bootstrap.Modal(errorModal);--}}
{{--                    modal.show();--}}
{{--                }--}}
{{--            });--}}
{{--        </script>--}}
{{--    @endif--}}

@endpush
