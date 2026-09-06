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
                                <div class="row g-gs">
                                    <div class="col-6 mb-3">
                                        <label class="form-label" for="customer_invoice_no">{{ __('invoice') }}#
                                        </label>
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
                                        <label class="form-label" for="area">{{ __('shop') }}
                                        </label>
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
                                    <div class="col-6 mb-3">
                                        <label class="form-label"
                                            for="customer_name">{{ __('customer') . ' ' . __('name') }}
                                            <span class="text-danger">*</span></label>
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
                                        <label class="form-label"
                                            for="customer_phone_number">{{ __('customer') . ' ' . __('phone') }}
                                            <span class="text-danger">*</span></label>
                                        <input type="number"
                                            class="form-control @error('customer_phone_number') is-invalid @enderror"
                                            id="customer_phone_number"
                                            value="{{ old('customer_phone_number') != '' ? old('customer_phone_number') : @$parcel->customer_phone_number }}"
                                            name="customer_phone_number"
                                            placeholder="{{ __('recipient') . ' ' . __('phone') }}">
                                        @if ($errors->has('customer_phone_number'))
                                            <div class="invalid-feedback help-block">
                                                <p>{{ $errors->first('customer_phone_number') }}</p>
                                            </div>
                                        @endif
                                    </div>
                                     <div class="col-6 mb-3">
                                        <label class="form-label" for="fv-full-name">{{ __('cash_collection') }}
                                            ({{ setting('default_currency') }})
                                            <span class="text-danger">*</span></label>
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
                                   {{-- <div class="col-6 mb-3">
                                        <label class="form-label" for="fv-full-name">{{ __('selling_price') }}
                                            ({{ setting('default_currency') }})</label>
                                        <input type="number" class="form-control" id="fv-full-name"
                                            value="{{ old('selling_price') != '' ? old('selling_price') : @$parcel->selling_price }}"
                                            name="selling_price" placeholder="{{ __('selling_price_of_parcel') }}">
                                        @if ($errors->has('selling_price'))
                                            <div class="invalid-feedback help-block">
                                                <p>{{ $errors->first('selling_price') }}</p>
                                            </div>
                                        @endif
                                    </div> --}}
                                    <div class="col-6 mb-3">
                                        <label class="form-label"
                                            for="customer_address">{{ __('customer') . ' ' . __('address') }}
                                            <span class="text-danger">*</span></label>
                                        <textarea class="form-control @error('customer_address') is-invalid @enderror"
                                            id="customer_address" placeholder="{{ __('recipient') . ' ' . __('address') }}"
                                            name="customer_address">{{ old('customer_address') != '' ? old('customer_address') : @$parcel->customer_address }}</textarea>
                                        @if ($errors->has('customer_address'))
                                            <div class="invalid-feedback help-block">
                                                <p>{{ $errors->first('customer_address') }}</p>
                                            </div>
                                        @endif
                                        <div id="address_auto_detect_badge" class="badge bg-soft-success text-success mt-2 p-1 px-2 d-none" style="font-size: 13px; border: 1px solid #28a745; border-radius: 4px; display: inline-block;">
                                            <i class="las la-magic me-1"></i> <span id="address_auto_detect_text"></span>
                                        </div>
                                    </div>
                                    
                                    <div class="col-6 mb-3">
                                        <label class="form-label" for="city_to_thana">{{ __('district') }} / District <span class="text-danger">*</span></label>
                                        <select
                                            class="without_search form-select form-control city_id district_id @error('city_id') is-invalid @enderror"
                                            name="city_id" id="city_to_thana">
                                            <option value="">Select District</option>
                                            @foreach($districts as $district)
                                                <option value="{{ $district->id }}"
                                                    data-name="{{ strtolower($district->name) }}"
                                                    {{ old('city_id', @$parcel->city_id) == $district->id ? 'selected' : '' }}>
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
                                    <label class="form-label" for="area">Thana
                                        <span class="text-danger">*</span></label>
                                
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
                                    
                                    <div class="col-6 mb-3">
                                        <label class="form-label" for="area">{{ __('delivery_area') }}
                                            <span class="text-danger">*</span></label>
                                        <select
                                            class="without_search form-select form-control parcel_type @error('parcel_type') is-invalid @enderror"
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
                                <label class="form-label" for="fv-full-name">{{ __('weight') }}
                                    <span class="text-danger">*</span></label>
                                <input type="number"
                                    class="form-control weight @error('weight') is-invalid @enderror"
                                    name="weight"
                                    id="fv-full-name"
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

                                   
                                    <div class="col-6 mb-3">
                                        <label class="form-label" for="note">{{ __('note') }}
                                        </label>
                                        <textarea class="form-control" id="note"
                                            placeholder="{{ __('note') . ' (' . __('parcel_note_from_merchant') . ')' }}"
                                            name="note">{{ old('note') != '' ? old('note') : @$parcel->note }}</textarea>
                                        @if ($errors->has('note'))
                                            <div class="invalid-feedback help-block">
                                                <p>{{ $errors->first('note') }}</p>
                                            </div>
                                        @endif
                                    </div>
                                    
                                     <div class="col-md-6 mb-3">
                                        <label class="form-label" for="fv-full-name">{{ __('packaging') }}</label>
                                        <select class="without_search form-select form-control packaging" name="packaging">
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
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label mt-4" for="note">
                                        </label>
                                        <div class=" mb-3">
                                            <div class="preview-block">
                                                <div class="custom-control custom-checkbox">
                                                    <label class="custom-control-label" for="fragile">
                                                        <input type="checkbox" class="custom-control-input" id="fragile"
                                                            name="fragile" {{ isset($parcel) ? ($parcel->fragile == 1 ? 'checked' : '') : '' }}>
                                                        <span class="text-capitalize">
                                                            {{ __('liquid') }}/{{ __('fragile') }}</span>
                                                    </label>
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
                                    <div class="col-md-12">
                                        <div class="row pt-1">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <div class="preview-block">
                                                        <div class="custom-control custom-checkbox">
                                                            <label class="custom-control-label" for="home_delivery">
                                                                <input type="checkbox" class="custom-control-input"
                                                                    id="home_delivery" name="home_delivery" value="1"
                                                                    checked>
                                                                <span class="text-capitalize">
                                                                    {{ __('home_delivery') }}</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12 text-right mt-4">
                                        <div class="">
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
                                            <tr
                                                class="fragile-charge-area {{ isset($parcel) ? ($parcel->fragile == 0 ? 'd-none' : '') : 'd-none' }}">

                                                <td>
                                                    <span>{{ __('liquid') }}/{{ __('fragile_charge') }}</span>
                                                </td>
                                                <td>
                                                    <span
                                                        id="fragile-charge">{{ isset($parcel) ? ($parcel->fragile == 0 ? '0.00' : $parcel->fragile_charge) : '0.00' }}</span>
                                                </td>
                                            </tr>
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
                                <div class="col-md-12 mb-3">
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

                                <div class="col-md-12 mb-3">
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

                                <div class="col-md-12 mb-3">
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

