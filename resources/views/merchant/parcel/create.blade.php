@extends('backend.layouts.master')

@section('title')
    {{ (@$parcel ? __('duplicate') : __('add')) . ' ' . __('parcel') }}
@endsection

@section('mainContent')
    <div class="container-fluid">
        <div class="row gx-20">
            <div class="col-lg-12">
                <div class="header-top d-flex justify-content-between align-items-center mb-12">
                    <h3 class="section-title">{{ @$parcel ? __('duplicate') : __('add') }}
                        {{ __('parcel') }}
                    </h3>
                    <div class="oftions-content-right">
                        <a href="{{ url()->previous() }}" class="d-flex align-items-center btn sg-btn-primary gap-2">
                            <i class="las la-arrow-left"></i>
                            <span>{{ __('back') }}</span>
                        </a>
                    </div>
                </div>
                <form
                    action="{{ Sentinel::getUser()->user_type == 'merchant' ? route('merchant.parcel.store') : route('merchant.staff.parcel.store') }}"
                    class="form-validate" method="POST" enctype="multipart/form-data">
                    <input type="hidden"
                        value="{{ Sentinel::getUser()->user_type == 'merchant' ? Sentinel::getUser()->merchant->id : Sentinel::getUser()->merchant_id }}"
                        name="merchant" class="merchant">
                    @csrf
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card bg-white redious-border p-20 p-sm-30 pt-sm-30">
                                <!-- Row 1: Invoice# & Shop -->
                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <label class="form-label" for="customer_invoice_no">{{ __('invoice') }}#</label>
                                        <input type="text"
                                            class="form-control @error('customer_invoice_no') is-invalid @enderror"
                                            id="customer_invoice_no"
                                            value="{{ old('customer_invoice_no') != '' ? old('customer_invoice_no') : @$parcel->customer_invoice_no }}"
                                            name="customer_invoice_no" placeholder="{{ __('invoice_or_memo_no') }}">
                                        @if ($errors->has('customer_invoice_no'))
                                            <div class="invalid-feedback help-block">
                                                <p>{{ $errors->first('customer_invoice_no') }}</p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-6 mb-3">
                                        <label class="form-label" for="area">{{ __('shop') }}</label>
                                        <select
                                            class="without_search form-select form-control select-shop @error('shop') is-invalid @enderror"
                                            data-url="{{ Sentinel::getUser()->user_type == 'merchant' ? route('merchant.shop') : route('merchant.staff.shop') }}"
                                            name="shop">
                                            <option value="">{{ __('select_shop') }}</option>
                                            @foreach ($shops as $shop)
                                                <option value="{{ $shop->id }}" {{ @$parcel->shop_id == $shop->id ? 'selected' : ($shop->default ? 'selected' : '') }}> {{ __($shop->shop_name) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('shop'))
                                            <div class="invalid-feedback help-block">
                                                <p>{{ $errors->first('shop') }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Row 2: Customer Name & Customer Phone -->
                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <label class="form-label" for="customer_name">{{ __('customer') . ' ' . __('name') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('customer_name') is-invalid @enderror"
                                            id="customer_name"
                                            value="{{ old('customer_name') != '' ? old('customer_name') : @$parcel->customer_name }}"
                                            name="customer_name" placeholder="{{ __('recipient_name') }}">
                                        @if ($errors->has('customer_name'))
                                            <div class="invalid-feedback help-block">
                                                <p>{{ $errors->first('customer_name') }}</p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-6 mb-3">
                                        <label class="form-label" for="customer_phone_number">{{ __('customer') . ' ' . __('phone') }} <span class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control @error('customer_phone_number') is-invalid @enderror"
                                            id="customer_phone_number"
                                            value="{{ old('customer_phone_number') != '' ? old('customer_phone_number') : @$parcel->customer_phone_number }}"
                                            name="customer_phone_number"
                                            maxlength="14"
                                            placeholder="01XXXXXXXXX">
                                        @if ($errors->has('customer_phone_number'))
                                            <div class="invalid-feedback help-block">
                                                <p>{{ $errors->first('customer_phone_number') }}</p>
                                            </div>
                                        @endif
                                        <div id="customer_phone_error" class="invalid-feedback d-none">
                                            {{ __('Please enter a valid 11-digit Bangladeshi mobile number (e.g. 017XXXXXXXX).') }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Row 3: Cash Collection & Customer Address -->
                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <label class="form-label" for="fv-full-name">{{ __('cash_collection') }} ({{ setting('default_currency') }}) <span class="text-danger">*</span></label>
                                        <input type="number"
                                            class="form-control cash-collection @error('price') is-invalid @enderror"
                                            id="fv-full-name"
                                            value="{{ old('price') != '' ? old('price') : @$parcel->price }}" name="price"
                                            placeholder="{{ __('cash_amount_including_delivery_charge') }}">
                                        @if ($errors->has('price'))
                                            <div class="invalid-feedback help-block">
                                                <p>{{ $errors->first('price') }}</p>
                                            </div>
                                        @endif
                                    </div>



                                     <!-- Row 6: Selling Price & Packaging -->
                                    <div class="col-6 mb-3">
                                        <label class="form-label" for="selling_price">{{ __('selling_price') }} ({{ setting('default_currency') }})</label>
                                        <input type="number" class="form-control @error('selling_price') is-invalid @enderror"
                                            id="selling_price" value="{{ old('selling_price') != '' ? old('selling_price') : @$parcel->selling_price }}"
                                            name="selling_price" placeholder="{{ __('selling_price_of_parcel') }}">
                                        @error('selling_price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                     <div class="col-md-6 mb-3">
                                            <label class="form-label" for="total_quantity">{{ __('total_quantity') }}</label>
                                            <input type="number" min="1" name="total_quantity" id="total_quantity" class="form-control" value="{{ old('total_quantity', 1) }}" placeholder="{{ __('total_quantity') }}">
                                        </div>

                                   



                                         


                                    <div class="col-6 mb-3">
                                        <label class="form-label" for="customer_address">{{ __('customer') . ' ' . __('address') }} <span class="text-danger">*</span></label>
                                        <textarea class="form-control @error('customer_address') is-invalid @enderror"
                                            id="customer_address" rows="3" placeholder="{{ __('recipient') . ' ' . __('address') }}"
                                            name="customer_address">{{ old('customer_address') != '' ? old('customer_address') : @$parcel->customer_address }}</textarea>
                                        @if ($errors->has('customer_address'))
                                            <div class="invalid-feedback help-block">
                                                <p>{{ $errors->first('customer_address') }}</p>
                                            </div>
                                        @endif
                                        <div id="address_auto_detect_badge" class="badge bg-soft-success text-success mt-2 p-1 px-2 d-none" style="font-size: 13px; border: 1px solid #28a745; border-radius: 4px; display: inline-block;">
                                            <i class="las la-magic me-1"></i> <span id="address_auto_detect_text"></span>
                                        </div>
                                        <div id="address_error_msg" class="invalid-feedback d-none">
                                            Please enter a valid address containing district and thana.
                                        </div>
                                    </div>
                                </div>

                                <!-- Row 4: District & Thana -->
                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <label class="form-label" for="city_to_thana">{{ __('district') }} / District <span class="text-danger">*</span></label>
                                        <select
                                            class="without_search form-select form-control city_id district_id @error('city_id') is-invalid @enderror"
                                            name="city_id" id="city_to_thana">
                                            <option value="">Select District</option>
                                            @foreach($districts as $district)
                                                <option value="{{ $district->id }}"
                                                    data-name="{{ strtolower($district->name) }}"
                                                    {{ old('city_id', @$parcel->district_id ?? @$parcel->city_id) == $district->id ? 'selected' : '' }}>
                                                    {{ $district->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('city_id'))
                                            <div class="invalid-feedback help-block">
                                                <p>{{ $errors->first('city_id') }}</p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-6 mb-3">
                                        <label class="form-label" for="thana_to_area">Thana <span class="text-danger">*</span></label>
                                        <select style="width:100%"
                                            class="without_search form-select form-control thana_id @error('thana_id') is-invalid @enderror"
                                            name="thana_id" id="thana_to_area">
                                            <option value="">Select Thana</option>
                                            @if(isset($thanas))
                                                @foreach($thanas as $thana)
                                                    <option value="{{ $thana->id }}"
                                                        {{ old('thana_id', @$parcel->thana_id) == $thana->id ? 'selected' : '' }}>
                                                        {{ $thana->name }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @if ($errors->has('thana_id'))
                                            <div class="invalid-feedback help-block">
                                                <p>{{ $errors->first('thana_id') }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Row 5: Delivery Area & Weight -->
                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <label class="form-label" for="delivery_area">{{ __('delivery_area') }} <span class="text-danger">*</span></label>
                                        <select
                                            class="without_search form-select form-control parcel_type @error('parcel_type') is-invalid @enderror"
                                            id="delivery_area"
                                            name="parcel_type">
                                            <option value="" selected disabled>{{ __('select_type') }}</option>
                                            @if (settingHelper('preferences')->where('title', 'same_day')->first()->merchant)
                                                <option value="same_day" {{ old('parcel_type') == 'same_day' ? 'selected' : (@$parcel->parcel_type == 'same_day' ? 'selected' : '') }}>
                                                    {{ __('same_day') }}
                                                </option>
                                            @endif
                                            @if (settingHelper('preferences')->where('title', 'sub_city')->first()->merchant)
                                                <option value="sub_city" {{ old('parcel_type') == 'sub_city' ? 'selected' : (@$parcel->parcel_type == 'sub_city' ? 'selected' : '') }}>
                                                    {{ __('sub_city') }}
                                                </option>
                                            @endif
                                            @if (settingHelper('preferences')->where('title', 'sub_urban_area')->first()->merchant)
                                                <option value="outside_city" {{ old('parcel_type') == 'outside_city' ? 'selected' : (@$parcel->parcel_type == 'sub_urban_area' ? 'selected' : '') }}>
                                                    {{ __('sub_urban_area') }}
                                                </option>
                                            @endif
                                        </select>
                                        @if ($errors->has('parcel_type'))
                                            <div class="invalid-feedback help-block">
                                                <p>{{ __('delivery_area_required') }}</p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-6 mb-3">
                                        <label class="form-label" for="weight">{{ __('weight') }} <span class="text-danger">*</span></label>
                                        <input type="number"
                                            class="form-control weight @error('weight') is-invalid @enderror"
                                            name="weight"
                                            id="weight"
                                            min="0"
                                            step="0.01"
                                            value="{{ old('weight') != '' ? old('weight') : (@$parcel->weight ?? 0) }}"
                                            placeholder="{{ __('weight') }}">
                                        @if ($errors->has('weight'))
                                            <div class="invalid-feedback help-block">
                                                <p>{{ $errors->first('weight') }}</p>
                                            </div>
                                        @endif
                                    </div>

                                    
                                </div>

                                <!-- Row 6: Note & Packaging -->
                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <label class="form-label" for="note">{{ __('note') }}</label>
                                        <textarea class="form-control" id="note" rows="3"
                                            placeholder="{{ __('note') . ' (' . __('parcel_note_from_merchant') . ')' }}"
                                            name="note">{{ old('note') != '' ? old('note') : @$parcel->note }}</textarea>
                                        @if ($errors->has('note'))
                                            <div class="invalid-feedback help-block">
                                                <p>{{ $errors->first('note') }}</p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-6 mb-3">
                                        <label class="form-label" for="packaging">{{ __('packaging') }}</label>
                                        <select class="without_search form-select form-control packaging" name="packaging" id="packaging">
                                            <option value="no">{{ __('select_packing') }}</option>
                                            @foreach (settingHelper('package_and_charges') as $package_and_charge)
                                                <option value="{{ $package_and_charge->id }}" {{ isset($parcel) ? ($parcel->packaging == $package_and_charge->id ? 'selected' : '') : '' }}>
                                                    {{ __($package_and_charge->package_type) }}
                                                    ({{ format_price($package_and_charge->charge) }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Checkboxes -->
                                <div class="row mt-2">
                                    <div class="col-12">
                                        <div class="form-check mb-2">
                                            <input type="checkbox" class="form-check-input" id="transfer_to_branch" value="1" name="transfer_to_branch"
                                                {{ old('transfer_to_branch') ? 'checked' : '' }}>
                                            <label class="form-check-label text-capitalize" for="transfer_to_branch" style="cursor: pointer;">
                                                {{ __('transfer_to_branch') }}
                                            </label>
                                        </div>

                                        <div class="col-6 mb-3 px-0 {{ old('transfer_to_branch') ? '' : 'd-none' }}" id="transfer_branch_wrapper">
                                            <label class="form-label" for="destination_branch_id">{{ __('select_branch') }} <span class="text-danger">*</span></label>
                                            <select class="without_search form-select form-control @error('destination_branch_id') is-invalid @enderror"
                                                name="destination_branch_id" id="destination_branch_id">
                                                <option value="" selected disabled>{{ __('select_branch') }}</option>
                                                @foreach ($branch as $item)
                                                    <option value="{{ $item->id }}"
                                                        {{ (old('destination_branch_id') == $item->id || old('transfer_branch_select_id') == $item->id) ? 'selected' : '' }}>
                                                        {{ $item->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('destination_branch_id'))
                                                <div class="invalid-feedback help-block">
                                                    <p>{{ $errors->first('destination_branch_id') }}</p>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="form-check mb-2">
                                            <input type="checkbox" class="form-check-input" id="open_box" value="1" name="open_box">
                                            <label class="form-check-label text-capitalize" for="open_box" style="cursor: pointer;">
                                                {{ __('open_box') }}
                                            </label>
                                        </div>

                                        <div class="form-check mb-3">
                                            <input type="checkbox" class="form-check-input" id="home_delivery" name="home_delivery" value="1" checked>
                                            <label class="form-check-label text-capitalize" for="home_delivery" style="cursor: pointer;">
                                                {{ __('home_delivery') }}
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="row">
                                    <div class="col-12 mt-2">
                                        <button type="submit" class="btn sg-btn-primary resubmit">{{ __('submit') }}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-white redious-border p-20 p-sm-30 pt-sm-30">
                                <div class="card-title-group mb-2">
                                    <div class="card-title">
                                        <h6 class="title">{{ __('charge_details') }}</h6>
                                    </div>
                                </div>
                                <ul class="nk-top-products">
                                    <div class="card-inner p-0">
                                        <table class="table">
                                            <tr>
                                                <th><span class="sub-text"><strong>{{ __('title') }}</strong></span>
                                                </th>
                                                <th><span class="sub-text"><strong>{{ __('amount') }}</strong></span>
                                                </th>
                                            </tr>
                                            <tr>

                                                <td>
                                                    <span>{{ __('cash_collection') }}</span>
                                                </td>
                                                <td>
                                                    <span
                                                        id="cash-collection-charge">{{ isset($parcel) ? $parcel->price : '0.00' }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <span>{{ __('delivery_charge') }}</span>
                                                </td>
                                                <td>
                                                    <span
                                                        id="delivery-charge">{{ isset($parcel) ? $parcel->charge : '0.00' }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <span>{{ __('cod_charge') }}</span>
                                                </td>
                                                <td>
                                                    <span
                                                        id="cod-charge">{{ isset($parcel) ? ($parcel->price / 100) * $parcel->cod_charge : '0.00' }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <span>{{ __('vat') }}</span>
                                                </td>
                                                <td>
                                                    <span
                                                        id="vat-charge">{{ isset($parcel) ? (($parcel->charge + $parcel->fragile_charge + $parcel->packaging_charge + ($parcel->price / 100) * $parcel->cod_charge) / 100) * $parcel->vat : '0.00' }}</span>
                                                </td>
                                            </tr>
                                            {{-- <tr
                                                class="fragile-charge-area {{ isset($parcel) ? ($parcel->fragile == 0 ? 'd-none' : '') : 'd-none' }}">

                                                <td>
                                                    <span>{{ __('liquid') }}/{{ __('fragile_charge') }}</span>
                                                </td>
                                                <td>
                                                    <span
                                                        id="fragile-charge">{{ isset($parcel) ? ($parcel->fragile == 0 ? '0.00' : $parcel->fragile_charge) : '0.00' }}</span>
                                                </td>
                                            </tr> --}}
                                            <tr
                                                class="packaging-charge-area {{ isset($parcel) ? ($parcel->packaging == 'no' ? 'd-none' : '') : 'd-none' }}">

                                                <td>
                                                    <span>{{ __('packaging_charge') }}</span>
                                                </td>
                                                <td>
                                                    <span
                                                        id="packaging-charge">{{ isset($parcel) ? ($parcel->packaging == 'no' ? '0.00' : $parcel->packaging_charge) : '0.00' }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <span>{{ __('total_delivery_charge') }}</span>
                                                </td>
                                                <td>
                                                    <span
                                                        id="total-delivery-charge">{{ isset($parcel) ? $parcel->total_delivery_charge : '0.00' }}</span>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td><span class="sub-text" id="currency"
                                                        data-default-currency="{{ setting('default_currency') }}"><strong>{{ __('current_payable') }}</strong></span>
                                                </td>
                                                <td><span class="sub-text"><strong id="current-payable-charge">
                                                            {{ setting('default_currency') }}
                                                            {{ isset($parcel) ? $parcel->payable : '0.00' }}</strong></span>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </ul>
                            </div>

                            <div class="card bg-white redious-border p-20 p-sm-30 pt-sm-30 mt-4">
                                <div class="mb-3">
                                    <label class="form-label" for="pickup_branch_id">{{ __('pickup_branch') }}</label>
                                    <input type="text" class="form-control @error('pickup_branch_id') is-invalid @enderror"
                                        id="shop_pickup_branch"
                                        value="{{ old('pickup_branch_id') ? old('pickup_branch_id') : (@$parcel->branch->name ? @$parcel->branch->name : @$default_shop->branch->name) }}"
                                        name="pickup_branch_id" placeholder="{{ __('pickup_branch') }}" readonly>
                                    @if ($errors->has('pickup_branch_id'))
                                        <div class="invalid-feedback help-block">
                                            <p>{{ $errors->first('pickup_branch_id') }}</p>
                                        </div>
                                    @endif
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="shop_phone_number">{{ __('pickup_number') }}</label>
                                    <input type="text" class="form-control" id="shop_phone_number"
                                        value="{{ old('shop_phone_number') ? old('shop_phone_number') : (@$parcel->pickup_shop_phone_number ? @$parcel->pickup_shop_phone_number : @$default_shop->shop_phone_number) }}"
                                        name="shop_phone_number" placeholder="{{ __('pickup_number') }}" readonly>
                                    @if ($errors->has('shop_phone_number'))
                                        <div class="invalid-feedback help-block">
                                            <p>{{ $errors->first('shop_phone_number') }}</p>
                                        </div>
                                    @endif
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="shop_address">{{ __('pickup_address') }}</label>
                                    <input type="text" class="form-control" id="shop_address"
                                        value="{{ old('shop_address') ? old('shop_address') : (@$parcel->pickup_address ? @$parcel->pickup_address : @$default_shop->address) }}"
                                        name="shop_address" placeholder="{{ __('pickup_address') }}" readonly>
                                    @if ($errors->has('shop_address'))
                                        <div class="invalid-feedback help-block">
                                            <p>{{ $errors->first('shop_address') }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @include('admin.parcel.charge-script')
    @include('admin.parcel.address-detect-script')

@push('script')
<script>
$(document).ready(function () {
    $('#transfer_to_branch').on('change', function () {
        if ($(this).is(':checked')) {
            $('#transfer_branch_wrapper').removeClass('d-none'); 
        } else {
            $('#transfer_branch_wrapper').addClass('d-none');    
            $('select[name="destination_branch_id"]').val('').trigger('change');
        }
    });
});
</script>
@endpush

@endsection
