@extends('backend.layouts.master')

@section('title')
    {{ __('import') }}
@endsection

@section('mainContent')

    <style>
        input#choose_file {
            padding-left: 11px;
            height: 38px;
        }
    </style>

    <div class="container-fluid">
        <div class="row gx-20">
            <div class="col-lg-12">
                <div class="header-top d-flex justify-content-between align-items-center mb-12">
                    <h3 class="section-title">{{ __('import') }}</h3>
                    <div class="oftions-content-right">
                        <a href="{{ route('parcel') }}" class="d-flex align-items-center btn sg-btn-primary gap-2">
                            <i class="las la-arrow-left"></i>
                            <span>{{ __('back') }}</span>
                        </a>
                    </div>
                </div>
                <form
                    action="{{ Sentinel::getUser()->user_type == 'merchant' ? route('merchant.import') : (Sentinel::getUser()->user_type == 'merchant_staff' ? route('merchant.staff.import') : route('import')) }}"
                    class="form-validate" id="parcel-form" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="bg-white redious-border p-20 p-sm-30 pt-sm-30">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card-inner">
                                    <div class="row g-gs">
                                        <div class="col-md-6">
                                            @if (Sentinel::getUser()->user_type != 'merchant' && Sentinel::getUser()->user_type != 'merchant_staff')
                                                <div class="mb-3">
                                                    <label class="form-label" for="merchant">{{ __('merchants') }}
                                                        <span class="text-danger">*</span></label>
                                                    <select
                                                        class="with_search form-select form-control @error('merchant') is-invalid @enderror"
                                                        id="selectMerchant" name="merchant" required>
                                                        <option value="">{{ __('select_merchant') }} </option>
                                                        @foreach ($merchants as $item)
                                                            <option
                                                                value="{{$item->merchant->id}}"> {{$item->first_name.' '.$item->last_name}} </option>
                                                        @endforeach
                                                    </select>
                                                    @error('merchant')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            @endif
                                            <div class="mb-3">
                                                <label class="form-label" for="shop">{{ __('Shop') }}
                                                    <span class="text-danger">*</span></label>
                                                <select
                                                    class="with_search form-select form-control shop @error('shop') is-invalid @enderror"
                                                    id="shop" name="shop" required>
                                                    <option value="">{{ __('select_shop') }} </option>
                                                    @if (Sentinel::getUser()->user_type == 'merchant')
                                                        @foreach (App\Models\Shop::where('merchant_id', Sentinel::getUser()->merchant->id)->get() as $item)
                                                            <option value="{{$item->id}}"> {{$item->shop_name}}</option>
                                                        @endforeach
                                                    @endif
                                                    @if (Sentinel::getUser()->user_type == 'merchant_staff')
                                                        @foreach (App\Models\Shop::where('merchant_id', Sentinel::getUser()->merchant_id)->get() as $item)
                                                            <option value="{{$item->id}}"> {{$item->shop_name}}</option>
                                                        @endforeach
                                                    @endif

                                                </select>
                                                @error('shop')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="choose_file"
                                                       class="form-label">{{ __('choose_file') }}</label>
                                                <input class="form-control" name="file" type="file" id="choose_file"
                                                       accept=".xlsx, .csv">
                                            </div>
                                            @if ($errors && $errors->any())
                                                @foreach ($errors->all() as $error)
                                                    <div class="invalid-feedback help-block">
                                                        <p>{{ $error }}</p>
                                                    </div>
                                                @endforeach
                                            @endif
                                            <div class="col-md-12 text-right mt-4">
                                                <div class="mb-3">
                                                    <button type="submit"
                                                            class="btn sg-btn-primary resubmit">{{ __('submit') }}</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h5>{{ __('n_b') }}</h5>
                                            <p>{{ __('please_check_this_before_importing_your_file') }}</p>
                                            <ul class="list list-sm list-success">
                                                <li>{{ __('uploaded_file_must_be_xlsx_or_csv') }}</li>
                                                <li>{{ __('the_file_must_contain_price_selling_price_customer_name_customer_invoice_no_customer_phone_number_customer_address') }}
                                                </li>
                                                <li>{{ __('price_and_selling_price_must_be_numeric_example') }}</li>
                                                <li>{{ __('fragile_parcel_type_note_weight_pickup_shop_phone_number_pickup_address_pickup_branch') }}
                                                </li>
                                                <li>{{ __('if_parcel_type_not_provided_by_default_it_will_be_set_for_next_day') }}
                                                </li>
                                                <li>{{ __('if_weight_not_provided_by_default_weight_will_be_1') }}</li>
                                                <li>{{ __('if_parcel_types_provided_and_not_available_this_row_will_be_auto_ignored') }}
                                                </li>
                                                @if (hasPermission('parcel_create') || hasPermission('manage_parcel') || Sentinel::getUser()->user_type == 'merchant')
                                                    <a class="import-sample-btn"
                                                       href="{{ Sentinel::getUser()->user_type == 'merchant' ? route('merchant.export') : (Sentinel::getUser()->user_type == 'merchant_staff' ? route('merchant.staff.export') : route('export')) }}">
                                                        <span><i
                                                                class="icon las la-file-download"></i></span>
                                                        <span>{{ __('parcel_import_sample') . ' ' . __('download') }}</span>
                                                    </a>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script>
        $(document).on('change', '#selectMerchant', function () {
            const merchantId = $(this).val();
            const shopSelect = $('#shop');
            shopSelect.html('<option value="">Loading...</option>');

            if (merchantId) {
                $.ajax({
                    url: "{{ route('get.shops.by.merchant') }}",
                    type: "GET",
                    data: {merchant_id: merchantId},
                    success: function (response) {
                        let options = '<option value="">{{ __('select_shop') }}</option>';
                        if (Array.isArray(response)) {
                            response.forEach(shop => {
                                options += `<option value="${shop.id}">${shop.shop_name}</option>`;
                            });
                        } else {
                            options += '<option disabled>No valid shop data</option>';
                        }
                        shopSelect.html(options);

                    },
                    error: function () {
                        shopSelect.html('<option value="">{{ __('no_shops_found') }}</option>');
                    }
                });
            } else {
                shopSelect.html('<option value="">{{ __('select_shop') }}</option>');
            }
        });
    </script>
@endpush
