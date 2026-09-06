@extends('backend.layouts.master')

@section('title')
    {{__('merchant').' '.__('permissions')}}
@endsection

@section('mainContent')
    <section class="oftions">
        <div class="container-fluid">
            <div class="row">
                @include('admin.merchants.details.menu')
                <div class="col-xxl-9 col-lg-8 col-md-8">
                    <div class="default-tab-list default-tab-list-v2 bg-white redious-border activeItem-bd-none p-20 p-sm-30">
                        <div class="d-flex justify-content-between align-items-center mb-12">
                            <div>
                                <h5>{{__('api_credentials')}}</h5>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                @if(hasPermission('merchant_api_credentials_update') && @settingHelper('preferences')->where('title','merchant_api_update')->first()->staff)
                                    <form action="{{ route('detail.merchant.api.credentials.update')}}" class="form-validate" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @endif
                                        <div class="">
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
                                                                        <span type="button" class="btn btn-icon btn-trigger btn-tooltip copy-to-clipboard" data-text="{{ $merchant->api_key }}"
                                                                            data-original-title="{{__('copy')}}"
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
                                                        @if(hasPermission('merchant_api_credentials_update') && @settingHelper('preferences')->where('title','merchant_api_update')->first()->staff)
                                                            <div class="row">
                                                                <div class="col-md-6 text-right mt-3">
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
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                <h5>{{__('merchant').' '.__('permissions')}}</h5>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <form action="{{ route('detail.merchant.permission.update', $merchant->id)}}" class="form-validate" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="card-inner">
                                                <div class="row g-gs">
                                                    <table class="table m-2">
                                                        <thead>
                                                        <tr>
                                                            <th scope="col">{{ __('title') }}</th>
                                                            <th scope="col">{{ __('status') }}</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>
                                                                    {{ __('read_credentials') }}
                                                                </td>
                                                                <td>
                                                                    <div class="custom-control custom-checkbox">
                                                                        <label class="custom-control-label" for="read_api_credentials">
                                                                        <input type="checkbox" class="custom-control-input  common-key" id="{{'read_api_credentials'}}" name="permissions[]" value="read_api_credentials" {{in_array('read_api_credentials', $merchant->user->permissions)? 'checked':''}}>
                                                                        <span></span>
                                                                    </label>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    {{ __('update_credentials') }}
                                                                </td>
                                                                <td>
                                                                    <div class="custom-control custom-checkbox">
                                                                        <label class="custom-control-label" for="update_api_credentials">
                                                                        <input type="checkbox" class="custom-control-input common-key" id="{{'update_api_credentials'}}" name="permissions[]" value="update_api_credentials" {{in_array('update_api_credentials', $merchant->user->permissions)? 'checked':''}}>
                                                                        <span></span>
                                                                    </label>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12 text-right mt-4">
                                                        <div class="mb-3">
                                                            <button type="submit" class="btn sg-btn-primary resubmit">{{__('update')}}</button>
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
                </div>
            </div>
        </div>
    </section>
    @include('admin.merchants.details.script')
@endsection

