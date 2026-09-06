@extends('backend.layouts.master')

@section('title')
    {{ __('edit') . ' ' . __('merchant') }}
@endsection

@section('mainContent')
    <div class="container-fluid">
        <div class="row gx-20">
            <div class="col-lg-12">
                <div class="header-top d-flex justify-content-between align-items-center mb-12">
                    <h3 class="section-title">{{ __('edit') }} {{ __('merchant') }}</h3>
                    <div class="oftions-content-right">
                    </div>
                </div>
                <div class="bg-white redious-border p-20 p-sm-30">
                    <form action="{{ route('merchant.update') }}" class="form-validate" method="POST"
                        enctype="multipart/form-data">
                        <input type="hidden" value="{{ $user->id }}" name="id">
                        <input type="hidden" value="{{ $merchant->id }}" name="merchant_id">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card-inner">
                                    <div class="row g-gs">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label" for="fv-full-name">{{ __('first_name') }} <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="fv-full-name"
                                                    name="first_name"
                                                    value="{{ old('first_name') != '' ? old('first_name') : $user->first_name }}"
                                                    >
                                                @if ($errors->has('first_name'))
                                                    <div class="invalid-feedback help-block">
                                                        <p>{{ $errors->first('first_name') }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label" for="fv-last_name">{{ __('last_name') }} <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="fv-last_name"
                                                    value="{{ old('last_name') != '' ? old('last_name') : $user->last_name }}"
                                                    name="last_name">
                                                @if ($errors->has('last_name'))
                                                    <div class="invalid-feedback help-block">
                                                        <p>{{ $errors->first('last_name') }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label" for="fv-email">{{ __('email') }} <span
                                                        class="text-danger">*</span></label>
                                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="fv-email" name="email"
                                                       value="{{ old('email') != '' ? old('email') : (isDemoMode() ? '**************' : ($user->email ?? '')) }}"
                                                    >
                                                @if ($errors->has('email'))
                                                    <div class="invalid-feedback help-block">
                                                        <p>{{ $errors->first('email') }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label" for="fv-email">{{ __('password') }}
                                                    <span class="text-danger">*</span></label>
                                                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="fv-email"
                                                        name="password" value="{{ old('password') }}">
                                                @if ($errors->has('password'))
                                                    <div class="invalid-feedback help-block">
                                                        <p>{{ $errors->first('password') }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label" for="company">{{ __('company_name') }} <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('company') is-invalid @enderror" id="company"
                                                    value="{{ old('company') != '' ? old('company') : $merchant->company }}"
                                                    name="company" >
                                                @if ($errors->has('company'))
                                                    <div class="invalid-feedback help-block">
                                                        <p>{{ $errors->first('company') }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label" for="phone_number">{{ __('phone_number') }} <span
                                                        class="text-danger">*</span></label>
                                                <input type="number" class="form-control @error('phone_number') is-invalid @enderror" id="phone_number"
                                                    value="{{ old('phone_number') != '' ? old('phone_number') : (isDemoMode() ? '**************' : ( $merchant->phone_number ?? '')) }}"
                                                    name="phone_number" >
                                                @if ($errors->has('phone_number'))
                                                    <div class="invalid-feedback help-block">
                                                        <p>{{ $errors->first('phone_number') }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label" for="website">{{ __('website') }}
                                                    @if (!blank($merchant->website))
                                                        <a href="{{ $merchant->website }}" target="_blank"> <i
                                                                class="icon  las la-external-link-alt"></i></a>
                                                    @endif
                                                </label>
                                                    <input type="text" class="form-control" id="website"
                                                        value="{{ old('website') != '' ? old('website') : $merchant->website }}"
                                                        name="website">
                                                @if ($errors->has('website'))
                                                    <div class="invalid-feedback help-block">
                                                        <p>{{ $errors->first('website') }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label" for="city">{{ __('city') }}</label>
                                                <input type="text" class="form-control" id="city"
                                                    value="{{ old('city') != '' ? old('city') : $merchant->city }}"
                                                    name="city">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label" for="zip">{{ __('zip') }}</label>
                                                <input type="text" class="form-control" id="zip"
                                                    value="{{ old('zip') != '' ? old('zip') : $merchant->zip }}"
                                                    name="zip">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label"
                                                    for="billing_street">{{ __('billing') . ' ' . __('street') }}</label>
                                                <input type="text" class="form-control" id="billing_street"
                                                    value="{{ old('billing_street') != '' ? old('billing_street') : $merchant->billing_street }}"
                                                    name="billing_street">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label"
                                                    for="billing_city">{{ __('billing') . ' ' . __('city') }}</label>
                                                <input type="text" class="form-control" id="billing_city"
                                                    value="{{ old('billing_city') != '' ? old('billing_city') : $merchant->billing_city }}"
                                                    name="billing_city">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label"
                                                    for="billing_zip">{{ __('billing') . ' ' . __('zip') }}</label>
                                                <input type="text" class="form-control" id="billing_zip"
                                                    value="{{ old('billing_zip') != '' ? old('billing_zip') : $merchant->billing_zip }}"
                                                    name="billing_zip">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label" for="address">{{ __('address') }}</label>
                                                <input type="text" class="form-control" id="address"
                                                    value="{{ old('address') != '' ? old('address') : $merchant->address }}"
                                                    name="address">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label" for="vat">{{ __('vat') }} (%)</label>
                                                    <input type="text" class="form-control" id="vat"
                                                        value="{{ old('vat') != '' ? old('vat') : $merchant->vat }}"
                                                        name="vat">
                                            </div>
                                            @if ($errors->has('vat'))
                                                <div class="invalid-feedback help-block">
                                                    <p>{{ $errors->first('vat') }}</p>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label"
                                                    for="opening_balance">{{ __('opening_balance') }} ({{ setting('default_currency') }})<span class="text-danger">*</span> <span
                                                        class="text-warning">{{ @$merchant->merchantAccount->payment_withdraw_id != null || @$merchant->merchantAccount->is_paid == true ? __('adjusted_in_payment') : '' }}</span></label>
                                                    <input type="number" class="form-control" id="opening_balance"
                                                        value="{{ old('opening_balance') != '' ? old('opening_balance') : @$merchant->merchantAccount->amount }}"
                                                        name="opening_balance"
                                                        {{ @$merchant->merchantAccount->payment_withdraw_id != null || @$merchant->merchantAccount->is_paid == true ? 'readonly' : '' }}>
                                                @if ($errors->has('opening_balance'))
                                                    <div class="invalid-feedback help-block">
                                                        <p>{{ $errors->first('opening_balance') }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-lg-4 input_file_div">
                                            <div class="mb-3 mt-2">
                                                <label class="form-label mb-1">{{ __('trade_license') }}</label>
                                                <input class="form-control sp_file_input file_picker" type="file" id="trade_license"
                                                    name="trade_license" accept="image/*">
                                                    @if ($errors->has('trade_license'))
                                                    <div class="invalid-feedback help-block">
                                                        <p>{{ $errors->first('trade_license') }}</p>
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="selected-files d-flex flex-wrap gap-20">
                                                <div class="selected-files-item">
                                                    <img class="selected-img" src="{{ getFileLink('80X80', $merchant->trade_license) }}" alt="favicon">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 input_file_div">
                                            <div class="mb-3 mt-2">
                                                <label class="form-label mb-1">{{ __('nid') }}</label>
                                                <input class="form-control sp_file_input file_picker" type="file" id="nid"
                                                    name="nid" accept="image/*">
                                                    @if ($errors->has('nid'))
                                                    <div class="invalid-feedback help-block">
                                                        <p>{{ $errors->first('nid') }}</p>
                                                    </div>
                                                @endif
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

                                        <div class="col-lg-4 input_file_div">
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
                                                     src="{{ getFileLink('80X80', $merchant->user->image_id) }}"
                                                     alt="favicon">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-5">
                                        <div class="col-md-12 mb-3">
                                            <h5>{{ __('cash_on_delivery_charge') }} (%)</h5>
                                        </div>

                                        @foreach ($merchant->cod_charges as $key => $cod_charge)
                                 
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label class="form-label" for="charge">{{ __($key) }}</label>
                                                        <input type="hidden" name="locations[]"
                                                            value="{{ $key }}">
                                                            <input type="text" class="form-control"
                                                                value="{{ $cod_charge }}" id="charge_{{ $key }}" name="charge[]"
                                                                >
                                                    </div>
                                                    @if ($errors->has('charge'))
                                                        <div class="invalid-feedback help-block">
                                                            <p>{{ $errors->first('charge') }}</p>
                                                        </div>
                                                    @endif
                                                </div>
                                        @endforeach
                                    </div>

                                    <div class="row mt-5">
                                        <div class="col-md-12 mb-3">
                                            <h5>{{ __('charge') }}</h5>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="bg-white redious-border p-20 p-sm-30 pt-sm-30">
                                                    <div class="row">
                                                        <div class="col-lg-12 table-responsive">
                                                            <table class="table table-bordered role-create-table"
                                                                id="permissions-table"
                                                                style="background-color: rgb(245,255,251);">
                                                                <thead>
                                                                    <tr>
                                                                        <th scope="col">{{ __('weight') }}</th>
                                                                        <th scope="col">{{ __('same_day') }}</th>
                                                                        <!-- <th scope="col">{{ __('next_day') }}</th> -->
                                                                        <th scope="col">{{ __('sub_city') }}</th>
                                                                        <th scope="col">{{ __('sub_urban_area') }}</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach ($merchant->charges as $weight => $charge)
                                                                        <tr>
                                                                            <td>
                                                                                <span
                                                                                    class="text-capitalize">{{ $weight }}</span>
                                                                                <input type="hidden"
                                                                                    value="{{ $weight }}"
                                                                                    name="weights[]">
                                                                            </td>
                                                                            <td>
                                                                                <input type="text"
                                                                                    class="form-control"
                                                                                    value="{{ data_get($charge, 'same_day', 0.0) }}"
                                                                                    id="charge_same_day_{{ $loop->index }}" name="same_day[]"
                                                                                    >
                                                                            </td>
                                                                            <!-- <td>
                                                                                <input type="text"
                                                                                    class="form-control"
                                                                                    value="{{ data_get($charge, 'next_day', 0.0) }}"
                                                                                    id="charge_next_day_{{ $loop->index }}" name="next_day[]"
                                                                                    >
                                                                            </td> -->
                                                                            <td>
                                                                                <input type="text"
                                                                                    class="form-control"
                                                                                    value="{{ data_get($charge, 'sub_city', 0.0) }}"
                                                                                    id="charge_sub_city_{{ $loop->index }}" name="sub_city[]"
                                                                                    >
                                                                            </td>
                                                                            <td>
                                                                                <input type="text"
                                                                                    class="form-control"
                                                                                    value="{{ data_get($charge, 'sub_urban_area', 0.0) }}"
                                                                                    id="charge_outside_dhaka_{{ $loop->index }}"
                                                                                    name="sub_urban_area[]" >
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach

                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-end align-items-center mt-30">
                                        <button type="submit" class="btn sg-btn-primary">{{ __('submit') }}</button>
                                        @include('backend.common.loading-btn', [
                                            'class' => 'btn sg-btn-primary',
                                        ])
                                    </div>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @include('admin.roles.script')
    @push('script')
        <script>
            $(document).on("change", ".file_picker", function(e) {
                let file = e.target.files[0];
                let selector = $(this).closest(".input_file_div");
                selector.find(".file-upload-text").text(file.name);
                selector
                    .find(".selected-img")
                    .attr("src", URL.createObjectURL(file));
            });
        </script>
    @endpush
@endsection
