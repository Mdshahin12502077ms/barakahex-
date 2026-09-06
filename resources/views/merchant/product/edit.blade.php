@extends('backend.layouts.master')
@section('title')
    {{ __('edit') . ' ' . __('product') }}
@endsection
@section('mainContent')
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-12 col-lg-6 col-md-8">
                <div class="header-top d-flex justify-content-between align-items-center">
                    <h3 class="section-title">{{ __('edit') }} {{ __('product') }}</h3>
                    <div class="oftions-content-right mb-12">
                        <div class="oftions-content-right">
                            <a href="{{ url()->previous() }}" class="d-flex align-items-center btn sg-btn-primary gap-2"><i
                                    class="icon las la-arrow-left"></i><span>{{ __('back') }}</span></a>
                        </div>
                    </div>
                </div>
                <form action="{{ route('merchant.products.update',$product->id) }}" class="form-validate" method="POST"
                      enctype="multipart/form-data">
                    @csrf
                    <div class="bg-white redious-border p-20 p-sm-30 pt-sm-30">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card-inner">
                                    <input type="hidden" name="merchant" hidden id="merchant" value="{{ $product->merchant_id }}">
                                    <div class="mb-3">
                                        <label class="form-label" for="name">{{ __('product_name') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                               value="{{ old('name') ? old('name') : $product->name }}" name="name">
                                        @if ($errors->has('name'))
                                            <div class="invalid-feedback help-block">
                                                <p>{{ $errors->first('name') }}</p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="note">{{ __('description') }} <span
                                                class="text-danger">*</span></label>
                                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" placeholder="{{ __('description') }}" name="description">{{ old('description') ? old('description') : $product->description }}</textarea>
                                        @if ($errors->has('description'))
                                            <div class="invalid-feedback help-block">
                                                <p>{{ $errors->first('description') }}</p>
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
