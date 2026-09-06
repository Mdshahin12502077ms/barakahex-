@extends('backend.layouts.master')

@section('title')
    {{__('api_credentials')}}
@endsection
@section('mainContent')
    <section class="oftions">
        <div class="container-fluid">
            <div class="row">
                @include('admin.merchants.details.menu')
                <div class="col-xxl-9 col-lg-8 col-md-8">
                    <div class="card-aside-wrap">
                        <div class="card-inner card-inner-lg">
                            <div class="header-top d-flex justify-content-between align-items-center mb-12">
                                <div class="">
                                    <div class="oftions-content-right">
                                        <h4 class="nk-block-title">{{__('api_credentials')}}</h4>
                                    </div>
                                </div>
                            </div>
                            @if(hasPermission('merchant_api_credentials_update') && @settingHelper('preferences')->where('title','merchant_api_update')->first()->staff)
                                <form action="{{ route('detail.merchant.api.credentials.update')}}" class="form-validate" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @endif
                                    <div class="card shadow-none">

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="card-inner">
                                                    <div class="row g-gs">
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label class="form-label" for="api_key">{{ __('api_key') }} {{ @settingHelper('preferences')->where('title','merchant_api_update')->first()->staff ? '*' : '' }}</label>
                                                                <div class="form-control-wrap d-flex">
                                                                    <input type="text" class="form-control" hidden id="merchant" value="{{ $merchant->id }}" name="id">
                                                                    <input type="text" class="form-control api-key" id="api_key" data-text="{{__('copied')}}" value="{{ $merchant->api_key }}" name="api_key" required>
                                                                    @if(hasPermission('merchant_api_credentials_update') && @settingHelper('preferences')->where('title','merchant_api_update')->first()->staff)
                                                                        <span type="button" class="btn btn-icon btn-trigger btn-tooltip {{ hasPermission('merchant_api_credentials_update') ? '' : 'd-none' }}" onclick="getKey(15, 'api_key')" data-original-title="{{__('change')}}"><i class="las la-redo-alt"></i></span>
                                                                    @endif
                                                                    <span type="button" class="btn btn-icon btn-trigger btn-tooltip copy-to-clipboard" data-text="{{ $merchant->api_key }}" data-original-title="{{__('copy')}}"
                                                                        ><i class="icon las la-copy"></i></span>
                                                                </div>
                                                                @if($errors->has('api_key'))
                                                                    <div class="invalid-feedback help-block">
                                                                        <p>{{ $errors->first('api_key') }}</p>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row g-gs">
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label class="form-label" for="secret_key">{{ __('secret_key') }} {{ @settingHelper('preferences')->where('title','merchant_api_update')->first()->staff ? '*' : ''}}</label>
                                                                <div class="form-control-wrap d-flex">
                                                                    <input type="text" class="form-control secret-key" id="secret_key" data-text="{{__('copied')}}" value="{{ $merchant->secret_key }}" name="secret_key" required>
                                                                    @if(hasPermission('merchant_api_credentials_update') && @settingHelper('preferences')->where('title','merchant_api_update')->first()->staff)
                                                                        <span type="button" class="btn btn-icon btn-trigger btn-tooltip {{ hasPermission('merchant_api_credentials_update') ? '' : 'd-none' }}" onclick="getKey(30, 'secret_key')" data-original-title="{{__('change')}}"><i class="las la-redo-alt"></i></span>
                                                                    @endif
                                                                    <span type="button" class="btn btn-icon btn-trigger btn-tooltip copy-to-clipboard" data-text="{{ $merchant->secret_key }}" data-original-title="{{__('copy')}}"
                                                                    ><i class="icon las la-copy"></i></span>
                                                                </div>
                                                                @if($errors->has('secret_key'))
                                                                    <div class="invalid-feedback help-block">
                                                                        <p>{{ $errors->first('secret_key') }}</p>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row g-gs mt-3">
                                                        <div class="col-md-12">
                                                            <hr class="my-2">
                                                            <h6 class="mb-3">{{ __('webhook_settings') }}</h6>
                                                        </div>
                                                    </div>

                                                    <div class="row g-gs">
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label class="form-label" for="webhook_url">{{ __('webhook_url') }}</label>
                                                                <input type="url" class="form-control" id="webhook_url" value="{{ $merchant->webhook_url }}" name="webhook_url" placeholder="https://example.com/api/webhook">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label class="form-label" for="webhook_secret">{{ __('webhook_secret') }}</label>
                                                                <div class="form-control-wrap d-flex">
                                                                    <input type="text" class="form-control" id="webhook_secret" data-text="{{__('copied')}}" value="{{ $merchant->webhook_secret }}" name="webhook_secret">
                                                                    @if(hasPermission('merchant_api_credentials_update') && @settingHelper('preferences')->where('title','merchant_api_update')->first()->staff)
                                                                        <span type="button" class="btn btn-icon btn-trigger btn-tooltip" onclick="getKey(32, 'webhook_secret')" data-original-title="{{__('change')}}"><i class="las la-redo-alt"></i></span>
                                                                    @endif
                                                                    <span type="button" class="btn btn-icon btn-trigger btn-tooltip copy-to-clipboard" data-text="{{ $merchant->webhook_secret }}" data-original-title="{{__('copy')}}"><i class="icon las la-copy"></i></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    @if(hasPermission('merchant_api_credentials_update') && @settingHelper('preferences')->where('title','merchant_api_update')->first()->staff)
                                                        <div class="row">
                                                            <div class="col-md-6 text-right mt-4">
                                                                <div class="mb-3">
                                                                    <button type="submit" class="btn sg-btn-primary resubmit">{{__('update')}}</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    @if(hasPermission('merchant_api_credentials_update') && @settingHelper('preferences')->where('title','merchant_api_update')->first()->staff)
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
