<div class="col-xxl-6 col-xl-6 col-lg-6 col-md-12">
    <div class="payment-box">
        <div class="payment-icon">
            <img src="{{ static_asset('images/sms/ssl.svg') }}" alt="AAmarpay">
            <span class="payment-title">{{ __('ssl_wireless') }}</span>
        </div>

        <div class="payment-settings">
            <div class="payment-settings-btn">
                <a href="#" class="btn btn-md sg-btn-outline-primary" data-bs-toggle="modal" data-bs-target="#sslWireless"
                ><i class="las la-cog"></i> <span>{{ __('setting') }}</span></a>
            </div>

            <div class="setting-check">
                <input type="checkbox" id="ssl_wireless" name="active_sms_provider" value="ssl" data-url="{{ route('otp-status') }}" class="sms-status-change" {{ setting('active_sms_provider') == 'ssl' ? 'checked' : '' }}>
                <label for="ssl_wireless"></label>
            </div>
        </div>
    </div>
</div>
<!-- End Payment box -->
<div class="modal fade" id="sslWireless" tabindex="-1" aria-labelledby="paymentMethodLabel" aria-hidden="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <h6 class="sub-title">{{ __('ssl_wireless') }} {{ __('configuration') }}</h6>
            <button type="button" class="btn-close modal-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <form action="{{ route('otp.setting') }}" method="post">@csrf
                <div class="row gx-20">
                    <div
                        class="col-lg-12 ssl_wireless_div">
                        <div class="mb-4">
                            <label for="SSLWirelessKey" class="form-label">{{ __('ssl_sms_api_token') }}</label>
                            <input type="text" class="form-control rounded-2" id="SSLWirelessKey" required name="ssl_sms_api_token" value="{{ isDemoMode() ? '******************' : stringMasking(setting('ssl_sms_api_token'),'*',3) }}">
                            <div class="nk-block-des text-danger">
                                <p class="ssl_sms_api_token_error error"></p>
                            </div>
                        </div>
                    </div>
                    <!-- End SSL Wireless Key -->

                    <div class="col-lg-12 ssl_wireless_div">
                        <div class="mb-4">
                            <label for="SSLWirelessSecret"
                                   class="form-label">{{ __('ssl_sms_sid') }}</label>
                            <input type="text" class="form-control rounded-2" id="SSLWirelessSecret" required name="ssl_sms_sid" value="{{ isDemoMode() ? '******************' : stringMasking(setting('ssl_sms_sid'),'*',3) }}">
                            <div class="nk-block-des text-danger">
                                <p class="ssl_sms_sid_error error"></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12 ssl_wireless_div">
                        <div class="mb-4">
                            <label for="SSLWirelessSecret"
                                   class="form-label">{{ __('ssl_sms_url') }}</label>
                            <input type="text" class="form-control rounded-2" required id="ssl_sms_url" name="ssl_sms_url" value="{{ isDemoMode() ? '******************' : stringMasking(setting('ssl_sms_url'),'*',3) }}">
                            <div class="nk-block-des text-danger">
                                <p class="ssl_sms_url_error error"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END Permissions Tab====== -->
                <div class="d-flex justify-content-end align-items-center mt-30">
                    <button type="submit" class="btn sg-btn-primary">{{ __('save') }}</button>
                    @include('backend.common.loading-btn',['class' => 'btn sg-btn-primary'])
                </div>
            </form>
        </div>
    </div>
</div>
