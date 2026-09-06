@extends('backend.layouts.master')
@section('title')
    {{ __('shops') . ' ' . __('list') }}
@endsection
@section('mainContent')
    <section class="oftions">
        <div class="container-fluid">
            <div class="row d-flex justify-content-md-center">
                <div class="col col-lg-6 col-md-9">
                    <div class="default-tab-list default-tab-list-v2 bg-white redious-border activeItem-bd-none p-20 p-sm-30">
                        <div class="d-flex justify-content-between align-items-center mb-12">
                            <div>
                                <h5>{{__('api_credentials')}}</h5>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                @if (!hasPermission('read_api_credentials'))
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="row g-gs">
                                                {{ __('contact_with_admin_to_get_your_api_credentials') }}
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    @if (Sentinel::getUser()->user_type == 'merchant' &&
                                            @settingHelper('preferences')->where('title', 'merchant_api_update')->first()->merchant &&
                                            hasPermission('update_api_credentials'))
                                        <form action="{{ route('merchant.api.credentials.update') }}" class="form-validate"
                                            method="POST" enctype="multipart/form-data">
                                            @csrf
                                    @endif
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="row g-gs">
                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <label class="form-label" for="api_key">{{ __('api_key') }}
                                                            {{ settingHelper('preferences')->where('title', 'merchant_api_update')->first()->merchant && hasPermission('update_api_credentials')? '*': '' }}</label>
                                                        <div class="form-control-wrap d-flex">
                                                            <input type="text" class="form-control" hidden
                                                                id="merchant"
                                                                value="{{ \Sentinel::getUser()->merchant->id }}"
                                                                name="id">
                                                            <input type="text" class="form-control api-key"
                                                                id="api_key" data-text="{{ __('copied') }}"
                                                                value="{{ isDemoMode() ? '******************' : \Sentinel::getUser()->merchant->api_key }}"
                                                                name="api_key"  required>
                                                            @if (Sentinel::getUser()->user_type == 'merchant' &&
                                                                    @settingHelper('preferences')->where('title', 'merchant_api_update')->first()->merchant &&
                                                                    hasPermission('update_api_credentials'))
                                                                <span type="button"
                                                                    class="btn btn-icon btn-trigger btn-tooltip"
                                                                    onclick="getKey(15, 'api_key')"
                                                                    data-original-title="{{ __('change') }}"><i
                                                                    class="icon las la-redo-alt"></i></span>
                                                            @endif
                                                            <span type="button"
                                                                class="btn btn-icon btn-trigger btn-tooltip copy-to-clipboard input"
                                                                data-original-title="{{ __('copy') }}"
                                                                data-text="{{ \Sentinel::getUser()->merchant->api_key }}"><i
                                                                    class="icon las la-copy"></i></span>
                                                        </div>
                                                        @if ($errors->has('api_key'))
                                                            <div class="invalid-feedback help-block">
                                                                <p>{{ $errors->first('api_key') }}</p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row g-gs">
                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <label class="form-label"
                                                            for="secret_key">{{ __('secret_key') }}
                                                            {{ settingHelper('preferences')->where('title', 'merchant_api_update')->first()->merchant && hasPermission('update_api_credentials')? '*': '' }}</label>
                                                        <div class="form-control-wrap d-flex">
                                                            <input type="text" class="form-control secret-key"
                                                                id="secret_key" data-text="{{ __('copied') }}"
                                                                value="{{ isDemoMode() ? '******************' : \Sentinel::getUser()->merchant->secret_key }}"
                                                                name="secret_key" required>
                                                            @if (Sentinel::getUser()->user_type == 'merchant' &&
                                                                    @settingHelper('preferences')->where('title', 'merchant_api_update')->first()->merchant &&
                                                                    hasPermission('update_api_credentials'))
                                                                <span type="button"
                                                                    class="btn btn-icon btn-trigger btn-tooltip"
                                                                    onclick="getKey(30, 'secret_key')"
                                                                    data-original-title="{{ __('change') }}"><i
                                                                        class="icon las la-redo-alt"></i></span>
                                                            @endif
                                                            <span type="button"
                                                                class="btn btn-icon btn-trigger btn-tooltip copy-to-clipboard input"
                                                                data-original-title="{{ __('copy') }}"
                                                                data-text="{{ \Sentinel::getUser()->merchant->secret_key }}"
                                                                ><i
                                                                    class="icon las la-copy"></i></span>
                                                        </div>
                                                        @if ($errors->has('secret_key'))
                                                            <div class="invalid-feedback help-block">
                                                                <p>{{ $errors->first('secret_key') }}</p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row g-gs mt-3">
                                                <div class="col-md-12">
                                                    <hr class="my-3">
                                                    <h6 class="mb-3">{{ __('webhook_settings') }}</h6>
                                                </div>
                                            </div>

                                            <div class="row g-gs">
                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <label class="form-label" for="webhook_url">{{ __('webhook_url') }}</label>
                                                        <div class="form-control-wrap">
                                                            <input type="url" class="form-control"
                                                                id="webhook_url"
                                                                value="{{ old('webhook_url') ?? \Sentinel::getUser()->merchant->webhook_url }}"
                                                                name="webhook_url"
                                                                placeholder="https://yourwebsite.com/api/webhook/barakah"
                                                                {{ (Sentinel::getUser()->user_type == 'merchant' && @settingHelper('preferences')->where('title', 'merchant_api_update')->first()->merchant && hasPermission('update_api_credentials')) ? '' : 'readonly' }}>
                                                        </div>
                                                        @if ($errors->has('webhook_url'))
                                                            <div class="invalid-feedback help-block">
                                                                <p>{{ $errors->first('webhook_url') }}</p>
                                                            </div>
                                                        @endif
                                                        <small class="text-muted d-block mt-1">{{ __('Enter the URL where status update notifications should be sent.') }}</small>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row g-gs">
                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <label class="form-label" for="webhook_secret">{{ __('webhook_secret') }}</label>
                                                        <div class="form-control-wrap d-flex">
                                                            <input type="text" class="form-control"
                                                                id="webhook_secret" data-text="{{ __('copied') }}"
                                                                value="{{ isDemoMode() ? '******************' : (\Sentinel::getUser()->merchant->webhook_secret ?? \Illuminate\Support\Str::random(32)) }}"
                                                                name="webhook_secret" readonly>
                                                            @if (Sentinel::getUser()->user_type == 'merchant' &&
                                                                    @settingHelper('preferences')->where('title', 'merchant_api_update')->first()->merchant &&
                                                                    hasPermission('update_api_credentials'))
                                                                <span type="button"
                                                                    class="btn btn-icon btn-trigger btn-tooltip"
                                                                    onclick="getKey(32, 'webhook_secret')"
                                                                    data-original-title="{{ __('change') }}"><i
                                                                        class="icon las la-redo-alt"></i></span>
                                                            @endif
                                                            <span type="button"
                                                                class="btn btn-icon btn-trigger btn-tooltip copy-to-clipboard input"
                                                                data-original-title="{{ __('copy') }}"
                                                                data-text="{{ \Sentinel::getUser()->merchant->webhook_secret }}"><i
                                                                    class="icon las la-copy"></i></span>
                                                        </div>
                                                        @if ($errors->has('webhook_secret'))
                                                            <div class="invalid-feedback help-block">
                                                                <p>{{ $errors->first('webhook_secret') }}</p>
                                                            </div>
                                                        @endif
                                                        <small class="text-muted d-block mt-1">{{ __('Your server should verify this secret in the HTTP header (X-Webhook-Secret).') }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                            @if (Sentinel::getUser()->user_type == 'merchant' &&
                                                    @settingHelper('preferences')->where('title', 'merchant_api_update')->first()->merchant &&
                                                    hasPermission('update_api_credentials'))
                                                <div class="row">
                                                    <div class="col-md-6 text-right mt-3">
                                                        <div class="mb-3">
                                                            <button type="submit"
                                                                class="btn sg-btn-primary resubmit">{{ __('update') }}</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    @if (Sentinel::getUser()->user_type == 'merchant' &&
                                            @settingHelper('preferences')->where('title', 'merchant_api_update')->first()->merchant &&
                                            hasPermission('update_api_credentials'))
                                        </form>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
