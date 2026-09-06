@extends('backend.layouts.master')

@section('title')
    {{ (@$parcel ? __('duplicate') : __('add')) . ' ' . __('parcel') }}
@endsection

@section('mainContent')
    <div class="container-fluid">
        <div class="row gx-20">
            <div class="col-lg-12">
                <div class="header-top d-flex justify-content-between align-items-center">
                    <div class="">
                        <h3 class="section-title">{{ @$parcel ? __('duplicate') : __('add') }}{{ __('parcel') }}</h3>
                    </div>
                    <div class="oftions-content-right">
                        <a href="{{ url()->previous() }}" class="btn sg-btn-primary d-md-inline-flex"><i
                                class="icon las la-arrow-left"></i><span>{{ __('back') }}</span></a>
                    </div>
                </div>
                <form action="{{ route('merchant.parcel.store') }}" class="form-validate" method="POST"
                    enctype="multipart/form-data">
                    <input type="hidden" value="{{ \Sentinel::getUser()->merchant->id }}" name="merchant" class="merchant">
                    @csrf
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card bg-white redious-border p-20 p-sm-30 pt-sm-30">
                                <div class="row g-gs">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label"
                                                for="customer_invoice_no">{{ __('invoice') }}# <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="customer_invoice_no"
                                                value="{{ old('customer_invoice_no') != '' ? old('customer_invoice_no') : @$parcel->customer_invoice_no }}"
                                                name="customer_invoice_no"
                                                placeholder="{{ __('invoice_or_memo_no') }}" required>
                                            @if ($errors->has('customer_invoice_no'))
                                                <div class="invalid-feedback help-block">
                                                    <p>{{ $errors->first('customer_invoice_no') }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="fv-full-name">{{ __('weight') }}
                                                <span class="text-danger">*</span></label>
                                            <select class="form-select form-control form-control-lg weight"
                                                name="weight">
                                                <option value="">{{ __('select_weight') }}</option>
                                                @foreach ($charges as $charge)
                                                    <option value="{{ $charge->weight }}"
                                                        {{ $charge->weight == @$parcel->weight ? 'selected' : '' }}>
                                                        {{ $charge->weight }}{{ ' ' . __(setting('default_weight')) }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('weight'))
                                                <div class="invalid-feedback help-block">
                                                    <p>{{ $errors->first('weight') }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label"
                                                for="fv-full-name">{{ __('cash_collection') }}
                                                <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control cash-collection"
                                                id="fv-full-name"
                                                value="{{ old('price') != '' ? old('price') : @$parcel->price }}"
                                                name="price"
                                                placeholder="{{ __('cash_amount_including_delivery_charge') }}"
                                                required>
                                            @if ($errors->has('price'))
                                                <div class="invalid-feedback help-block">
                                                    <p>{{ $errors->first('price') }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>


                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label"
                                                for="fv-full-name">{{ __('selling_price') }}</label>
                                            <input type="text" class="form-control" id="fv-full-name"
                                                value="{{ old('selling_price') != '' ? old('selling_price') : @$parcel->selling_price }}"
                                                name="selling_price"
                                                placeholder="{{ __('selling_price_of_parcel') }}">
                                            @if ($errors->has('selling_price'))
                                                <div class="invalid-feedback help-block">
                                                    <p>{{ $errors->first('selling_price') }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="area">{{ __('delivery_area') }}
                                                <span class="text-danger">*</span></label>
                                            <select class="form-select form-control form-control-lg parcel_type"
                                                name="parcel_type" required>
                                                <option value="">{{ __('select_type') }}</option>
                                                @if (settingHelper('preferences')->where('title', 'same_day')->first()->merchant)
                                                    <option value="same_day"
                                                        {{ old('parcel_type') == 'same_day' ? 'selected' : (@$parcel->parcel_type == 'same_day' ? 'selected' : '') }}>
                                                        {{ __('same_day') }}</option>
                                                @endif
                                                <!-- @if (settingHelper('preferences')->where('title', 'next_day')->first()->merchant)
                                                    <option value="next_day"
                                                        {{ old('parcel_type') == 'next_day' ? 'selected' : (@$parcel->parcel_type == 'next_day' ? 'selected' : '') }}>
                                                        {{ __('next_day') }}</option>
                                                @endif -->
                                                @if (settingHelper('preferences')->where('title', 'sub_city')->first()->merchant)
                                                    <option value="sub_city"
                                                        {{ old('parcel_type') == 'sub_city' ? 'selected' : (@$parcel->parcel_type == 'sub_city' ? 'selected' : '') }}>
                                                        {{ __('sub_city') }}</option>
                                                @endif
                                                @if (settingHelper('preferences')->where('title', 'sub_urban_area')->first()->merchant)
                                                    <option value="sub_urban_area"
                                                        {{ old('parcel_type') == 'sub_urban_area' ? 'selected' : (@$parcel->parcel_type == 'sub_urban_area' ? 'selected' : '') }}>
                                                        {{ __('sub_urban_area') }}</option>
                                                @endif
                                            </select>
                                            @if ($errors->has('parcel_type'))
                                                <div class="invalid-feedback help-block">
                                                    <p>{{ __('delivery_area_required') }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="area">{{ __('shop') }}
                                            </label>
                                            <select class="form-select form-control form-control-lg select-shop"
                                                data-url="{{ route('merchant.shop') }}" name="shop">
                                                <option value="">{{ __('select_shop') }}</option>
                                                @foreach ($shops as $shop)
                                                    <option value="{{ $shop->id }}">
                                                        {{ __($shop->shop_name) }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('shop'))
                                                <div class="invalid-feedback help-block">
                                                    <p>{{ $errors->first('shop') }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label"
                                            for="pickup_branch_id">{{ __('pickup_branch') }}</label>
                                            <input type="text" class="form-control @error('pickup_branch_id') is-invalid @enderror"
                                                id="shop_pickup_branch"
                                                value="{{ old('pickup_branch_id') ? old('pickup_branch_id') : (@$parcel->branch->name ? @$parcel->branch->name  : '' ) }}"
                                                name="pickup_branch_id"
                                                placeholder="{{ __('pickup_branch') }}" readonly>
                                        @if ($errors->has('pickup_branch_id'))
                                            <div class="invalid-feedback help-block">
                                                <p>{{ $errors->first('pickup_branch_id') }}</p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label"
                                                for="shop_phone_number">{{ __('pickup_number') }}</label>
                                            <input type="text" class="form-control" id="shop_phone_number"
                                                value="{{ old('shop_phone_number') ? old('shop_phone_number') : (@$parcel->pickup_shop_phone_number ? @$parcel->pickup_shop_phone_number : '') }}"
                                                name="shop_phone_number"
                                                placeholder="{{ __('pickup_number') }}">
                                            @if ($errors->has('shop_phone_number'))
                                                <div class="invalid-feedback help-block">
                                                    <p>{{ $errors->first('shop_phone_number') }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label"
                                                for="shop_address">{{ __('pickup_address') }}</label>
                                            <input type="text" class="form-control" id="shop_address"
                                                value="{{ old('shop_address') ? old('shop_address') : (@$parcel->pickup_address ? @$parcel->pickup_address : '') }}"
                                                name="shop_address" placeholder="{{ __('pickup_address') }}">
                                            @if ($errors->has('shop_address'))
                                                <div class="invalid-feedback help-block">
                                                    <p>{{ $errors->first('shop_address') }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label"
                                                for="customer_name">{{ __('customer') . ' ' . __('name') }}
                                                <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="customer_name"
                                                value="{{ old('customer_name') != '' ? old('customer_name') : @$parcel->customer_name }}"
                                                name="customer_name"
                                                placeholder="{{ __('recipient' . ' ' . __('name')) }}"
                                                required>
                                            @if ($errors->has('customer_name'))
                                                <div class="invalid-feedback help-block">
                                                    <p>{{ $errors->first('customer_name') }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label"
                                                for="customer_phone_number">{{ __('customer') . ' ' . __('phone') }}
                                                <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control"
                                                id="customer_phone_number"
                                                value="{{ old('customer_phone_number') != '' ? old('customer_phone_number') : @$parcel->customer_phone_number }}"
                                                name="customer_phone_number"
                                                placeholder="{{ __('recipient') . ' ' . __('phone') }}"
                                                required>
                                            @if ($errors->has('customer_phone_number'))
                                                <div class="invalid-feedback help-block">
                                                    <p>{{ $errors->first('customer_phone_number') }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label"
                                                for="customer_address">{{ __('customer') . ' ' . __('address') }}
                                                <span class="text-danger">*</span></label>
                                            <textarea class="form-control" id="customer_address" placeholder="{{ __('recipient') . ' ' . __('address') }}"
                                                required name="customer_address">{{ old('customer_address') != '' ? old('customer_address') : @$parcel->customer_address }}</textarea>
                                            @if ($errors->has('customer_address'))
                                                <div class="invalid-feedback help-block">
                                                    <p>{{ $errors->first('customer_address') }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="note">{{ __('note') }}
                                            </label>
                                            <textarea class="form-control" id="note"
                                                placeholder="{{ __('note') . ' (' . __('parcel_note_from_merchant') . ')' }}" name="note">{{ old('note') != '' ? old('note') : @$parcel->note }}</textarea>
                                            @if ($errors->has('note'))
                                                <div class="invalid-feedback help-block">
                                                    <p>{{ $errors->first('note') }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="">
                                            <label class="form-label"
                                                for="fv-full-name">{{ __('choose_which_needed_for_parcel') }}</label>
                                        </div>
                                        <div class="row pt-1">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <div class="preview-block">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox"
                                                                class="custom-control-input" id="fragile"
                                                                name="fragile"
                                                                {{ isset($parcel) ? ($parcel->fragile == 1 ? 'checked' : '') : '' }}>
                                                            <label class="custom-control-label"
                                                                for="fragile">{{ __('liquid') }}/{{ __('fragile') }}</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="col-md-6 packaging-area  {{ isset($parcel) ? ($parcel->fragile == 0 ? 'd-none' : '') : 'd-none' }}">
                                        <div class="mb-3">
                                            <label class="form-label"
                                                for="fv-full-name">{{ __('packaging') }}</label>
                                            <select class="form-select form-control form-control-lg packaging"
                                                name="packaging">
                                                <option value="no">{{ __('select_packing') }}</option>
                                                @foreach (settingHelper('package_and_charges') as $package_and_charge)
                                                    <option value="{{ $package_and_charge->id }}"
                                                        {{ isset($parcel) ? ($parcel->packaging == $package_and_charge->id ? 'selected' : '') : '' }}>
                                                        {{ __($package_and_charge->package_type) }}
                                                        ({{format_price($package_and_charge->charge) }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="col-md-12 text-right mt-4">
                                        <div class="mb-3">
                                            <button type="submit"
                                                class="btn sg-btn-primary resubmit">{{ __('submit') }}</button>
                                        </div>
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
                                        <div class="nk-tb-list nk-tb-ulist">
                                            <div class="nk-tb-item nk-tb-head">

                                                <div class="nk-tb-col"><span
                                                        class="sub-text"><strong>{{ __('title') }}</strong></span>
                                                </div>
                                                <div class="nk-tb-col"><span
                                                        class="sub-text"><strong>{{ __('amount') }}({{ setting('default_currency') }})</strong></span>
                                                </div>
                                            </div>
                                            <div class="nk-tb-item">

                                                <div class="nk-tb-col">
                                                    <span>{{ __('cash_collection') }}</span>
                                                </div>
                                                <div class="nk-tb-col">
                                                    <span
                                                        id="cash-collection-charge">{{ isset($parcel) ? $parcel->price : '0.00' }}</span>
                                                </div>
                                            </div>
                                            <div class="nk-tb-item">

                                                <div class="nk-tb-col">
                                                    <span>{{ __('delivery_charge') }}</span>
                                                </div>
                                                <div class="nk-tb-col">
                                                    <span
                                                        id="delivery-charge">{{ isset($parcel) ? $parcel->charge : '0.00' }}</span>
                                                </div>
                                            </div>
                                            <div class="nk-tb-item">

                                                <div class="nk-tb-col">
                                                    <span>{{ __('cod_charge') }}</span>
                                                </div>
                                                <div class="nk-tb-col">
                                                    <span
                                                        id="cod-charge">{{ isset($parcel) ? format_price(($parcel->price / 100) * $parcel->cod_charge) : '0.00' }}</span>
                                                </div>
                                            </div>
                                            <div class="nk-tb-item">

                                                <div class="nk-tb-col">
                                                    <span>{{ __('vat') }}</span>
                                                </div>
                                                <div class="nk-tb-col">
                                                    <span
                                                        id="vat-charge">{{ isset($parcel) ? format_price((($parcel->charge + $parcel->fragile_charge + $parcel->packaging_charge + ($parcel->price / 100) * $parcel->cod_charge) / 100) * $parcel->vat) : '0.00' }}</span>
                                                </div>
                                            </div>
                                            <div
                                                class="nk-tb-item fragile-charge-area {{ isset($parcel) ? ($parcel->fragile == 0 ? 'd-none' : '') : 'd-none' }}">

                                                <div class="nk-tb-col">
                                                    <span>{{ __('liquid') }}/{{ __('fragile_charge') }}</span>
                                                </div>
                                                <div class="nk-tb-col">
                                                    <span
                                                        id="fragile-charge">{{ isset($parcel) ? ($parcel->fragile == 0 ? '0.00' : $parcel->fragile_charge) : '0.00' }}</span>
                                                </div>
                                            </div>
                                            <div
                                                class="nk-tb-item packaging-charge-area {{ isset($parcel) ? ($parcel->packaging == 'no' ? 'd-none' : '') : 'd-none' }}">

                                                <div class="nk-tb-col">
                                                    <span>{{ __('packaging_charge') }}</span>
                                                </div>
                                                <div class="nk-tb-col">
                                                    <span
                                                        id="packaging-charge">{{ isset($parcel) ? ($parcel->packaging == 'no' ? '0.00' : $parcel->packaging_charge) : '0.00' }}</span>
                                                </div>
                                            </div>
                                            <div class="nk-tb-item">

                                                <div class="nk-tb-col">
                                                    <span>{{ __('total_delivery_charge') }}</span>
                                                </div>
                                                <div class="nk-tb-col">
                                                    <span
                                                        id="total-delivery-charge">{{ isset($parcel) ? format_price($parcel->total_delivery_charge) : '0.00' }}</span>
                                                </div>
                                            </div>

                                            <div class="nk-tb-item nk-tb-head">
                                                <div class="nk-tb-col"><span
                                                        class="sub-text"><strong>{{ __('current_payable') }}</strong></span>
                                                </div>
                                                <div class="nk-tb-col"><span class="sub-text"><strong
                                                            id="current-payable-charge">{{ isset($parcel) ? $parcel->payable : '0.00' }}</strong></span>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </ul>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @include('admin.parcel.charge-script')
@endsection
