@extends('backend.layouts.master')

@section('title')
    {{ __('add_new_batch') }}
@endsection

@section('mainContent')
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-12 col-lg-6 col-md-8">
                <div class="header-top d-flex justify-content-between align-items-center mb-12">
                    <h3 class="section-title">{{ __('add_new_batch') }}</h3>
                    <div class="oftions-content-right">
                        <a href="{{ url()->previous() }}" class="d-flex align-items-center btn sg-btn-primary gap-2">
                            <i class="las la-arrow-left"></i>
                            <span>{{ __('back') }}</span>
                        </a>
                    </div>
                </div>
                <form action="{{ route('admin.withdraws.bulk.store') }}" class="form-validate" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="bg-white redious-border p-20 p-sm-30 pt-sm-30">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card-inner">

                                    <div class="mb-3">
                                        <label class="form-label" for="title">{{ __('Title') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="title" id="title"
                                            value="{{ old('title') }}" placeholder="{{ __('title') }}" required>
                                        @if ($errors->has('title'))
                                            <div class="invalid-feedback help-block">
                                                <p>{{ $errors->first('title') }}</p>
                                            </div>
                                        @endif
                                    </div>


                                    <div class="mb-3">
                                        <label class="form-label">{{ __('batch_type') }}</label>
                                        <select class="without_search form-select form-control withdraw_batches"
                                            name="batch_type" required>
                                            @foreach($methods as $method)
                                            <option value="{{ $method->name }}"
                                            {{ old('batch_type') ? (old('batch_type') == 'bank' ? 'selected' : '') : '' }}>
                                            {{ __($method->name) }}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('batch_type'))
                                            <div class="invalid-feedback help-block">
                                                <p>{{ $errors->first('batch_type') }}</p>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label" for="note">{{ __('Note') }} </label>
                                        <textarea class="form-control" id="note" placeholder="{{ __('note') . ' (' . __('optional') . ')' }}"
                                            name="note">{{ old('note') }}</textarea>
                                        @if ($errors->has('note'))
                                            <div class="invalid-feedback help-block">
                                                <p>{{ $errors->first('note') }}</p>
                                            </div>
                                        @endif
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
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
