@extends('backend.layouts.master')
@section('title')
    {{ __('add') . ' ' . __('merchant') }}
@endsection
@section('mainContent')
    <div class="container-fluid">
        <div class="row gx-20">
            <div class="col-lg-12">

                <div class="header-top d-flex justify-content-between align-items-center mb-12">
                    <h3 class="section-title">{{ __('add') }} {{ __('merchant') }}</h3>
                    <div class="oftions-content-right">
                    </div>
                </div>
                <div class="bg-white redious-border p-20 p-sm-30">
                    <form action="{{ route('merchant.store') }}" class="form-validate" method="POST"
                        enctype="multipart/form-data">
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
                                                    value="{{ old('first_name') }}" name="first_name">
                                                @if ($errors->has('first_name'))
                                                    <div class="invalid-feedback help-block">
                                                        <p>{{ $errors->first('first_name') }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label" for="fv-full-name">{{ __('last_name') }}</label>
                                                <input type="text" class="form-control"
                                                    value="{{ old('last_name') }}" name="last_name">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label" for="fv-email">{{ __('email') }} <span
                                                        class="text-danger">*</span></label>
                                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="fv-email"
                                                    value="{{ old('email') }}" name="email">
                                                @if ($errors->has('email'))
                                                    <div class="invalid-feedback help-block">
                                                        <p>{{ $errors->first('email') }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label" for="fv-email">{{ __('password') }} <span
                                                        class="text-danger">*</span></label>
                                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="fv-email" name="password"
                                                >
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
                                                <input type="text" class="form-control @error('company') is-invalid @enderror" value="{{ old('company') }}"
                                                    id="company" name="company">
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
                                                    value="{{ old('phone_number') }}" name="phone_number">
                                                @if ($errors->has('phone_number'))
                                                    <div class="invalid-feedback help-block">
                                                        <p>{{ $errors->first('phone_number') }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label" for="website">{{ __('website') }}</label>
                                                <input type="text" class="form-control" id="website"
                                                    value="{{ old('website') }}" name="website">
                                            </div>

                                            @if ($errors->has('website'))
                                                <div class="invalid-feedback help-block">
                                                    <p>{{ $errors->first('website') }}</p>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label" for="city">{{ __('city') }}</label>
                                                <input type="text" class="form-control" id="city"
                                                    value="{{ old('city') }}" name="city">
                                                    @if ($errors->has('city'))
                                                    <div class="invalid-feedback help-block">
                                                        <p>{{ $errors->first('city') }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label" for="zip">{{ __('zip') }}</label>
                                                <input type="text" class="form-control" value="{{ old('zip') }}"
                                                    id="zip" name="zip">
                                                    @if ($errors->has('zip'))
                                                    <div class="invalid-feedback help-block">
                                                        <p>{{ $errors->first('zip') }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label" for="address">{{ __('address') }}</label>
                                                <input type="text" class="form-control" value="{{ old('address') }}"
                                                    id="address" name="address">
                                                    @if ($errors->has('address'))
                                                    <div class="invalid-feedback help-block">
                                                        <p>{{ $errors->first('address') }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label"
                                                    for="billing_street">{{ __('billing') . ' ' . __('street') }}</label>
                                                <input type="text" class="form-control"
                                                    value="{{ old('billing_street') }}" id="billing_street"
                                                    name="billing_street">
                                                    @if ($errors->has('billing_street'))
                                                    <div class="invalid-feedback help-block">
                                                        <p>{{ $errors->first('billing_street') }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label"
                                                    for="billing_city">{{ __('billing') . ' ' . __('city') }}</label>
                                                <input type="text" class="form-control"
                                                    value="{{ old('billing_city') }}" id="billing_city"
                                                    name="billing_city">
                                                    @if ($errors->has('billing_city'))
                                                    <div class="invalid-feedback help-block">
                                                        <p>{{ $errors->first('billing_city') }}</p>
                                                    </div>
                                                @endif
                                            </div>

                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label"
                                                    for="billing_zip">{{ __('billing') . ' ' . __('zip') }}</label>
                                                <input type="text" class="form-control"
                                                    value="{{ old('billing_zip') }}" id="billing_zip"
                                                    name="billing_zip">
                                                    @if ($errors->has('billing_zip'))
                                                    <div class="invalid-feedback help-block">
                                                        <p>{{ $errors->first('billing_zip') }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label" for="vat">{{ __('vat') }} (%)</label>
                                                <input type="text" class="form-control @error('vat') is-invalid @enderror"
                                                    value="{{ old('vat') != '' ? old('old') : 0 }}" id="vat"
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
                                                    for="opening_balance">{{ __('opening_balance') }} ({{ setting('default_currency') }})<span
                                                        class="text-danger">*</span></label>
                                                <input type="number" class="form-control @error('opening_balance') is-invalid @enderror" id="opening_balance"
                                                    value="{{ old('opening_balance') ? old('opening_balance') : 0.0 }}"
                                                    name="opening_balance">
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
                                                    <img class="selected-img" src="{{ getFileLink('80X80', []) }}"
                                                        alt="favicon">
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
                                                    <img class="selected-img" src="{{ getFileLink('80X80', []) }}"
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
                                                    <img class="selected-img" src="{{ getFileLink('80X80', []) }}"
                                                        alt="favicon">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-5">
                                            <div class="col-md-12 mb-3">
                                                <h5>{{ __('cash_on_delivery_charge') }}</h5>
                                            </div>
                                            @foreach ($cod_charges as $cod_charge)
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label class="form-label"
                                                            for="charge">{{ __($cod_charge->location) }} ({{ setting('default_currency') }})</label>
                                                        <input type="hidden" name="locations[]"
                                                            value="{{ $cod_charge->location }}">
                                                        <input type="text" class="form-control"
                                                            value="{{ $cod_charge->charge }}" id="charge"
                                                            name="charge[]">
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
                                                                            <th scope="col">{{ __('weight') }} ({{ setting('default_weight') }})</th>
                                                                            <th scope="col">{{ __('same_day') }} ({{ setting('default_currency') }})</th>
                                                                            <!-- <th scope="col">{{ __('next_day') }} ({{ setting('default_currency') }})</th> -->
                                                                            <th scope="col">{{ __('sub_city') }} ({{ setting('default_currency') }})</th>
                                                                            <th scope="col">{{ __('sub_urban_area') }} ({{ setting('default_currency') }})
                                                                            </th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach ($charges as $charge)
                                                                            <tr>
                                                                                <td>
                                                                                    <span
                                                                                        class="text-capitalize">{{ $charge->weight }}</span>
                                                                                    <input type="hidden"
                                                                                        value="{{ $charge->weight }}"
                                                                                        name="weights[]">
                                                                                    <input type="hidden"
                                                                                        value="{{ $charge->id }}"
                                                                                        name="cod_ids[]">
                                                                                </td>
                                                                                <td>
                                                                                    <input type="text"
                                                                                        class="form-control"
                                                                                        value="{{ $charge->same_day }}"
                                                                                        id="charge" name="same_day[]"
                                                                                    >
                                                                                </td>
                                                                                <!-- <td>
                                                                                    <input type="text"
                                                                                        class="form-control"
                                                                                        value="{{ $charge->next_day }}"
                                                                                        id="charge" name="next_day[]"
                                                                                    >
                                                                                </td> -->
                                                                                <td>
                                                                                    <input type="text"
                                                                                        class="form-control"
                                                                                        value="{{ $charge->sub_city }}"
                                                                                        id="charge" name="sub_city[]"
                                                                                    >
                                                                                </td>
                                                                                <td>
                                                                                    <input type="text"
                                                                                        class="form-control"
                                                                                        value="{{ $charge->sub_urban_area }}"
                                                                                        id="charge"
                                                                                        name="sub_urban_area[]">
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
                                            <button type="submit"
                                                class="btn sg-btn-primary resubmit">{{ __('submit') }}</button>
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
