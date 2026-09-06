<div class="col-xxl-6 col-xl-6 col-lg-6 col-md-12">
    <div class="payment-box">
        <div class="payment-icon">
            <img src="{{ static_asset('images/sms/fast.png') }}" alt="Stripe">
            <span class="payment-title">{{ __('fast_2sms') }}</span>
        </div>

        <div class="payment-settings">
            <div class="payment-settings-btn">
                <a href="#" class="btn btn-md sg-btn-outline-primary"  data-bs-toggle="modal" data-bs-target="#fastSms"><i class="las la-cog"></i> <span>{{ __('setting') }}</span></a>
            </div>

            <div class="setting-check">
                <input type="checkbox" id="fast2" name="active_sms_provider" value="fast2" data-url="{{ route('otp-status') }}" class="sms-status-change" {{ setting('active_sms_provider') == 'fast2' ? 'checked' : '' }}>
                <label for="fast2"></label>
            </div>
        </div>
    </div>
</div>
<!-- End Payment box -->
<div class="modal fade" id="fastSms" tabindex="-1" aria-labelledby="paymentMethodLabel" aria-hidden="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <h6 class="sub-title">{{ __('fast_2sms') }} {{ __('configuration') }}</h6>
            <button type="button" class="btn-close modal-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <form action="{{ route('otp.setting') }}" method="post">@csrf
                <div
                    class="col-lg-12 fast2_div">
                    <div class="mb-4">
                        <label for="authKey" class="form-label">{{ __('auth_key') }}</label>
                        <input type="text" required class="form-control rounded-2" id="authKey" name="fast_2_auth_key" value="{{ isDemoMode() ? '******************' : stringMasking(setting('fast_2_auth_key'),'*',3) }}">
                        <div class="nk-block-des text-danger">
                            <p class="fast_2_auth_key_error error"></p>
                        </div>
                    </div>
                </div>
                <!-- End Auth Key -->

                <div
                    class="col-lg-12 fast2_div">
                    <div class="mb-4">
                        <label for="entityID" class="form-label">{{ __('entity_id') }}</label>
                        <input type="text" class="form-control rounded-2" required id="entityID" name="fast_2_entity_id" value="{{ isDemoMode() ? '******************' : stringMasking(setting('fast_2_entity_id'),'*',3) }}">
                        <div class="nk-block-des text-danger">
                            <p class="fast_2_entity_id_error error"></p>
                        </div>
                    </div>
                </div>
                <!-- End Entity ID -->

                <div
                    class="col-lg-12 fast2_div">
                    <div class="mb-4">
                        <label for="route" class="form-label">{{ __('route') }}</label>
                        <div class="select-type-v2">
                            <select id="route" class="form-select form-select-lg mb-3 without_search"
                                    name="fast_2_route" required>
                                <option value="dlt_manual" {{ stringMasking(setting('fast_2_route'),'*',3) == 'dlt_manual' ? 'selected' : ''}}>{{ __('dlt_manual') }}</option>
                                <option value="promotional_use" {{ stringMasking(setting('fast_2_route'),'*',3) == 'promotional_use' ? 'selected' : ''}}>{{ __('promotional_use') }}</option>
                                <option value="transactional_use" {{ stringMasking(setting('fast_2_route'),'*',3) == 'transactional_use' ? 'selected' : ''}}>{{ __('transactional_use') }}</option>
                            </select>
                            <div class="nk-block-des text-danger">
                                <p class="fast_2_route_error error"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Route -->

                <div
                    class="col-lg-12 fast2_div">
                    <div class="mb-4">
                        <label for="language" class="form-label">{{ __('language') }}</label>
                        <div class="select-type-v2">
                            <select id="language" class="form-select form-select-lg mb-3 without_search"
                                    name="fast_2_language" required>
                                <option value="english" {{ stringMasking(setting('fast_2_language'),'*',3) == 'english' ? 'selected' : ''}}>{{ __('english') }}</option>
                                <option value="Unicode" {{ stringMasking(setting('fast_2_language'),'*',3) == 'Unicode' ? 'selected' : ''}}>{{ __('unicode') }}</option>
                            </select>
                            <div class="nk-block-des text-danger">
                                <p class="fast_2_language_error error"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Language -->

                <div
                    class="col-lg-12 fast2_div">
                    <div class="mb-4">
                        <label for="senderID" class="form-label">{{ __('sender_id') }}</label>
                        <input type="text" class="form-control rounded-2" required id="senderID" name="fast_2_sender_id" value="{{ isDemoMode() ? '******************' : stringMasking(setting('fast_2_sender_id'),'*',3) }}">
                        <div class="nk-block-des text-danger">
                            <p class="fast_2_sender_id_error error"></p>
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
