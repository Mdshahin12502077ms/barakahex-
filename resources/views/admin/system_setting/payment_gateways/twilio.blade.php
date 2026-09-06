<div class="col-xxl-6 col-xl-6 col-lg-6 col-md-12">
    <div class="payment-box">
        <div class="payment-icon">
            <img src="{{ static_asset('images/sms/Twilio.svg') }}" alt="Stripe">
            <span class="payment-title">{{ __('twilio') }}</span>
        </div>

        <div class="payment-settings">
            <div class="payment-settings-btn">
                <a href="#" class="btn btn-md sg-btn-outline-primary" data-bs-toggle="modal" data-bs-target="#twilio"><i
                        class="las la-cog"></i> <span>{{ __('setting') }}</span></a>
            </div>

            <div class="setting-check">
                <input type="checkbox" id="twilio_system" name="active_sms_provider" value="twilio" data-url="{{ route('otp-status') }}" class="sms-status-change" {{ setting('active_sms_provider') == 'twilio' ? 'checked' : '' }}>
                <label for="twilio_system"></label>
            </div>
        </div>
    </div>
</div>
<!-- End Payment box -->
<div class="modal fade" id="twilio" tabindex="-1" aria-labelledby="paymentMethodLabel" aria-hidden="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <h6 class="sub-title">{{ __('twilio') }} {{ __('configuration') }}</h6>
            <button type="button" class="btn-close modal-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <form action="{{ route('otp.setting') }}" method="post">@csrf
                <div class="row gx-20">
                    <div
                        class="col-lg-12">
                        <div class="mb-4">
                            <label for="twilioSID" class="form-label">{{ __('twilio_sid') }}</label>
                            <input type="text" class="form-control rounded-2" id="twilioSID" required name="twilio_sms_sid" value="{{ isDemoMode() ? '******************' : stringMasking(setting('twilio_sms_sid'),'*',3,-3) }}">
                            <div class="nk-block-des text-danger">
                                <p class="twilio_sms_sid_error error"></p>
                            </div>
                        </div>
                    </div>
                    <!-- End Twilio SID -->

                    <div
                        class="col-lg-12">
                        <div class="mb-4">
                            <label for="twilioAuthToken"
                                   class="form-label">{{ __('twilio_auth_token') }}</label>
                            <input type="text" class="form-control rounded-2" required name="twilio_sms_auth_token" id="twilioAuthToken" value="{{ isDemoMode() ? '******************' : stringMasking(setting('twilio_sms_auth_token'),'*',3,-3) }}">
                            <div class="nk-block-des text-danger">
                                <p class="twilio_sms_auth_token_error error"></p>
                            </div>
                        </div>
                    </div>
                    <!-- End Twilio Auth Token -->

                    <div
                        class="col-lg-12">
                        <div class="mb-4">
                            <label for="validTwilioNumber"
                                   class="form-label">{{ __('valid_twilio_number') }}</label>
                            <input type="text" class="form-control rounded-2" id="validTwilioNumber" required name="valid_twilio_sms_number" value="{{ isDemoMode() ? '******************' : stringMasking(setting('valid_twilio_sms_number'),'*',3,-3) }}">
                            <div class="nk-block-des text-danger">
                                <p class="valid_twilio_sms_number_error error"></p>
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
