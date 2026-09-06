@extends('backend.layouts.master')
@section('parcel', 'active')
@section('title')
    {{ (@$parcel ? __('duplicate') : __('add')) . ' ' . __('parcel') }}
@endsection
@section('mainContent')
    <div class="container-fluid">
        <div class="row gx-20">
            <div class="col-lg-12">
                <div class="header-top d-flex justify-content-between align-items-center mb-12">
                    <h3 class="section-title">{{ @$parcel ? __('duplicate') : __('add') }} {{ __('parcel') }}</h3>
                    <div class="oftions-content-right">
                        <a href="{{ url()->previous() }}" class="d-flex align-items-center btn sg-btn-primary gap-2">
                            <i class="las la-arrow-left"></i>
                            <span>{{ __('back') }}</span>
                        </a>
                    </div>
                </div>
                <form action="{{ route('parcel.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card bg-white redious-border p-20 p-sm-30 pt-sm-30 flex-column h-100 d-flex">
                                <div class="row g-gs">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">{{ __('merchant') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <div>
                                                <select name="merchant"
                                                    class=" form-control select-merchant merchant merchant-live-search  @error('merchant') is-invalid @enderror"
                                                    data-url="{{ route('merchant.change') }}">
                                                    <option value="">{{ __('select_merchant') }}</option>
                                                    <option value="{{ @$parcel->merchant->id }}" {{ @$parcel->merchant->id != null ? 'selected' : '' }}>
                                                        {{ @$parcel->merchant->user->first_name . ' ' . @$parcel->merchant->user->last_name }}
                                                        ({{ @$parcel->merchant->company }})
                                                    </option>
                                                </select>
                                            </div>
                                            @error('merchant')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label" for="merchant_select">
                                                {{ __('shop') . ' (' . __('select_merchant_first') }})
                                            </label>
                                            <select
                                                class="without_search form-select form-control select-shop @error('shop') is-invalid @enderror"
                                                data-url="{{ route('admin.merchant.shop') }}" id="merchant_select"
                                                name="shop">
                                                @isset($parcel)
                                                    <option value="">{{ __('select_shop') }}</option>
                                                    @foreach (@$parcel->merchant->shops as $shop)
                                                        <option value="{{ @$shop->id }}" {{ @$parcel->shop_id == $shop->id ? 'selected' : '' }}>
                                                            {{ __(@$shop->shop_name) }}
                                                        </option>
                                                    @endforeach
                                                @endisset
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
                                            <label class="form-label" for="customer_invoice_no">{{ __('invoice') }}#
                                            </label>
                                            <input type="text"
                                                class="form-control @error('customer_invoice_no') is-invalid @enderror"
                                                value="{{ old('customer_invoice_no') != '' ? old('customer_invoice_no') : @$parcel->customer_invoice_no }}"
                                                name="customer_invoice_no" placeholder="{{ __('invoice_or_memo_no') }}">
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
                                            <label class="form-label" for="delivery_area">{{ __('delivery_area') }}
                                                <span class="text-danger">*</span></label>
                                            <select
                                                class="without_search form-select form-control parcel_type @error('parcel_type') is-invalid @enderror"
                                                id="delivery_area" name="parcel_type">
                                                <option value="" selected disabled>{{ __('select_type') }}
                                                </option>
                                                @if (settingHelper('preferences')->where('title', 'same_day')->first()->staff)
                                                    <option value="same_day" {{ old('parcel_type') == 'same_day' ? 'selected' : (@$parcel->parcel_type == 'same_day' ? 'selected' : '') }}>
                                                        {{ __('same_day') }}
                                                    </option>
                                                @endif

                                                @if (settingHelper('preferences')->where('title', 'sub_city')->first()->staff)
                                                    <option value="sub_city" {{ old('parcel_type') == 'sub_city' ? 'selected' : (@$parcel->parcel_type == 'sub_city' ? 'selected' : '') }}>
                                                        {{ __('sub_city') }}
                                                    </option>
                                                @endif
                                                @if (settingHelper('preferences')->where('title', 'sub_urban_area')->first()->staff)
                                                    <option value="outside_city" {{ old('parcel_type') == 'outside_city' ? 'selected' : (@$parcel->parcel_type == 'sub_urban_area' ? 'selected' : '') }}> {{ __('sub_urban_area') }}</option>
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
                                            <label class="form-label" for="fv-full-name">{{ __('cash_collection') }} <span
                                                    class="text-danger">*</span></label>
                                            <input type="number"
                                                class="form-control cash-collection @error('price') is-invalid @enderror"
                                                id="fv-full-name"
                                                value="{{ old('price') != '' ? old('price') : @$parcel->price }}"
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
                                            <label class="form-label" for="fv-full-name">{{ __('selling_price') }}</label>
                                            <input type="number"
                                                class="form-control @error('selling_price') is-invalid @enderror"
                                                id="fv-full-name"
                                                value="{{ old('selling_price') != '' ? old('selling_price') : @$parcel->selling_price }}"
                                                name="selling_price" placeholder="{{ __('selling_price_of_parcel') }}">
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
                                            <select
                                                class="without_search form-select form-control weight @error('weight') is-invalid @enderror"
                                                name="weight">
                                                <option value="" selected disabled>{{ __('select_weight') }}
                                                </option>
                                                @foreach ($charges as $charge)
                                                    <option value="{{ $charge->weight }}" {{ $charge->weight == old('weight') ? 'selected' : '' }}>
                                                        {{ $charge->weight }} {{ __(setting('default_weight')) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('weight'))
                                                <div class="invalid-feedback">
                                                    <p>{{ $errors->first('weight') }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label"
                                                for="customer_name">{{ __('customer') . ' ' . __('name') }}
                                                <span class="text-danger">*</span></label>
                                            <input type="text"
                                                class="form-control @error('customer_name') is-invalid @enderror"
                                                value="{{ old('customer_name') != '' ? old('customer_name') : @$parcel->customer_name }}"
                                                name="customer_name" placeholder="{{ __('recipient_name') }}">
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
                                            <input type="text"
                                                class="form-control @error('customer_phone_number') is-invalid @enderror"
                                                value="{{ old('customer_phone_number') != '' ? old('customer_phone_number') : @$parcel->customer_phone_number }}"
                                                name="customer_phone_number"
                                                placeholder="{{ __('recipient') . ' ' . __('phone') }}">
                                            @error('customer_phone_number')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label"
                                                for="customer_address">{{ __('customer') . ' ' . __('address') }}
                                                <span class="text-danger">*</span></label>
                                            <textarea class="form-control @error('customer_address') is-invalid @enderror"
                                                placeholder="{{ __('recipient') . ' ' . __('address') }}"
                                                name="customer_address">{{ old('customer_address') != '' ? old('customer_address') : @$parcel->customer_address }}</textarea>
                                            @error('customer_address')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="note">{{ __('note') }}
                                            </label>
                                            <div>
                                                <textarea class="form-control" id="note"
                                                    placeholder="{{ __('note') . ' (' . __('optional') . ')' }}"
                                                    name="note">{{ old('note') != '' ? old('note') : @$parcel->note }}</textarea>
                                            </div>
                                            @if ($errors->has('note'))
                                                <div class="invalid-feedback help-block">
                                                    <p>{{ $errors->first('note') }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-gs">
                                    <div class="col-md-6">
                                        <div class="row pt-1">
                                            <label class="form-label mt-4" for="fv-full-name"></label>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <div class="preview-block">
                                                        <div class="custom-control custom-checkbox">
                                                            <label class="custom-control-label" for="fragile">
                                                                <input type="checkbox" class="custom-control-input"
                                                                    id="fragile" name="fragile" {{ isset($parcel) ? ($parcel->fragile == 1 ? 'checked' : '') : '' }}>
                                                                <span class="text-capitalize">
                                                                    {{ __('liquid') }}/{{ __('fragile') }}</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="fv-full-name">{{ __('packaging') }}</label>
                                            <select class="without_search form-select form-control packaging"
                                                name="packaging">
                                                <option value="no">{{ __('select_packing') }}
                                                </option>
                                                @foreach (settingHelper('package_and_charges') as $package_and_charge)
                                                    <option value="{{ $package_and_charge->id }}" {{ isset($parcel) ? ($parcel->packaging == $package_and_charge->id ? 'selected' : '') : '' }}>
                                                        {{ __($package_and_charge->package_type) }}
                                                        ({{ format_price($package_and_charge->charge) }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row pt-1">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <div class="preview-block">
                                                        <div class="custom-control custom-checkbox">
                                                            <label class="custom-control-label" for="open_box">
                                                                <input type="checkbox" class="custom-control-input"
                                                                    id="open_box" value="1" name="open_box">
                                                                <span class="text-capitalize">
                                                                    {{ __('open_box') }}</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="row pt-1">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <div class="preview-block">
                                                    <div class="custom-control custom-checkbox">
                                                        <label class="custom-control-label" for="home_delivery">
                                                            <input type="checkbox" class="custom-control-input"
                                                                id="home_delivery" name="home_delivery" value="1" checked>
                                                            <span class="text-capitalize">
                                                                {{ __('home_delivery') }}</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12 text-right mt-4">
                                        <div class="mb-3">
                                            <button type="submit" class="btn sg-btn-primary">{{ __('submit') }}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="h-100 d-flex flex-column">
                                <div class="card bg-white redious-border p-20 p-sm-30 pt-sm-30">
                                    <div class="card-title-group mb-2">
                                        <div class="card-title">
                                            <h6 class="title">{{ __('charge_details') }}</h6>
                                        </div>
                                    </div>
                                    <div class="card-inner p-0">
                                        <table class="table">
                                            <tr>
                                                <th><span><strong>{{ __('title') }}</strong></span>
                                                </th>
                                                <th class="text-right">
                                                    <span><strong>{{ __('amount') }}</strong></span>
                                                </th>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <span>{{ __('cash_collection') }}</span>
                                                </td>
                                                <td class="text-right">
                                                    <span
                                                        id="cash-collection-charge">{{ isset($parcel) ? $parcel->price : '0.00' }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <span>{{ __('delivery_charge') }}</span>
                                                </td>
                                                <td class="text-right">
                                                    <span
                                                        id="delivery-charge">{{ isset($parcel) ? $parcel->charge : '0.00' }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <span>{{ __('cod_charge') }}</span>
                                                </td>
                                                <td class="text-right">
                                                    <span
                                                        id="cod-charge">{{ isset($parcel) ? format_price(($parcel->price / 100) * $parcel->cod_charge) : '0.00' }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <span>{{ __('vat') }}</span>
                                                </td>
                                                <td class="text-right">
                                                    <span
                                                        id="vat-charge">{{ isset($parcel) ? format_price((($parcel->charge + $parcel->fragile_charge + $parcel->packaging_charge + ($parcel->price / 100) * $parcel->cod_charge) / 100) * $parcel->vat) : '0.00' }}</span>
                                                </td>
                                            </tr>
                                            <tr
                                                class="fragile-charge-area {{ isset($parcel) ? ($parcel->fragile == 0 ? 'd-none' : '') : 'd-none' }}">
                                                <td>
                                                    <span>{{ __('liquid') }}/{{ __('fragile_charge') }}</span>
                                                </td>
                                                <td class="text-right">
                                                    <span
                                                        id="fragile-charge">{{ isset($parcel) ? ($parcel->fragile == 0 ? '0.00' : $parcel->fragile_charge) : '0.00' }}</span>
                                                </td>
                                            </tr>
                                            <tr
                                                class="packaging-charge-area {{ isset($parcel) ? ($parcel->packaging == 'no' ? 'd-none' : '') : 'd-none' }}">
                                                <td>
                                                    <span>{{ __('packaging_charge') }}</span>
                                                </td>
                                                <td class="text-right">
                                                    <span
                                                        id="packaging-charge">{{ isset($parcel) ? ($parcel->packaging == 'no' ? '0.00' : $parcel->packaging_charge) : '0.00' }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <span>{{ __('total_delivery_charge') }}</span>
                                                </td>
                                                <td class="text-right">
                                                    <span
                                                        id="total-delivery-charge">{{ isset($parcel) ? format_price($parcel->total_delivery_charge) : '0.00' }}</span>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td><span><strong>{{ __('current_payable') }}</strong></span>
                                                </td>
                                                <td class="text-right">
                                                    <span>
                                                        <strong id="current-payable-charge">
                                                            <div id="currency"
                                                                data-default-currency="{{ setting('default_currency') }}">
                                                            </div>
                                                            {{ isset($parcel) ? (setting('default_currency') . $parcel->payable) : (setting('default_currency') . ' 0.00') }}
                                                        </strong>
                                                    </span>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                <div class="card bg-white redious-border p-20 p-sm-30 pt-sm-30 mt-4">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label" for="pickup_branch_id">{{ __('pickup_branch') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control @error('pickup_branch_id') is-invalid @enderror"
                                            id="shop_pickup_branch"
                                            value="{{ old('pickup_branch_id') ? old('pickup_branch_id') : (@$parcel->pickupBranch->name ? @$parcel->pickupBranch->name : '') }}"
                                            name="pickup_branch_id" placeholder="{{ __('pickup_branch') }}" readonly>
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
                                            <input type="text" class="form-control" id="shop_phone_number"
                                                value="{{ old('shop_phone_number') ? old('shop_phone_number') : (@$parcel->pickup_shop_phone_number ? @$parcel->pickup_shop_phone_number : @$default_shop->shop_phone_number) }}"
                                                name="shop_phone_number" placeholder="{{ __('pickup_number') }}" readonly>
                                            @if ($errors->has('shop_phone_number'))
                                                <div class="invalid-feedback help-block">
                                                    <p>{{ $errors->first('shop_phone_number') }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label" for="shop_address">{{ __('pickup_address') }}</label>
                                            <input type="text"
                                                class="form-control @error('shop_address') is-invalid @enderror"
                                                id="shop_address"
                                                value="{{ old('shop_address') ? old('shop_address') : (@$parcel->pickup_address ? @$parcel->pickup_address : @$default_shop->address) }}"
                                                name="shop_address" placeholder="{{ __('pickup_address') }}" readonly>
                                            @error('shop_address')
                                                <div class="text-danger invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
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
@endsection
@include('live_search.merchants')