@extends('backend.layouts.master')

@section('title')
    {{ __('profile') }}
@endsection

@section('mainContent')
<div class="container-fluid">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-20 flex-wrap gap-2">
        <div>
            <h4 class="section-title mb-1">{{ __('profile') }}</h4>
            <p class="text-muted small mb-0">{{ __('manage_your_personal_information_and_security') }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('deliveryman.dashboard') }}" class="btn btn-sm sg-btn-outline-primary">
                <i class="las la-arrow-left"></i> {{ __('back_to_dashboard') }}
            </a>
        </div>
    </div>

    <div class="row g-4">
        {{-- Left Profile Summary Card --}}
        <div class="col-lg-4">
            <div class="bg-white redious-border p-20 p-sm-30 text-center mb-4">
                <div class="position-relative d-inline-block mb-3">
                    <img src="{{ getFileLink('80X80', $user->image_id) }}" 
                         alt="{{ $user->first_name }}" 
                         class="rounded-circle border border-3 border-primary shadow-sm" 
                         style="width: 100px; height: 100px; object-fit: cover;">
                </div>
                <h5 class="fw-bold text-dark mb-1">{{ $user->first_name }} {{ $user->last_name }}</h5>
                <span class="badge bg-primary text-white mb-3">{{ __('delivery_hero') }}</span>

                <div class="text-start border-top pt-3">
                    <div class="mb-2">
                        <small class="text-muted d-block">{{ __('email') }}</small>
                        <span class="fw-semibold text-dark">{{ $user->email }}</span>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted d-block">{{ __('phone') }}</small>
                        <span class="fw-semibold text-dark">{{ $user->phone_number ?: 'N/A' }}</span>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted d-block">{{ __('address') }}</small>
                        <span class="text-dark">{{ $deliveryMan->address ?? 'N/A' }}</span>
                    </div>
                    @if(@$deliveryMan->city || @$deliveryMan->zip)
                        <div class="mb-2">
                            <small class="text-muted d-block">{{ __('city') }} / {{ __('zip') }}</small>
                            <span class="text-dark">{{ $deliveryMan->city ?: '' }} {{ $deliveryMan->zip ? '('.$deliveryMan->zip.')' : '' }}</span>
                        </div>
                    @endif
                    <div class="mb-0">
                        <small class="text-muted d-block">{{ __('status') }}</small>
                        <span class="badge bg-success text-white">{{ __('active') }}</span>
                    </div>
                </div>
            </div>

            {{-- Fee Rates Overview Card --}}
            <div class="bg-white redious-border p-20 p-sm-30 mb-4">
                <h6 class="fw-bold text-dark mb-3">
                    <i class="las la-percentage text-primary"></i> {{ __('commission_and_fee_rates') }}
                </h6>
                <ul class="list-group list-group-flush small">
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>{{ __('pickup_fee') }}</span>
                        <span class="fw-bold text-dark">{{ setting('default_currency') }} {{ number_format(@$deliveryMan->pick_up_fee ?? 0, 2) }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>{{ __('delivery_fee') }}</span>
                        <span class="fw-bold text-dark">{{ setting('default_currency') }} {{ number_format(@$deliveryMan->delivery_fee ?? 0, 2) }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>{{ __('return_fee') }}</span>
                        <span class="fw-bold text-dark">{{ setting('default_currency') }} {{ number_format(@$deliveryMan->return_fee ?? 0, 2) }}</span>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Right Forms: Update Profile & Change Password --}}
        <div class="col-lg-8">
            {{-- Update Profile Form --}}
            <div class="bg-white redious-border p-20 p-sm-30 mb-4">
                <h5 class="fw-bold text-dark mb-3">
                    <i class="las la-user-edit font-20 text-primary"></i> {{ __('edit_profile') }}
                </h5>
                <form action="{{ route('deliveryman.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('first_name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $user->first_name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('last_name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $user->last_name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('email') }} <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('phone') }} <span class="text-danger">*</span></label>
                            <input type="text" name="phone_number" class="form-control" value="{{ old('phone_number', $user->phone_number) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('city') }}</label>
                            <input type="text" name="city" class="form-control" value="{{ old('city', @$deliveryMan->city) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('zip') }}</label>
                            <input type="text" name="zip" class="form-control" value="{{ old('zip', @$deliveryMan->zip) }}">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">{{ __('address') }}</label>
                            <textarea name="address" class="form-control" rows="2">{{ old('address', $deliveryMan->address ?? '') }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('profile_image') }}</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('driving_license') }}</label>
                            <input type="file" name="driving_license" class="form-control" accept="image/*,.pdf">
                        </div>
                        <div class="col-12 text-end">
                            <button type="submit" class="btn sg-btn-primary px-4">
                                <i class="las la-save"></i> {{ __('save_changes') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Change Password Form --}}
            <div class="bg-white redious-border p-20 p-sm-30">
                <h5 class="fw-bold text-dark mb-3">
                    <i class="las la-lock font-20 text-warning"></i> {{ __('change_password') }}
                </h5>
                <form action="{{ route('deliveryman.change.password') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">{{ __('current_password') }} <span class="text-danger">*</span></label>
                            <input type="password" name="current_password" class="form-control" required placeholder="••••••••">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('new_password') }} <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" required minlength="6" placeholder="••••••••">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('confirm_password') }} <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control" required minlength="6" placeholder="••••••••">
                        </div>
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-warning px-4 text-dark fw-semibold">
                                <i class="las la-key"></i> {{ __('update_password') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
