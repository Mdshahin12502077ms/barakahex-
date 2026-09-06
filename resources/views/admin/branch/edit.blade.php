@extends('backend.layouts.master')

@section('title')
    {{ __('edit') . ' ' . __('branch') }}
@endsection

@section('mainContent')
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-12 col-lg-6 col-md-8">
                <div class="header-top d-flex justify-content-between align-items-center">
                    <h3 class="section-title">{{ __('edit') }} {{ __('branch') }}</h3>
                    <div class="oftions-content-right mb-12">
                        <div class="oftions-content-right">
                            <a href="{{ url()->previous() }}" class="d-flex align-items-center btn sg-btn-primary gap-2"><i
                                    class="icon las la-arrow-left"></i><span>{{ __('back') }}</span></a>
                        </div>
                    </div>
                </div>
                <form action="{{ route('admin.branch.update') }}" class="form-validate" method="POST"
                    enctype="multipart/form-data">
                    <input type="hidden" name="id" value="{{ $branch->id }}">
                    @csrf
                    <div class="bg-white redious-border p-20 p-sm-30 pt-sm-30">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card-inner">
                                    <div class="mb-3">
                                        <label class="form-label" for="method">{{ __('manager') }} <span
                                                class="text-danger">*</span></label>
                                        <select class="without_search form-select form-control @error('manager') is-invalid @enderror" name="manager">
                                            <option value="">{{ __('select_manager') }}</option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}"
                                                    {{ old('manager') ? ($user->id == old('manager') ? 'selected' : '') : ($user->id == $branch->user_id ? 'selected' : '') }}>
                                                    {{ $user->first_name . ' ' . $user->last_name }}(
                                                    {{  isDemoMode() ? '**************' :  $user->email ?? '' }})</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('manager'))
                                            <div class="invalid-feedback help-block">
                                                <p>{{ $errors->first('manager') }}</p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="name">{{ __('name') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                            value="{{ old('name') ? old('name') : $branch->name }}" name="name">
                                        @if ($errors->has('name'))
                                            <div class="invalid-feedback help-block">
                                                <p>{{ $errors->first('name') }}</p>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label" for="type">{{ __('type') }} <span class="text-danger">*</span></label>
                                        <select class="form-select form-control" id="type" name="type">
                                            <option value="branch" {{ old('type', $branch->type) == 'branch' ? 'selected' : '' }}>Branch</option>
                                            <option value="hub" {{ old('type', $branch->type) == 'hub' ? 'selected' : '' }}>Hub</option>
                                            <option value="sort_center" {{ old('type', $branch->type) == 'sort_center' ? 'selected' : '' }}>Sort Center</option>
                                            <option value="head_office" {{ old('type', $branch->type) == 'head_office' ? 'selected' : '' }}>Head Office</option>
                                        </select>
                                    </div>

                                    <div class="mb-3" id="parent_hub_div">
                                        <label class="form-label" for="parent_id">{{ __('parent_hub_or_office') }}</label>
                                        <select class="with_search form-select form-control" id="parent_id" name="parent_id">
                                            <option value="">{{ __('select_parent_hub') }} (Optional)</option>
                                            @foreach ($hubs as $hub)
                                                <option value="{{ $hub->id }}" {{ old('parent_id', $branch->parent_id) == $hub->id ? 'selected' : '' }}>
                                                    {{ $hub->name }} ({{ ucfirst(str_replace('_', ' ', $hub->type)) }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="phone_number">{{ __('phone_number') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="number" class="form-control @error('phone_number') is-invalid @enderror" id="phone_number"
                                               value="{{ old('phone_number') ? old('phone_number') : (isDemoMode() ? '**************' : ($branch->phone_number ?? '')) }}"
                                            name="phone_number">
                                        @if ($errors->has('phone_number'))
                                            <div class="invalid-feedback help-block">
                                                <p>{{ $errors->first('phone_number') }}</p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="note">{{ __('address') }} <span
                                                class="text-danger">*</span></label>
                                        <textarea class="form-control @error('address') is-invalid @enderror" id="address" placeholder="{{ __('address') }}" name="address">{{ old('address') ? old('address') : $branch->address }}</textarea>
                                        @if ($errors->has('address'))
                                            <div class="invalid-feedback help-block">
                                                <p>{{ $errors->first('address') }}</p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="d-flex justify-content-left align-items-center mt-30">
                                        <button type="submit"
                                            class="btn sg-btn-primary resubmit">{{ __('update') }}</button>
                                        @include('backend.common.loading-btn', [
                                            'class' => 'btn sg-btn-primary',
                                        ])
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
