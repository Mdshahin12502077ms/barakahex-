@extends('backend.layouts.master')
@section('title')
    {{ __('add') . ' ' . __('delivery_man') }}
@endsection
@section('mainContent')
    <div class="container-fluid">
        <div class="row gx-20">
            <div class="col-lg-12">
                <div class="header-top d-flex justify-content-between align-items-center mb-12">
                    <h3 class="section-title">{{ __('add') }} {{ __('delivery_man') }}</h3>
                    <div class="oftions-content-right">
                        <a href="{{ url()->previous() }}" class="d-flex align-items-center btn sg-btn-primary gap-2">
                            <i class="las la-arrow-left"></i>
                            <span>{{ __('back') }}</span>
                        </a>
                    </div>
                </div>
                <div class="bg-white redious-border p-20 p-sm-30">
                    <form action="{{ route('delivery.man.store') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card-inner">
                                    <div class="row g-gs">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label" for="first_name">{{ __('first_name') }}
                                                    <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name"
                                                        name="first_name" value="{{ old('first_name') }}">
                                                @if ($errors->has('first_name'))
                                                    <div class="invalid-feedback help-block">
                                                        <p>{{ $errors->first('first_name') }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label" for="last_name">{{ __('last_name') }}
                                                </label>
                                                    <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name"
                                                        name="last_name" value="{{ old('last_name') }}" >
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
                                                        value="{{ old('email') }}">
                                                @if ($errors->has('email'))
                                                    <div class="invalid-feedback help-block">
                                                        <p>{{ $errors->first('email') }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label" for="password">{{ __('password') }}
                                                    <span class="text-danger">*</span></label>
                                                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
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
                                                <label class="form-label" for="phone_number">{{ __('phone_number') }} <span
                                                        class="text-danger">*</span></label>
                                                <input type="number" class="form-control @error('phone_number') is-invalid @enderror" id="phone_number"
                                                    name="phone_number" value="{{ old('phone_number') }}">
                                                @if ($errors->has('phone_number'))
                                                    <div class="invalid-feedback help-block">
                                                        <p>{{ $errors->first('phone_number') }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label" for="zip">{{ __('zip') }}</label>
                                                    <input type="text" class="form-control" id="zip"
                                                        value="{{ old('zip') }}" name="zip">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label class="form-label" for="address">{{ __('address') }}</label>
                                                    <input type="text" class="form-control" id="address"
                                                        value="{{ old('address') }}" name="address">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label" for="city">{{ __('city') }}</label>
                                                    <input type="text" class="form-control" id="city"
                                                        value="{{ old('city') }}" name="city">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label"
                                                    for="pickup_branch">{{ __('branch') . ' ' . __('pickup_branch') }} </label>
                                                    <select class="without_search" id="branch" name="branch">
                                                        <option value="">{{ __('select_branch') }}</option>
                                                        @foreach ($branchs as $branch)
                                                            <option value="{{ $branch->id }}">
                                                                {{ __($branch->name) . ' (' . $branch->address . ')' }}</option>
                                                        @endforeach
                                                    </select>
                                                @if ($errors->has('branch'))
                                                    <div class="invalid-feedback help-block">
                                                        <p>{{ $errors->first('pickup_branch') }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label" for="pick_up_fee">{{ __('pick_up_fee') }} ({{ setting('default_currency_symbol') }})
                                                    <span class="text-danger">*</span></label>
                                                    <input type="number" class="form-control @error('pick_up_fee') is-invalid @enderror" id="pick_up_fee"
                                                        value="{{ old('pick_up_fee') ? old('pick_up_fee') : 0.0 }}" name="pick_up_fee">
                                                @if ($errors->has('pick_up_fee'))
                                                    <div class="invalid-feedback help-block">
                                                        <p>{{ $errors->first('pick_up_fee') }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label" for="delivery_fee">{{ __('delivery_fee') }} ({{ setting('default_currency_symbol') }})
                                                    <span class="text-danger">*</span></label>
                                                    <input type="number" class="form-control @error('delivery_fee') is-invalid @enderror" id="delivery_fee"
                                                        value="{{ old('delivery_fee') ? old('delivery_fee') : 0.0 }}" name="delivery_fee">
                                                @if ($errors->has('delivery_fee'))
                                                    <div class="invalid-feedback help-block">
                                                        <p>{{ $errors->first('delivery_fee') }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label" for="delivery_fee">{{ __('return_fee') }} ({{ setting('default_currency_symbol') }})
                                                    <span class="text-danger">*</span></label>
                                                    <input type="number" class="form-control @error('return_fee') is-invalid @enderror" id="return_fee"
                                                        value="{{ old('return_fee') ? old('return_fee') : 0.0 }}" name="return_fee">
                                                @if ($errors->has('return_fee'))
                                                    <div class="invalid-feedback help-block">
                                                        <p>{{ $errors->first('return_fee') }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label"
                                                    for="opening_balance">{{ __('opening_balance') }} ({{ setting('default_currency_symbol') }})<span
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
                                                <label class="form-label mb-1">{{ __('driving_license') }}</label>
                                                    <input class="form-control file_picker sp_file_input" type="file" id="driving_license"
                                                    name="driving_license" accept="image/*">
                                                <div class="invalid-feedback help-block">
                                                    <p class="image_error error">{{ $errors->first('driving_license') }}
                                                    </p>
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
                                                <label class="form-label mb-1">{{ __('profile_image') }}</label>

                                                <input class="form-control file_picker sp_file_input" type="file" id="image"
                                                    name="image_id" accept="image/*">
                                                <div class="invalid-feedback help-block">
                                                    <p class="image_error error">{{ $errors->first('image') }}</p>
                                                </div>
                                            </div>
                                            <div class="selected-files d-flex flex-wrap gap-20">
                                                <div class="selected-files-item">
                                                    <img class="selected-img" src="{{ getFileLink('80X80', []) }}"
                                                        alt="favicon">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-end align-items-center mt-30">
                                            <button type="submit"
                                                class="btn sg-btn-primary">{{ __('submit') }}</button>
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
        @include('admin.roles.script')
    @endsection
