@extends('backend.layouts.master')
@section('parcel', 'active')
@section('title')
{{ __('edit') . ' ' . __('parcel') }}
@endsection
@section('mainContent')
<div class="container-fluid">
    <div class="row gx-20">
        <div class="col-lg-12">
            <div class="header-top d-flex justify-content-between align-items-center mb-12">
                <h3 class="section-title">{{ __('edit') }} {{ __('parcel') }}</h3>
                <div class="oftions-content-right">
                    <a href="{{ url()->previous() }}" class="d-flex align-items-center btn sg-btn-primary gap-2">
                        <i class="las la-arrow-left"></i>
                        <span>{{ __('back') }}</span>
                    </a>
                </div>
            </div>
            <form action="{{ route('parcel.update') }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" value="{{ $parcel->id }}">
                <div class="row">
                    <div class="col-md-8">
                        <div class="card bg-white redious-border p-20 p-sm-30 flex-column h-100 d-flex">
                            <div class="row g-gs">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('merchant') }} <span
                                                class="text-danger">*</span></label>
                                        <select id="merchant-live-search" name="merchant"
                                            class="without_search form-control select-merchant merchant merchant-live-search @error('merchant_id') is-invalid @enderror"
                                            data-url="{{ route('merchant.change') }}">
                                            <option value="">{{ __('select_merchant') }}</option>
                                            <option value="{{ $parcel->merchant->id }}" selected>
                                                {{ $parcel->merchant->user->first_name . ' ' . $parcel->merchant->user->last_name }}
                                                ({{ $parcel->merchant->company }})
                                            </option>
                                        </select>
                                        @if ($errors->has('merchant_id'))
                                        <div class="invalid-feedback help-block">
                                            <p>{{ $errors->first('merchant_id') }}</p>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label" for="area">{{ __('shop') }}
                                        </label>
                                        <select class="without_search form-select form-control select-shop @error('shop') is-invalid @enderror"
                                            data-url="{{ route('admin.merchant.shop') }}"
                                            id="merchant_select" name="shop">
                                            <option value="">{{ __('select_shop') }}</option>
                                            @foreach ($parcel->merchant->shops as $shop)
                                            <option value="{{ $shop->id }}"
                                                {{ @$parcel->shop_id == $shop->id ? 'selected' : '' }}>
                                                {{ __($shop->shop_name) }}
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
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label"
                                            for="customer_invoice_no">{{ __('invoice') }}# </label>
                                        <input type="text" class="form-control @error('customer_invoice_no') is-invalid @enderror"
                                            value="{{ old('customer_invoice_no') != '' ? old('customer_invoice_no') : $parcel->customer_invoice_no }}"
                                            name="customer_invoice_no"
                                            placeholder="{{ __('invoice_or_memo_no') }}">
                                        @error('customer_invoice_no')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <!--
                                        **important note: using label**
                                        here "delivery_area" that serves as "parcel_type" in code and gets added in database as "location"  
                                        -->
                                        <label class="form-label" for="area">{{ __('delivery_area') }}
                                            <span class="text-danger">*</span></label>
                                        <select class="without_search form-select form-control parcel_type @error('parcel_type') is-invalid @enderror"
                                            name="parcel_type">
                                            <option value="">{{ __('select_type') }}</option>
                                            @if (settingHelper('preferences')->where('title', 'same_day')->first()->staff)
                                            <option value="same_day"
                                                {{ old('parcel_type') == 'same_day' ? 'selected' : ($parcel->parcel_type == 'same_day' ? 'selected' : '') }}>
                                                {{ __('same_day') }}
                                            </option>
                                            @endif

                                            @if (settingHelper('preferences')->where('title', 'sub_city')->first()->staff)
                                            <option value="sub_city"
                                                {{ old('parcel_type') == 'sub_city' ? 'selected' : ($parcel->parcel_type == 'sub_city' ? 'selected' : '') }}>
                                                {{ __('sub_city') }}
                                            </option>
                                            @endif
                                            @if (settingHelper('preferences')->where('title', 'sub_urban_area')->first()->staff)
                                            <option value="outside_city"
                                                {{ old('parcel_type') == 'outside_city' ? 'selected' : ($parcel->parcel_type == 'sub_urban_area' ? 'selected' : '') }}>
                                                {{ __('sub_urban_area') }}
                                            </option>
                                            @endif
                                        </select>
                                        @error('parcel_type')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label"
                                            for="fv-full-name">{{ __('cash_collection') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control cash-collection @error('price') is-invalid @enderror"
                                            id="fv-full-name"
                                            value="{{ old('price') != '' ? old('price') : $parcel->price }}"
                                            name="price"
                                            placeholder="{{ __('cash_amount_including_delivery_charge') }}">
                                        @error('price')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label"
                                            for="fv-full-name">{{ __('selling_price') }}</label>
                                        <input type="text" class="form-control @error('selling_price') is-invalid @enderror" id="fv-full-name"
                                            value="{{ old('selling_price') != '' ? old('selling_price') : $parcel->selling_price }}"
                                            name="selling_price"
                                            placeholder="{{ __('selling_price_of_parcel') }}">
                                        @error('selling_price')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label" for="fv-full-name">{{ __('weight') }}
                                            <span class="text-danger">*</span></label>
                                        <select class="without_search form-select form-control weight @error('weight') is-invalid @enderror"
                                            name="weight">
                                            <option value="">{{ __('select_weight') }}</option>
                                            @foreach ($charges as $charge)
                                            <option value="{{ $charge->weight }}"
                                                {{ $charge->weight == @$parcel->weight ? 'selected' : '' }}>
                                                {{ $charge->weight }} {{ __(setting('default_weight')) }}
                                            </option>
                                            @endforeach
                                        </select>
                                        @error('weight')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label"
                                            for="customer_name">{{ __('customer') . ' ' . __('name') }}
                                            <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('customer_name') is-invalid @enderror" id="customer_name"
                                            value="{{ old('customer_name') != '' ? old('customer_name') : $parcel->customer_name }}"
                                            name="customer_name"
                                            placeholder="{{ __('recipient_name') }}">
                                        @error('customer_name')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>
                                </div>



                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label"
                                            for="customer_phone_number">{{ __('customer') . ' ' . __('phone') }}
                                            <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('customer_phone_number') is-invalid @enderror"
                                            id="customer_phone_number"
                                            value="{{ old('customer_phone_number') != '' ? old('customer_phone_number') : (isDemoMode() ? '**************' : ($parcel->customer_phone_number ?? '')) }}"
                                            name="customer_phone_number"
                                            maxlength="14"
                                            placeholder="01XXXXXXXXX">
                                        @error('customer_phone_number')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                        <div id="customer_phone_error" class="invalid-feedback d-none">
                                            {{ __('Please enter a valid 11-digit Bangladeshi mobile number (e.g. 017XXXXXXXX).') }}
                                        </div>
                                    </div>
                                </div>




                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label" for="adjustment">{{ __('adjustment') }}</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">+/-</span>
                                            <input type="number" step="any" class="form-control @error('adjustment') is-invalid @enderror"
                                                id="adjustment"
                                                value="{{ old('adjustment', $parcel->adjustment ?? 0) }}"
                                                name="adjustment"
                                                placeholder="{{ __('adjustment') }}">
                                        </div>
                                        <small class="text-muted"><i class="las la-info-circle"></i> Use minus (-) for deduction. (e.g. -50)</small>
                                        @error('adjustment')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label" for="total_quantity">{{ __('total_quantity') }}</label>
                                        <input type="number" min="1" name="total_quantity" id="total_quantity" class="form-control"
                                            value="{{ old('total_quantity', $parcel->total_quantity ?? 1) }}"
                                            placeholder="{{ __('total_quantity') }}">
                                    </div>
                                </div>

                            </div>

                            <!-- Row: District & Thana -->
                            <div class="row g-gs">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="city_to_thana">{{ __('district') }} / District <span class="text-danger">*</span></label>
                                        <select class="without_search form-select form-control city_id district_id @error('city_id') is-invalid @enderror"
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
                                            <div class="invalid-feedback help-block"><p>{{ $errors->first('city_id') }}</p></div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="thana_to_area">Thana <span class="text-danger">*</span></label>
                                        <select style="width:100%" class="without_search form-select form-control thana_id @error('thana_id') is-invalid @enderror"
                                            name="thana_id" id="thana_to_area">
                                            <option value="">Select Thana</option>
                                            @if(isset($thanas))
                                                @foreach($thanas as $thana)
                                                    <option value="{{ $thana->id }}" {{ old('thana_id', @$parcel->thana_id) == $thana->id ? 'selected' : '' }}>
                                                        {{ $thana->name }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @if ($errors->has('thana_id'))
                                            <div class="invalid-feedback help-block"><p>{{ $errors->first('thana_id') }}</p></div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="row g-gs">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"
                                            for="customer_address">{{ __('customer') . ' ' . __('address') }}
                                            <span class="text-danger">*</span></label>
                                        <textarea class="form-control @error('customer_address') is-invalid @enderror" id="customer_address" placeholder="{{ __('recipient') . ' ' . __('address') }}"
                                            name="customer_address">{{ old('customer_address') != '' ? old('customer_address') : $parcel->customer_address }}</textarea>
                                        @error('customer_address')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                        <div id="address_auto_detect_badge" class="badge bg-soft-success text-success mt-2 p-1 px-2 d-none" style="font-size: 13px; border: 1px solid #28a745; border-radius: 4px; display: inline-block;">
                                            <i class="las la-magic me-1"></i> <span id="address_auto_detect_text"></span>
                                        </div>
                                        <div id="address_error_msg" class="invalid-feedback d-none">
                                            Please enter a valid address containing district and thana.
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="note">{{ __('note') }}
                                        </label>
                                        <textarea class="form-control" id="note" placeholder="{{ __('note') . ' (' . __('optional') . ')' }}"
                                            name="note">{{ old('note') != '' ? old('note') : $parcel->note }}</textarea>
                                        @if ($errors->has('note'))
                                        <div class="invalid-feedback help-block">
                                            <p>{{ $errors->first('note') }}</p>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row g-gs">
                                {{-- <div class="col-md-6">
                                    <div class="row pt-1">
                                        <label class="form-label mt-4"></label>
                                        <div class="col-md-6 w-100">
                                            <div class="custom-checkbox mb-3">
                                                <label class="custom-control-label"
                                                    for="fragile">
                                                    <input type="checkbox"
                                                        class="custom-control-input"
                                                        id="fragile" name="fragile"
                                                        {{ $parcel->fragile == 1 ? 'checked' : '' }}>
                                                    <span class="text-capitalize">
                                                        {{ __('liquid') }}/{{ __('fragile') }}
                                                    </span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div> --}}
                                <div
                                    class="col-md-6 packaging-area">
                                    <div class="mb-3">
                                        <label class="form-label"
                                            for="fv-full-name">{{ __('packaging') }}</label>
                                        <select class="without_search form-select form-control packaging"
                                            name="packaging">
                                            <option value="no">{{ __('select_packing') }}</option>
                                            @foreach (settingHelper('package_and_charges') as $package_and_charge)
                                            <option value="{{ $package_and_charge->id }}"
                                                {{ $parcel->packaging == $package_and_charge->id ? 'selected' : '' }}>
                                                {{ __($package_and_charge->package_type) }}
                                                ({{ format_price($package_and_charge->charge) }})
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12 mb-2">
                                    <div class="mb-3">
                                        <div class="preview-block">
                                            <div class="custom-control custom-checkbox">
                                                <label class="custom-control-label" for="transfer_to_branch">
                                                    <input type="checkbox"
                                                        class="custom-control-input" id="transfer_to_branch" value="1"
                                                        name="transfer_to_branch" {{ (old('transfer_to_branch', !empty($parcel->destination_branch_id)) ? 'checked' : '') }}>
                                                    <span class="text-capitalize">
                                                        {{ __('transfer_to_branch') }}</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6 mb-3 px-0 {{ (old('transfer_to_branch', !empty($parcel->destination_branch_id)) ? '' : 'd-none') }}" id="transfer_branch_wrapper">
                                        <label class="form-label" for="destination_branch_id">{{ __('select_branch') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select class="without_search form-select form-control @error('destination_branch_id') is-invalid @enderror"
                                            name="destination_branch_id" id="destination_branch_id">
                                            <option value="" selected disabled>{{ __('select_branch') }}</option>
                                            @if(isset($branchs))
                                                @foreach ($branchs as $item)
                                                    <option value="{{ $item->id }}"
                                                        {{ old('destination_branch_id', $parcel->destination_branch_id) == $item->id ? 'selected' : '' }}>
                                                        {{ $item->name }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @if ($errors->has('destination_branch_id'))
                                            <div class="invalid-feedback help-block">
                                                <p>{{ $errors->first('destination_branch_id') }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <div class="preview-block">
                                            <div class="custom-control custom-checkbox">
                                                <label class="custom-control-label" for="open_box">
                                                    <input type="checkbox"
                                                        class="custom-control-input" id="open_box" value="1"
                                                        name="open_box" {{ isset($parcel) ? ($parcel->open_box == 1 ? 'checked' : '') : '' }}>
                                                    <span class="text-capitalize">
                                                        {{ __('open_box') }}</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <div class="preview-block">
                                            <div class="custom-control custom-checkbox">
                                                <label class="custom-control-label" for="home_delivery">
                                                    <input type="checkbox"
                                                        class="custom-control-input" id="home_delivery"
                                                        name="home_delivery" value="1" {{ isset($parcel) ? ($parcel->home_delivery == 1 ? 'checked' : '') : '' }}>
                                                    <span class="text-capitalize">
                                                        {{ __('home_delivery') }}</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 text-right mt-4">
                                    <div class="mb-3">
                                        <button type="submit"
                                            class="btn sg-btn-primary resubmit">{{ __('update') }}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="flex-column h-100 d-flex">
                            <div class="card bg-white redious-border p-20 p-sm-30">
                                <div class="card-title-group mb-2">
                                    <div class="card-title">
                                        <h6 class="title">{{ __('charge_details') }}</h6>
                                    </div>
                                </div>
                                <ul class="nk-top-products">
                                    <div class="card-inner p-0">
                                        <table class="table">
                                            <tr>
                                                <th><span><strong>{{ __('title') }}</strong></span>
                                                </th>
                                                <th><span><strong>{{ __('amount') }}</strong></span>
                                                </th>
                                            </tr>
                                            <tr>

                                                <td>
                                                    <span>{{ __('cash_collection') }}</span>
                                                </td>
                                                <td>
                                                    <span
                                                        id="cash-collection-charge">{{ $parcel->price }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <span>{{ __('delivery_charge') }}</span>
                                                </td>
                                                <td>
                                                    <span
                                                        id="delivery-charge">{{ $parcel->charge }}</span>
                                                </td>
                                            </tr>
                                            <tr>

                                                <td>
                                                    <span>{{ __('cod_charge') }}</span>
                                                </td>
                                                <td>
                                                    <span
                                                        id="cod-charge">{{ floor(($parcel->price / 100) * $parcel->cod_charge) }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <span>{{ __('vat') }}</span>
                                                </td>
                                                <td>
                                                    <span
                                                        id="vat-charge">{{ floor((floor($parcel->charge + $parcel->fragile_charge + $parcel->packaging_charge + ($parcel->price / 100) * $parcel->cod_charge) / 100) * $parcel->vat) }}</span>
                                                </td>
                                            </tr>
                                            {{-- <tr
                                                class="fragile-charge-area {{ $parcel->fragile == 0 ? 'd-none' : '' }}">

                                                <td>
                                                    <span>{{ __('liquid') }}/{{ __('fragile_charge') }}</span>
                                                </td>
                                                <td>
                                                    <span
                                                        id="fragile-charge">{{ $parcel->fragile_charge }}</span>
                                                </td>
                                            </tr> --}}
                                            <tr
                                                class="packaging-charge-area {{ $parcel->packaging == 'no' ? 'd-none' : '' }} ">

                                                <td>
                                                    <span>{{ __('packaging_charge') }}</span>
                                                </td>
                                                <td>
                                                    <span
                                                        id="packaging-charge">{{ $parcel->packaging_charge }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <span>{{ __('total_delivery_charge') }}</span>
                                                </td>
                                                <td>
                                                    <span
                                                        id="total-delivery-charge">{{ $parcel->total_delivery_charge }}</span>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td><span><strong>{{ __('current_payable') }}</strong></span>
                                                </td>
                                                <td>
                                                    <strong id="current-payable-charge">
                                                        <div id="currency" data-default-currency="{{ setting('default_currency') }}"></div>
                                                        {{ isset($parcel) ? (setting('default_currency') . $parcel->payable) : (setting('default_currency') . ' 0.00') }}
                                                    </strong>
                                                    <span>
                                                        <strong id="current-payable-charge">
                                                            <div id="currency" data-default-currency="{{ setting('default_currency') }}"></div>
                                                        </strong>
                                                    </span>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </ul>
                            </div>

                            <div class="card bg-white redious-border p-20 p-sm-30 mt-4">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label"
                                        for="pickup_branch_id">{{ __('pickup_branch') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('pickup_branch_id') is-invalid @enderror"
                                        id="shop_pickup_branch"
                                        value="{{ old('pickup_branch_id') ? old('pickup_branch_id') : (@$parcel->pickupBranch->name ? @$parcel->pickupBranch->name : '') }}"
                                        name="pickup_branch_id"
                                        placeholder="{{ __('pickup_branch') }}" readonly>
                                    @if ($errors->has('pickup_branch_id'))
                                    <div class="invalid-feedback help-block">
                                        <p>{{ $errors->first('pickup_branch_id') }}</p>
                                    </div>
                                    @endif
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label"
                                            for="shop_phone_number">{{ __('pickup_number') }}</label>
                                        <input type="text" class="form-control"
                                            id="shop_phone_number"
                                            value="{{ old('shop_phone_number') ? old('shop_phone_number') : (isDemoMode() ? '**************' : ($parcel->pickup_shop_phone_number ?? '')) }}"
                                            name="shop_phone_number"
                                            placeholder="{{ __('pickup_number') }}" readonly>
                                        @if ($errors->has('shop_phone_number'))
                                        <div class="invalid-feedback help-block">
                                            <p>{{ $errors->first('shop_phone_number') }}</p>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label"
                                            for="shop_address">{{ __('pickup_address') }}</label>
                                        <input type="text" class="form-control" id="shop_address"
                                            value="{{ old('shop_address') ? old('shop_address') : $parcel->pickup_address }} "
                                            name="shop_address"
                                            placeholder="{{ __('pickup_address') }}" readonly>
                                        @if ($errors->has('shop_address'))
                                        <div class="invalid-feedback help-block">
                                            <p>{{ $errors->first('shop_address') }}</p>
                                        </div>
                                        @endif
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
@include('admin.roles.script')
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
@include('live_search.merchants')