@push('script')
<script>
$(document).ready(function () {
    const DB_LOCATIONS = @json($districts);

    const DISTRICTS_MAP = [
        { name: "Dhaka", aliases: ["dhaka", "ঢাকা", "dhk"] },
        { name: "Gazipur", aliases: ["gazipur", "গাজীপুর", "joydebpur", "জয়দেবপুর"] },
        { name: "Narayanganj", aliases: ["narayanganj", "নারায়ণগঞ্জ", "নারায়নগঞ্জ", "fatullah", "ফতুল্লা", "sonargaon", "সোনারগাঁও"] },
        { name: "Tangail", aliases: ["tangail", "টাঙ্গাইল", "কালিহাতী", "মধুপুর"] },
        { name: "Faridpur", aliases: ["faridpur", "ফরিদপুর", "ভাঙ্গা", "bhanga"] },
        { name: "Gopalganj", aliases: ["gopalganj", "গোপালগঞ্জ", "টুঙ্গিপাড়া", "tungipara"] },
        { name: "Kishoreganj", aliases: ["kishoreganj", "কিশোরগঞ্জ", "bhairab", "ভৈরব"] },
        { name: "Madaripur", aliases: ["madaripur", "মাদারীপুর", "শিবচর", "shibchar"] },
        { name: "Manikganj", aliases: ["manikganj", "মানিকগঞ্জ", "সিংগাইর"] },
        { name: "Munshiganj", aliases: ["munshiganj", "মুন্সিগঞ্জ", "মুন্সীগঞ্জ", "শ্রীনগর"] },
        { name: "Narsingdi", aliases: ["narsingdi", "নরসিংদী", "রায়পুরা", "পলাশ"] },
        { name: "Rajbari", aliases: ["rajbari", "রাজবাড়ী", "রাজবাড়ি", "পাংশা"] },
        { name: "Shariatpur", aliases: ["shariatpur", "শরীয়তপুর", "শরিয়তপুর", "জাজিরা", "নড়িয়া", "naria"] },
        { name: "Chattogram", aliases: ["chattogram", "chittagong", "চট্টগ্রাম", "চিটাগাং", "ctg"] },
        { name: "Cox's Bazar", aliases: ["cox's bazar", "coxs bazar", "coxsbazar", "কক্সবাজার", "কক্স বাজার", "টেকনাফ", "teknaf"] },
        { name: "Cumilla", aliases: ["cumilla", "comilla", "কুমিল্লা"] },
        { name: "Brahmanbaria", aliases: ["brahmanbaria", "b.baria", "ব্রাহ্মণবাড়িয়া", "ব্রাহ্মনবাড়িয়া"] },
        { name: "Chandpur", aliases: ["chandpur", "চাঁদপুর", "চাদপুর", "হাজীগঞ্জ"] },
        { name: "Feni", aliases: ["feni", "ফেনী", "ফেনি"] },
        { name: "Lakshmipur", aliases: ["lakshmipur", "লক্ষ্মীপুর", "লক্ষীপুর", "রায়পুর"] },
        { name: "Noakhali", aliases: ["noakhali", "নোয়াখালী", "নোয়াখালী", "মাইজদী", "চৌমুহনী"] },
        { name: "Bandarban", aliases: ["bandarban", "বান্দরবান"] },
        { name: "Khagrachhari", aliases: ["khagrachhari", "খাগড়াছড়ি", "খাগড়াছড়ি"] },
        { name: "Rangamati", aliases: ["rangamati", "রাঙ্গামাটি", "রাঙামাটি"] },
        { name: "Rajshahi", aliases: ["rajshahi", "রাজশাহী"] },
        { name: "Bogura", aliases: ["bogura", "bogra", "বগুড়া", "বগুড়া"] },
        { name: "Joypurhat", aliases: ["joypurhat", "জয়পুরহাট", "জয়পুরহাট"] },
        { name: "Naogaon", aliases: ["naogaon", "নওগাঁ", "নওগা"] },
        { name: "Natore", aliases: ["natore", "নাটোর"] },
        { name: "Chapainawabganj", aliases: ["chapainawabganj", "chapai", "চাঁপাইনবাবগঞ্জ", "চাঁপাই"] },
        { name: "Pabna", aliases: ["pabna", "পাবনা", "ঈশ্বরদী", "ishwardi"] },
        { name: "Sirajganj", aliases: ["sirajganj", "সিরাজগঞ্জ"] },
        { name: "Khulna", aliases: ["khulna", "খুলনা"] },
        { name: "Bagerhat", aliases: ["bagerhat", "বাগেরহাট", "মংলা", "mongla"] },
        { name: "Chuadanga", aliases: ["chuadanga", "চুয়াডাঙ্গা", "চুয়াডাঙ্গা"] },
        { name: "Jashore", aliases: ["jashore", "jessore", "যশোর", "বেনাপোল", "benapole"] },
        { name: "Jhenaidah", aliases: ["jhenaidah", "ঝিনাইদহ"] },
        { name: "Kushtia", aliases: ["kushtia", "কুষ্টিয়া", "কুষ্টিয়া"] },
        { name: "Magura", aliases: ["magura", "মাগুরা"] },
        { name: "Meherpur", aliases: ["meherpur", "মেহেরপুর"] },
        { name: "Narail", aliases: ["narail", "নড়াইল", "নড়াইল"] },
        { name: "Satkhira", aliases: ["satkhira", "সাতক্ষীরা"] },
        { name: "Barishal", aliases: ["barishal", "barisal", "বরিশাল"] },
        { name: "Barguna", aliases: ["barguna", "বরগুনা"] },
        { name: "Bhola", aliases: ["bhola", "ভোলা", "বোরহানউদ্দিন", "চরফ্যাশন"] },
        { name: "Jhalokathi", aliases: ["jhalokathi", "ঝালকাঠি"] },
        { name: "Patuakhali", aliases: ["patuakhali", "পটুয়াখালী", "পটুয়াখালী", "কুয়াকাটা"] },
        { name: "Pirojpur", aliases: ["pirojpur", "পিরোজপুর"] },
        { name: "Sylhet", aliases: ["sylhet", "সিলেট"] },
        { name: "Habiganj", aliases: ["habiganj", "হবিগঞ্জ", "মাধবপুর"] },
        { name: "Moulvibazar", aliases: ["moulvibazar", "মৌলভীবাজার", "শ্রীমঙ্গল", "sreemangal"] },
        { name: "Sunamganj", aliases: ["sunamganj", "সুনামগঞ্জ", "ছাতক"] },
        { name: "Rangpur", aliases: ["rangpur", "রংপুর"] },
        { name: "Dinajpur", aliases: ["dinajpur", "দিনাজপুর"] },
        { name: "Gaibandha", aliases: ["gaibandha", "গাইবান্ধা"] },
        { name: "Kurigram", aliases: ["kurigram", "কুড়িগ্রাম", "কুড়িগ্রাম"] },
        { name: "Lalmonirhat", aliases: ["lalmonirhat", "লালমনিরহাট"] },
        { name: "Nilphamari", aliases: ["nilphamari", "নীলফামারী", "সৈয়দপুর", "saidpur"] },
        { name: "Panchagarh", aliases: ["panchagarh", "পঞ্চগড়", "পঞ্চগড়", "তেঁতুলিয়া"] },
        { name: "Thakurgaon", aliases: ["thakurgaon", "ঠাকুরগাঁও", "ঠাকুরগাও"] },
        { name: "Mymensingh", aliases: ["mymensingh", "ময়মনসিংহ", "ময়মনসিংহ"] },
        { name: "Jamalpur", aliases: ["jamalpur", "জামালপুর"] },
        { name: "Netrokona", aliases: ["netrokona", "নেত্রকোণা", "নেত্রকোনা"] },
        { name: "Sherpur", aliases: ["sherpur", "শেরপুর"] }
    ];

    const FAMOUS_THANAS = [
        { name: "Banani", aliases: ["banani", "বনানী"], district: "Dhaka" },
        { name: "Gulshan", aliases: ["gulshan", "গুলশান"], district: "Dhaka" },
        { name: "Mirpur", aliases: ["mirpur", "মিরপুর"], district: "Dhaka" },
        { name: "Dhanmondi", aliases: ["dhanmondi", "ধানমন্ডি", "ধানমণ্ডি"], district: "Dhaka" },
        { name: "Uttara", aliases: ["uttara", "উত্তরা"], district: "Dhaka" },
        { name: "Mohammadpur", aliases: ["mohammadpur", "মোহাম্মদপুর"], district: "Dhaka" },
        { name: "Badda", aliases: ["badda", "বাড্ডা"], district: "Dhaka" },
        { name: "Rampura", aliases: ["rampura", "রামপুরা"], district: "Dhaka" },
        { name: "Khilgaon", aliases: ["khilgaon", "খিলগাঁও", "খিলগাও"], district: "Dhaka" },
        { name: "Motijheel", aliases: ["motijheel", "মতিঝিল"], district: "Dhaka" },
        { name: "Jatrabari", aliases: ["jatrabari", "যাত্রাবাড়ী", "যাত্রাবাড়ি"], district: "Dhaka" },
        { name: "Demra", aliases: ["demra", "ডেমরা"], district: "Dhaka" },
        { name: "Lalbagh", aliases: ["lalbagh", "লালবাগ"], district: "Dhaka" },
        { name: "New Market", aliases: ["new market", "নিউ মার্কেট", "নিউমার্কেট"], district: "Dhaka" },
        { name: "Shahbagh", aliases: ["shahbagh", "শাহবাগ"], district: "Dhaka" },
        { name: "Paltan", aliases: ["paltan", "পল্টন"], district: "Dhaka" },
        { name: "Ramna", aliases: ["ramna", "রমনা"], district: "Dhaka" },
        { name: "Tejgaon", aliases: ["tejgaon", "তেজগাঁও", "তেজগাও"], district: "Dhaka" },
        { name: "Mohakhali", aliases: ["mohakhali", "মহাখালী"], district: "Dhaka" },
        { name: "Banasree", aliases: ["banasree", "বনশ্রী"], district: "Dhaka" },
        { name: "Basabo", aliases: ["basabo", "বাসাবো"], district: "Dhaka" },
        { name: "Bashundhara", aliases: ["bashundhara", "বসুন্ধরা"], district: "Dhaka" },
        { name: "Malibagh", aliases: ["malibagh", "মালিবাগ"], district: "Dhaka" },
        { name: "Moghbazar", aliases: ["moghbazar", "mogbazar", "মগবাজার"], district: "Dhaka" },
        { name: "Shantinagar", aliases: ["shantinagar", "শান্তিনগর"], district: "Dhaka" },
        { name: "Panthapath", aliases: ["panthapath", "পান্থপথ"], district: "Dhaka" },
        { name: "Elephant Road", aliases: ["elephant road", "এলিফ্যান্ট রোড"], district: "Dhaka" },
        { name: "Farmgate", aliases: ["farmgate", "ফার্মগেট"], district: "Dhaka" },
        { name: "Wari", aliases: ["wari", "ওয়ারী", "ওয়ারী"], district: "Dhaka" },
        { name: "Adabor", aliases: ["adabor", "আদাবর"], district: "Dhaka" },
        { name: "Kafrul", aliases: ["kafrul", "কাফরুল"], district: "Dhaka" },
        { name: "Kalabagan", aliases: ["kalabagan", "কলাবাগান"], district: "Dhaka" },
        { name: "Savar", aliases: ["savar", "সাভার"], district: "Dhaka", is_sub_city: true },
        { name: "Keraniganj", aliases: ["keraniganj", "কেরানীগঞ্জ", "কেরানিগঞ্জ"], district: "Dhaka", is_sub_city: true },
        { name: "Dhamrai", aliases: ["dhamrai", "ধামরাই"], district: "Dhaka", is_sub_city: true },
        { name: "Tongi", aliases: ["tongi", "টঙ্গী", "টঙ্গি"], district: "Gazipur", is_sub_city: true },
        { name: "Panchlaish", aliases: ["panchlaish", "পাঁচলাইশ"], district: "Chattogram" },
        { name: "Halishahar", aliases: ["halishahar", "হালিশহর"], district: "Chattogram" },
        { name: "Agrabad", aliases: ["agrabad", "আগ্রাবাদ"], district: "Chattogram" },
        { name: "Chandgaon", aliases: ["chandgaon", "চান্দগাঁও"], district: "Chattogram" },
        { name: "Kotwali", aliases: ["kotwali", "কোতোয়ালী", "কোতোয়ালী"] }
    ];

    let parseTimeout = null;
    let pendingThanaName = null;

    // Listen for thanas loaded from charge-script's AJAX
    $('#thana_to_area').on('thanas_loaded', function () {
        if (pendingThanaName) {
            selectMatchingThana(pendingThanaName);
            pendingThanaName = null;
        } else {
            // Fallback: check if any option in #thana_to_area exists in address
            const addr = $('#customer_address').val().trim().toLowerCase();
            $('#thana_to_area option').each(function () {
                const val = $(this).val();
                const text = $(this).text().trim().toLowerCase();
                if (val && text && text !== 'select thana') {
                    if (addr.includes(text)) {
                        selectMatchingThana(text);
                        return false;
                    }
                }
            });
        }
    });

    $('#customer_address').on('input paste keyup', function () {
        clearTimeout(parseTimeout);
        parseTimeout = setTimeout(detectLocationFromAddress, 300);
    });

    function findDistrictOption(districtNameOrId) {
        if (!districtNameOrId) return null;
        let matched = null;
        const target = districtNameOrId.toString().trim().toLowerCase();
        $('#city_to_thana option').each(function () {
            const val = $(this).val();
            if (!val) return;
            const text = $(this).text().trim().toLowerCase();
            const dataName = ($(this).data('name') || '').toString().toLowerCase();
            if (val.toString() === target || text === target || dataName === target) {
                matched = { id: val, name: $(this).text().trim() };
                return false;
            }
        });
        return matched;
    }

    function selectMatchingThana(thanaNameOrId) {
        if (!thanaNameOrId) return;
        const target = thanaNameOrId.toString().trim().toLowerCase();
        let matchedVal = null;
        let matchedText = '';

        // 1. Match by Option Value
        if ($('#thana_to_area option[value="' + thanaNameOrId + '"]').length) {
            matchedVal = thanaNameOrId;
            matchedText = $('#thana_to_area option[value="' + thanaNameOrId + '"]').text().trim();
        }

        // 2. Exact match by Text
        if (!matchedVal) {
            $('#thana_to_area option').each(function () {
                const val = $(this).val();
                if (!val) return;
                const text = $(this).text().trim().toLowerCase();
                if (text === target) {
                    matchedVal = val;
                    matchedText = $(this).text().trim();
                    return false;
                }
            });
        }

        // 3. Substring match by Text
        if (!matchedVal) {
            $('#thana_to_area option').each(function () {
                const val = $(this).val();
                if (!val) return;
                const text = $(this).text().trim().toLowerCase();
                if (text.includes(target) || target.includes(text)) {
                    matchedVal = val;
                    matchedText = $(this).text().trim();
                    return false;
                }
            });
        }

        if (matchedVal) {
            $('#thana_to_area').val(matchedVal).trigger('change');

            // Update badge with confirmed thana name
            const distText = $('#city_to_thana option:selected').text().trim();
            if (distText && distText !== 'Select District') {
                $('#address_auto_detect_text').text('📍 সনাক্ত হয়েছে: ' + distText + ' > ' + matchedText);
                $('#address_auto_detect_badge').removeClass('d-none');
            }
        }
    }

    function detectLocationFromAddress() {
        const address = $('#customer_address').val().trim().toLowerCase();
        if (address.length < 3) {
            $('#address_auto_detect_badge').addClass('d-none');
            return;
        }

        let detectedDistrict = null;
        let detectedThana = null;

        // 1. Check famous thanas first (with custom aliases / sub_city flags)
        for (const t of FAMOUS_THANAS) {
            for (const alias of t.aliases) {
                if (address.includes(alias)) {
                    detectedThana = { name: t.name, is_sub_city: t.is_sub_city };
                    if (t.district) {
                        detectedDistrict = DB_LOCATIONS.find(d => d.name.toLowerCase() === t.district.toLowerCase());
                    }
                    break;
                }
            }
            if (detectedThana) break;
        }

        // 2. Check 64 districts map (Bangla & English aliases)
        if (!detectedDistrict) {
            for (const d of DISTRICTS_MAP) {
                for (const alias of d.aliases) {
                    if (address.includes(alias)) {
                        detectedDistrict = DB_LOCATIONS.find(item => item.name.toLowerCase() === d.name.toLowerCase());
                        break;
                    }
                }
                if (detectedDistrict) break;
            }
        }

        // 3. Search ALL 64 districts' thanas in DB_LOCATIONS
        if (!detectedThana) {
            // If district is already found, check this district's thanas first
            if (detectedDistrict && detectedDistrict.thanas) {
                for (const th of detectedDistrict.thanas) {
                    const thName = th.name.toLowerCase();
                    if (address.includes(thName)) {
                        detectedThana = th;
                        break;
                    }
                    const cleaned = thName.replace(/\s+sadar$/, '').trim();
                    if (cleaned.length > 2 && address.includes(cleaned)) {
                        detectedThana = th;
                        break;
                    }
                }
            }

            // If thana not yet found, scan across ALL districts
            if (!detectedThana) {
                outerDistrictLoop:
                for (const dist of DB_LOCATIONS) {
                    if (!dist.thanas) continue;
                    for (const th of dist.thanas) {
                        const thName = th.name.toLowerCase();
                        if (thName === 'sadar' || thName.length < 3) continue;

                        if (address.includes(thName)) {
                            detectedThana = th;
                            if (!detectedDistrict) {
                                detectedDistrict = dist;
                            }
                            break outerDistrictLoop;
                        }
                    }
                }
            }
        }

        // 4. If district was detected, auto-select in #city_to_thana
        if (detectedDistrict) {
            const matchedOption = findDistrictOption(detectedDistrict.id || detectedDistrict.name);
            if (matchedOption) {
                const currentVal = $('#city_to_thana').val();
                const thanaToSelect = detectedThana ? (detectedThana.id || detectedThana.name) : null;

                if (currentVal != matchedOption.id) {
                    pendingThanaName = thanaToSelect;
                    // Trigger change to update Select2 and request thanabook AJAX
                    $('#city_to_thana').val(matchedOption.id).trigger('change');
                } else if (thanaToSelect) {
                    selectMatchingThana(thanaToSelect);
                }

                // 5. Auto-update delivery area (parcel_type)
                if (detectedThana && detectedThana.is_sub_city) {
                    $('select[name="parcel_type"]').val('sub_city').trigger('change');
                } else if (matchedOption.name.toLowerCase() === 'dhaka') {
                    const currentType = $('select[name="parcel_type"]').val();
                    if (!currentType || currentType === 'outside_city') {
                        $('select[name="parcel_type"]').val('same_day').trigger('change');
                    }
                } else if (matchedOption.name.toLowerCase() === 'gazipur' || matchedOption.name.toLowerCase() === 'narayanganj') {
                    $('select[name="parcel_type"]').val('sub_city').trigger('change');
                } else {
                    $('select[name="parcel_type"]').val('outside_city').trigger('change');
                }

                // 6. Visual Badge Feedback
                let badgeText = '📍 সনাক্ত হয়েছে: ' + matchedOption.name;
                if (detectedThana && detectedThana.name) {
                    badgeText += ' > ' + detectedThana.name;
                }
                $('#address_auto_detect_text').text(badgeText);
                $('#address_auto_detect_badge').removeClass('d-none');
            }
        }
    }
});
</script>
@endpush

@endsection