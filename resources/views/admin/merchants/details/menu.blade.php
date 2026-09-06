
<div class="col-xxl-3 col-lg-4 col-md-4">
    <div class="bg-white redious-border py-3 py-sm-30 mb-30">
        <div class="email-tamplate-sidenav">
            <div>
                <div class="user-info-panel  align-items-center justify-content-center gap-3 mb-3">
                    <div class="profile-img align-items-center justify-content-center d-flex mb-2">
                        <img src="{{ getFileLink('80X80', $merchant->user->image_id) }}">
                    </div>
                    <div class="user-info d-flex justify-content-center align-items-center">
                        <div>
                            <h4 class="text-center">{{$merchant->user->first_name.' '.$merchant->user->last_name}}</h4>
                            <span class="text-center">{{$merchant->user->email}}</span>
                            <div class="user-balance text-center {{ $merchant->balance($merchant->id) < 0  ? 'text-danger': '' }}">{{ format_price($merchant->balance($merchant->id)) }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <ul class="default-sidenav">
                <li>
                    <a href="{{ route('detail.merchant.personal.info', $merchant->id) }}" class="{{ request()->routeIs('detail.merchant.personal.info') ? 'active' : '' }}">
                        <span class="icon"><i class="las la-feather"></i></span>
                        <span>{{ __('personal_information') }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('detail.merchant.company', $merchant->id) }}" class="{{ request()->routeIs('detail.merchant.company') ? 'active' : '' }}">
                        <span class="icon"><i class="las la-palette"></i></span>
                        <span>{{ __('company_information') }}</span>
                    </a>
                </li>
                @if (hasPermission('merchant_staff_read'))
                    <li>
                        <a href="{{ route('detail.merchant.staffs', $merchant->id) }}" class="{{ request()->routeIs('detail.merchant.staffs') ? 'active' : '' }}">
                            <span class="icon"><i class="las la-heading"></i></span>
                            <span>{{ __('staffs') }}</span>
                        </a>
                    </li>
                @endif
                @if (hasPermission('merchant_account_activity_read'))
                    <li>
                        <a href="{{ route('detail.merchant.account-activity', $merchant->id) }}" class="{{ request()->routeIs('detail.merchant.account-activity') ? 'active' : '' }}">
                            <span class="icon"><i class="las la-hand-point-up"></i></span>
                            <span>{{ __('login_activity') }}</span>
                        </a>
                    </li>
                @endif
                @if (hasPermission('merchant_payment_account_read'))
                    <li>
                        <a href="{{ route('detail.merchant.payment.accounts', $merchant->id) }}" class="{{ request()->routeIs(['detail.merchant.payment.accounts', 'detail.merchant.payment.bank.edit', 'detail.merchant.payment.others.edit']) ? 'active' : '' }}">
                            <span class="icon"><i class="las la-memory"></i></span>
                            <span>{{ __('payout_accounts') }}</span>
                        </a>
                    </li>
                @endif
                @if (hasPermission('merchant_charge_read'))
                    <li>
                        <a href="{{ route('detail.merchant.charge', $merchant->id) }}" class="{{ request()->routeIs('detail.merchant.charge') ? 'active' : '' }}">
                            <span class="icon"><i class="las la-memory"></i></span>
                            <span>{{ __('delivery_charge') }}</span>
                        </a>
                    </li>
                @endif
                @if (hasPermission('merchant_cod_charge_read'))
                    <li>
                        <a href="{{ route('detail.merchant.cod.charge', $merchant->id) }}" class="{{ request()->routeIs('detail.merchant.cod.charge') ? 'active' : '' }}">
                            <span class="icon"><i class="las la-sticky-note"></i></span>
                            <span>{{ __('cash_on_delivery_charge') }}</span>
                        </a>
                    </li>
                @endif
                @if (hasPermission('merchant_payment_logs_read'))
                    <li>
                        <a href="{{ route('detail.merchant.statements', $merchant->id) }}" class="{{ request()->routeIs('detail.merchant.statements') ? 'active' : '' }}">
                            <span class="icon"><i class="las la-wallet"></i></span>
                            <span>{{ __('payout_logs') }}</span>
                        </a>
                    </li>
                @endif
                @if (hasPermission('merchant_shop_read'))
                    <li>
                        <a href="{{ route('detail.merchant.shops', $merchant->id) }}" class="{{ request()->routeIs('detail.merchant.shops') ? 'active' : '' }}">
                            <span class="icon"><i class="las la-store"></i></span>
                            <span>{{ __('shops') }}</span>
                        </a>
                    </li>
                @endif
                @if (hasPermission('merchant_api_credentials_read'))
                    <li>
                        <a href="{{ route('detail.merchant.permissions', $merchant->id) }}" class="{{ request()->routeIs('detail.merchant.permissions') ? 'active' : '' }}">
                            <span class="icon"><i class="las la-shield-alt"></i></span>
                            <span>{{ __('permissions') }} & {{ __('api_credentials') }}</span>
                        </a>
                    </li>
                @endif
                @if (hasPermission('user_logout_from_devices'))
                    <li>
                        <a href="javascript:void(0);"
                        onclick="logout_user_devices('logout-user-all-devices/', {{ $merchant->user->id }})" id="delete-btn" class="{{ request()->routeIs('logout-user-all-devices') ? 'active' : '' }}">
                            <span class="icon"><i class="la la-sign-out"></i></span>
                            <span> {{ __('logout_from_all_devices') }} </span>
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</div>
@include('common.script')
