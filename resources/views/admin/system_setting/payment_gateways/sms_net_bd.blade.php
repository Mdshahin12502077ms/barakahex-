<div class="col-xxl-6 col-xl-6 col-lg-6 col-md-12">
    <div class="payment-box">
        <div class="payment-icon">
            <span class="payment-title font-weight-bold">SMS.NET.BD</span>
        </div>

        <div class="payment-settings">
            <div class="payment-settings-btn">
                <a href="#" class="btn btn-md sg-btn-outline-primary" data-bs-toggle="modal" data-bs-target="#smsNetBd">
                    <i class="las la-cog"></i> <span>{{ __('setting') }}</span>
                </a>
            </div>

            <div class="setting-check">
                <input type="checkbox" id="sms_net_bd" name="active_sms_provider" value="sms_net_bd" data-url="{{ route('otp-status') }}" class="sms-status-change" {{ setting('active_sms_provider') == 'sms_net_bd' ? 'checked' : '' }}>
                <label for="sms_net_bd"></label>
            </div>
        </div>
    </div>
</div>
<!-- End SMS box -->

<div class="modal fade" id="smsNetBd" tabindex="-1" aria-labelledby="smsNetBdLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <h6 class="sub-title">SMS.NET.BD {{ __('configuration') }}</h6>
            <button type="button" class="btn-close modal-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <form action="{{ route('otp.setting') }}" method="post">@csrf
                <div class="row gx-20">
                    <div class="col-lg-12">
                        <div class="mb-4">
                            <label for="sms_net_bd_api_key" class="form-label">API Key</label>
                            <input type="text" class="form-control rounded-2" id="sms_net_bd_api_key" required name="sms_net_bd_api_key" value="{{ setting('sms_net_bd_api_key') ?: 'NTEMxl49ikpYLeieqfYwIn25aEpfc5WLVAbIm1n6' }}">
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end align-items-center mt-30">
                    <button type="submit" class="btn sg-btn-primary">{{ __('save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
