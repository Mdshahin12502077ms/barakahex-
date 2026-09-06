<div class="col-xxl-6 col-xl-6 col-lg-6 col-md-12">
    <div class="payment-box">
        <div class="payment-icon">
            <img src="{{ static_asset('images/sms/mimo.png') }}" alt="Stripe">
            <span class="payment-title">{{ __('mimo') }}</span>
        </div>

        <div class="payment-settings">
            <div class="payment-settings-btn">
                <a href="#" class="btn btn-md sg-btn-outline-primary"  data-bs-toggle="modal" data-bs-target="#mimo"><i class="las la-cog"></i> <span>{{ __('setting') }}</span></a>
            </div>
            <div class="setting-check">
                <input type="checkbox" id="mimo_system" name="active_sms_provider" value="mimo" data-url="{{ route('otp-status') }}" class="sms-status-change" {{ setting('active_sms_provider') == 'mimo' ? 'checked' : '' }}>
                <label for="mimo_system"></label>
            </div>
        </div>
    </div>
</div>
<!-- End Payment box -->
<div class="modal fade" id="mimo" tabindex="-1" aria-labelledby="paymentMethodLabel" aria-hidden="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <h6 class="sub-title">{{ __('mimo') }} {{ __('configuration') }}</h6>
            <button type="button" class="btn-close modal-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <form action="{{ route('otp.setting') }}" method="post">@csrf
                <div class="row gx-20">
                    <div class="col-lg-12 mimo_div">
                        <div class="mb-4">
                            <label for="MIMOUsername" class="form-label">{{ __('mimo_username') }}</label>
                            <input type="text" class="form-control rounded-2" required id="MIMOUsername" name="mimo_username" value="{{ isDemoMode() ? '******************' : stringMasking(setting('mimo_username'),'*',3) }}">
                            <div class="nk-block-des text-danger">
                                <p class="mimo_username_error error"></p>
                            </div>
                        </div>
                    </div>
                    <!-- End MIMO Username -->

                    <div
                        class="col-lg-12 mimo_div">
                        <div class="mb-4">
                            <label for="MIMOPassword" class="form-label">{{ __('mimo_password') }}</label>
                            <input type="text" class="form-control rounded-2" required id="MIMOPassword" name="mimo_sms_password" value="{{ isDemoMode() ? '******************' : stringMasking(setting('mimo_sms_password'),'*',3) }}">
                            <div class="nk-block-des text-danger">
                                <p class="mimo_sms_password_error error"></p>
                            </div>
                        </div>
                    </div>
                    <!-- End MIMO Password -->

                    <div
                        class="col-lg-12 mimo_div">
                        <div class="mb-4">
                            <label for="MIMOSenderID" class="form-label">{{ __('mimo_sender_id') }}</label>
                            <input type="text" class="form-control rounded-2" required id="MIMOSenderID" name="mimo_sms_sender_id" value="{{ isDemoMode() ? '******************' : stringMasking(setting('mimo_sms_sender_id'),'*',3) }}">
                            <div class="nk-block-des text-danger">
                                <p class="mimo_sms_sender_id_error error"></p>
                            </div>
                        </div>
                    </div>
                    <!-- End MIMO Sender ID -->
                    <!-- End MIMO Fields -->
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
