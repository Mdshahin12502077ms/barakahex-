@extends('backend.layouts.master')
@section('title')
    {{ __('add') . ' ' . __('notice') }}
@endsection
@section('mainContent')
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col col-lg-8 col-md-9">
                <div class="header-top d-flex justify-content-between align-items-center">
                    <h3 class="section-title">{{ __('add') }} {{ __('notice') }}</h3>
                    <div class="oftions-content-right mb-12">
                        <div class="oftions-content-right">
                            <a href="{{ url()->previous() }}" class="d-flex align-items-center btn sg-btn-primary gap-2"><i
                                    class="icon las la-arrow-left"></i><span>{{ __('back') }}</span></a>
                        </div>
                    </div>
                </div>
                <form action="{{ route('notice.store') }}" class="form-validate" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="bg-white redious-border p-20 p-sm-30 pt-sm-30">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card-inner">
                                    <div class="mb-3">
                                        <label class="form-label" for="title">{{ __('title') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="title"
                                            value="{{ old('title') }}" name="title" required>
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
                                            <option value="alert-danger">{{ __('alert-danger') }}</option>
                                            <option value="alert-info">{{ __('alert-info') }}</option>
                                            <option value="alert-primary">{{ __('alert-primary') }}</option>
                                            <option value="alert-secondary">{{ __('alert-secondary') }}</option>
                                            <option value="alert-success">{{ __('alert-success') }}</option>
                                            <option value="alert-warning">{{ __('alert-warning') }}</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('start_date') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control date-picker" name="start_date" required
                                            autocomplete="off" value="{{ date('Y-m-d') }}">
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
                                            autocomplete="off" value="{{ date('Y-m-d', strtotime('+1 day')) }}">
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
                                            name="end_time">
                                        @if ($errors->has('end_time'))
                                            <div class="invalid-feedback help-block">
                                                <p>{{ $errors->first('end_time') }}</p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="note">{{ __('details') }} </label>
                                        <textarea class="form-control" id="details" placeholder="{{ __('details') . ' (' . __('optional') . ')' }}" name="details"
                                            required>{{ old('details') }}</textarea>
                                        @if ($errors->has('details'))
                                            <div class="invalid-feedback help-block">
                                                <p>{{ $errors->first('details') }}</p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="row">
                                        <div class="d-flex justify-content-left align-items-center mt-30">
                                            <button type="submit"
                                                class="btn sg-btn-primary resubmit">{{ __('submit') }}</button>
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
