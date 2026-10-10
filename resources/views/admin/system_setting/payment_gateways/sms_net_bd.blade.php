<div class="col-xxl-6 col-xl-6 col-lg-6 col-md-12">
    <div class="payment-box">
        <div class="payment-icon">
            <h4>
              MRAM
          </h4>
            <span class="payment-title">{{ __('MRAM') }}</span>
        </div>

        <div class="payment-settings">
            <div class="payment-settings-btn">
                <a href="#" class="btn btn-md sg-btn-outline-primary" data-bs-toggle="modal"
                    data-bs-target="#mram"><i class="las la-cog"></i> <span>{{ __('setting') }}</span></a>
            </div>

            <div class="setting-check">
                <input type="checkbox" id="mram_system" name="active_sms_provider" value="mram"
                    data-url="{{ route('otp-status') }}" class="sms-status-change"
                    {{ setting('active_sms_provider') == 'mram' ? 'checked' : '' }}>
                <label for="mram_system"></label>
            </div>
        </div>
    </div>
</div>
<!-- End Payment box -->

<div class="modal fade" id="mram" tabindex="-1" aria-labelledby="paymentMethodLabel" aria-hidden="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <h6 class="sub-title">{{ __('MRAM') }} {{ __('configuration') }}</h6>
            <button type="button" class="btn-close modal-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <form action="{{ route('otp.setting') }}" method="post">@csrf
                <div class="row gx-20">
                    <div class="col-lg-12">
                        <div class="mb-4">
                            <label for="mramApiKey" class="form-label">{{ __('MRAM API Key') }}</label>
                            <input type="text" class="form-control rounded-2" id="mramApiKey" required
                                name="mram_api_key"
                                value="{{ isDemoMode() ? '******************' : setting('mram_api_key') }}">
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <div class="mb-4">
                            <label for="mramSenderId" class="form-label">{{ __('MRAM Sender ID') }}</label>
                            <input type="text" class="form-control rounded-2" id="mramSenderId" required
                                name="mram_sender_id"
                                value="{{ isDemoMode() ? '******************' : setting('mram_sender_id') }}">
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end align-items-center mt-30">
                    <button type="submit" class="btn sg-btn-primary">{{ __('save') }}</button>
                    @include('backend.common.loading-btn', ['class' => 'btn sg-btn-primary'])
                </div>
            </form>
        </div>
    </div>
</div>
