<div class="col-xxl-6 col-xl-6 col-lg-6 col-md-12">
    <div class="payment-box">
        <div class="payment-icon">
            <img src="{{ static_asset('images/sms/nexmo.png') }}" alt="AAmarpay">
            <span class="payment-title">{{ __('nexmo') }}</span>
        </div>

        <div class="payment-settings">
            <div class="payment-settings-btn">
                <a href="#" class="btn btn-md sg-btn-outline-primary" data-bs-toggle="modal" data-bs-target="#nexmo"
                ><i class="las la-cog"></i> <span>{{ __('setting') }}</span></a>
            </div>
            <div class="setting-check">
                <input type="checkbox" id="nexmo_system" name="active_sms_provider" value="nexmo" data-url="{{ route('otp-status') }}" class="sms-status-change" {{ setting('active_sms_provider') == 'nexmo' ? 'checked' : '' }}>
                <label for="nexmo_system"></label>
            </div>
        </div>
    </div>
</div>
<!-- End Payment box -->
<div class="modal fade" id="nexmo" tabindex="-1" aria-labelledby="paymentMethodLabel" aria-hidden="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <h6 class="sub-title">{{ __('nexmo') }} {{ __('configuration') }}</h6>
            <button type="button" class="btn-close modal-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <form action="{{ route('otp.setting') }}" method="post">@csrf
                <div class="row gx-20">
                    <div
                        class="col-lg-12 nexmo_div">
                        <div class="mb-4">
                            <label for="NexmoKey" class="form-label">{{ __('nexmo_key') }}</label>
                            <input type="text" class="form-control rounded-2" required id="NexmoKey" name="nexmo_sms_key" value="{{ isDemoMode() ? '******************' : stringMasking(setting('nexmo_sms_key'),'*',3) }}">
                            <div class="nk-block-des text-danger">
                                <p class="nexmo_sms_key_error error"></p>
                            </div>
                        </div>
                    </div>
                    <!-- End Nexmo Key -->

                    <div
                        class="col-lg-12 nexmo_div">
                        <div class="mb-4">
                            <label for="NexmoSecret" class="form-label">{{ __('nexmo_secret') }}</label>
                            <input type="text" class="form-control rounded-2" required id="NexmoSecret" name="nexmo_sms_secret_key" value="{{ isDemoMode() ? '******************' : stringMasking(setting('nexmo_sms_secret_key'),'*',3) }}">
                            <div class="nk-block-des text-danger">
                                <p class="nexmo_sms_secret_key_error error"></p>
                            </div>
                        </div>
                    </div>
                    <!-- End Nexmo Secret -->
                    <!-- End Nexmo Fields -->
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
