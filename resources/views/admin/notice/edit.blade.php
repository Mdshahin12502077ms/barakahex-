@extends('backend.layouts.master')
@section('title')
    {{ __('edit') . ' ' . __('notice') }}
@endsection
@section('mainContent')
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col col-lg-8 col-md-9">
                <div class="header-top d-flex justify-content-between align-items-center">
                    <h3 class="section-title">{{ __('edit') }} {{ __('notice') }}</h3>
                    <div class="oftions-content-right mb-12">
                        <div class="oftions-content-right">
                            <a href="{{ url()->previous() }}" class="d-flex align-items-center btn sg-btn-primary gap-2"><i
                                    class="icon las la-arrow-left"></i><span>{{ __('back') }}</span></a>
                        </div>
                    </div>
                </div>
                <form action="{{ route('notice.update') }}" class="form-validate" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="bg-white redious-border p-20 p-sm-30 pt-sm-30">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card-inner">
                                    <div class="mb-3">
                                        <label class="form-label" for="title">{{ __('title') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="id"
                                            value="{{ $notice->id }}" name="id" required hidden>
                                        <input type="text" class="form-control" id="title"
                                            value="{{ old('title') ? old('title') : $notice->title }}" name="title"
                                            required>
                                        @if ($errors->has('title'))
                                            <div class="invalid-feedback help-block">
                                                <p>{{ $errors->first('title') }}</p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="alert_class">{{ __('alert_class') }} <span
                                                class="text-danger">*</span></label>
                                        <select class="without_search form-control" name="alert_class">
                                            <option>{{ __('select_class') }}</option>
                                            <option value="alert-danger"
                                                {{ $notice->alert_class == 'alert-danger' ? 'selected' : '' }}>
                                                {{ __('alert-danger') }}</option>
                                            <option value="alert-info"
                                                {{ $notice->alert_class == 'alert-info' ? 'selected' : '' }}>
                                                {{ __('alert-info') }}</option>
                                            <option value="alert-primary"
                                                {{ $notice->alert_class == 'alert-primary' ? 'selected' : '' }}>
                                                {{ __('alert-primary') }}</option>
                                            <option value="alert-secondary"
                                                {{ $notice->alert_class == 'alert-secondary' ? 'selected' : '' }}>
                                                {{ __('alert-secondary') }}</option>
                                            <option value="alert-success"
                                                {{ $notice->alert_class == 'alert-success' ? 'selected' : '' }}>
                                                {{ __('alert-success') }}</option>
                                            <option value="alert-warning"
                                                {{ $notice->alert_class == 'alert-warning' ? 'selected' : '' }}>
                                                {{ __('alert-warning') }}</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('start_date') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control date-picker" name="date" required
                                            autocomplete="off"
                                            value="{{ old('start_date') ?? date('Y-m-d', strtotime($notice->start_time)) }}">
                                        @if ($errors->has('start_date'))
                                            <div class="invalid-feedback help-block">
                                                <p>{{ $errors->first('start_date') }}</p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('start_time') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="time" class="form-control time-picker" id="outlined-time-picker"
                                            value="{{ old('start_time', $notice->start_time ? date('H:i', strtotime($notice->start_time)) : '') }}"
                                            name="start_time">
                                        @if ($errors->has('start_time'))
                                            <div class="invalid-feedback help-block">
                                                <p>{{ $errors->first('start_time') }}</p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('end_date') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control date-picker" name="end_date" required
                                            autocomplete="off"
                                            value="{{ old('end_date') != '' ? old('end_date') : date('Y-m-d', strtotime($notice->end_time)) }}">
                                        @if ($errors->has('end_date'))
                                            <div class="invalid-feedback help-block">
                                                <p>{{ $errors->first('end_date') }}</p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('end_time') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="time" class="form-control time-picker" id="outlined-time-picker"
                                            value="{{ old('end_time', date('H:i', strtotime($notice->end_time))) }}"
                                            name="end_time">
                                        @if ($errors->has('end_time'))
                                            <div class="invalid-feedback help-block">
                                                <p>{{ $errors->first('end_time') }}</p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="note">{{ __('details') }} </label>
                                        <textarea class="form-control" id="details" placeholder="{{ __('details') . ' (' . __('optional') . ')' }}"
                                            name="details" required>{{ old('details') ? old('details') : $notice->details }}</textarea>
                                        @if ($errors->has('details'))
                                            <div class="invalid-feedback help-block">
                                                <p>{{ $errors->first('details') }}</p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="row">
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
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
