<div class="col-xxl-6 col-xl-6 col-lg-6 col-md-12">
    <div class="payment-box">
        <div class="payment-icon">
            <img src="{{ static_asset('images/sms/reve.png') }}" alt="Stripe">
            <span class="payment-title">{{ __('reve_systems') }}</span>
        </div>

        <div class="payment-settings">
            <div class="payment-settings-btn">
                <a href="#" class="btn btn-md sg-btn-outline-primary"  data-bs-toggle="modal" data-bs-target="#reveSystems"><i class="las la-cog"></i> <span>{{ __('setting') }}</span></a>
            </div>

            <div class="setting-check">
                <input type="checkbox" id="reve_systems" name="active_sms_provider" value="spagreen" data-url="{{ route('otp-status') }}" class="sms-status-change" {{ setting('active_sms_provider') == 'spagreen' ? 'checked' : '' }}>
                <label for="reve_systems"></label>
            </div>
        </div>
    </div>
</div>
<!-- End Payment box -->
<div class="modal fade" id="reveSystems" tabindex="-1" aria-labelledby="paymentMethodLabel" aria-hidden="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <h6 class="sub-title">{{ __('reve_systems') }} {{ __('configuration') }}</h6>
            <button type="button" class="btn-close modal-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <form action="{{ route('otp.setting') }}" method="post">@csrf
                <div class="row">
                    <div
                    class="col-lg-12 spagreen_div">
                        <div class="mb-4">
                            <label for="REVESystemsApi"
                                class="form-label">{{ __('reve_systems_api_key') }}</label>
                            <input type="text" class="form-control rounded-2" id="REVESystemsApi" required name="spagreen_sms_api_key" value="{{ isDemoMode() ? '******************' : stringMasking(setting('spagreen_sms_api_key'),'*',3) }}">
                            <div class="nk-block-des text-danger">
                                <p class="spagreen_sms_api_key_error error"></p>
                            </div>
                        </div>
                    </div>
                    <!-- End REVE Systems Api Key -->

                    <div class="col-lg-12 spagreen_div ">
                        <div class="mb-4">
                            <label for="REVESystemsSecret"
                                class="form-label">{{ __('reve_systems_secret') }}</label>
                            <input type="text" class="form-control rounded-2" id="REVESystemsSecret" required name="spagreen_secret_key" value="{{ isDemoMode() ? '******************' : stringMasking(setting('spagreen_secret_key'),'*',3) }}">
                            <div class="nk-block-des text-danger">
                                <p class="spagreen_secret_key_error error"></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12 spagreen_div ">
                        <div class="mb-4">
                            <label for="REVESystemsSecret"
                                class="form-label">{{ __('reve_systems_sender_id') }}</label>
                            <input type="text" class="form-control rounded-2" id="reve_systems_sender_id"  name="spagreen_sender_id" value="{{ isDemoMode() ? '******************' : stringMasking(setting('spagreen_sender_id'),'*',3) }}">
                            <div class="nk-block-des text-danger">
                                <p class="spagreen_sender_id_error error"></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12 spagreen_div ">
                        <div class="mb-4">
                            <label for="REVESystemsSecret"
                                class="form-label">{{ __('sms_url') }}</label>
                            <input type="text" class="form-control rounded-2" id="reve_systems_base_url"  name="spagreen_sms_url" value="{{ isDemoMode() ? '******************' : stringMasking(setting('spagreen_sms_url'),'*',3) }}">
                            <div class="nk-block-des text-danger">
                                <p class="spagreen_sms_url_error error"></p>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end align-items-center mt-30">
                        <button type="submit" class="btn sg-btn-primary">{{ __('save') }}</button>
                        @include('backend.common.loading-btn',['class' => 'btn sg-btn-primary'])
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
