
<div class="col-xxl-3 col-lg-4 col-md-4">
    <div class="bg-white redious-border py-3 py-sm-30 mb-30">
        <div class="email-tamplate-sidenav">
            <div>
                <div class="user-info-panel align-items-center justify-content-center mb-3">
                    <div class="profile-img align-items-center justify-content-center d-flex mb-2">
                        <img src="{{ getFileLink('80X80', \Sentinel::getUser()->image_id) }}" class="redious-border">
                    </div>
                    <div class="user-info d-flex justify-content-center align-items-center">
                        <div>
                            <h4 class="text-center">{{ @\Sentinel::getUser()->merchant->company }}</h4>
                            <span class="text-center">{{ \Sentinel::getUser()->email }}</span>
                            @if (Sentinel::getUser()->user_type == 'merchant')
                                <div
                                    class="user-balance text-center {{ Sentinel::getUser()->merchant->balance(\Sentinel::getUser()->merchant->id) < 0 ? 'text-danger' : '' }}">
                                    {{ format_price(Sentinel::getUser()->merchant->balance(\Sentinel::getUser()->merchant->id)) }}
                                </div>
                            @else
                                <div
                                    class="user-balance text-center {{ Sentinel::getUser()->balance(\Sentinel::getUser()->id) < 0 ? 'text-danger' : '' }}">
                                    {{ format_price(Sentinel::getUser()->balance(\Sentinel::getUser()->id)) }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <ul class="default-sidenav">
                @if(Sentinel::getUser()->user_type == 'merchant' || (hasPermission('manage_company_information')))
                    <li>
                        <a href="{{ Sentinel::getUser()->user_type == 'merchant' ? route('merchant.company') : route('merchant.staff.company') }}" class="{{ request()->routeIs(['merchant.company', 'merchant.staff.company']) ? 'active' : '' }}">
                            <span class="icon"><i class="las la-palette"></i></span>
                            <span>{{ __('company_information') }}</span>
                        </a>
                    </li>
                @endif
                @if(Sentinel::getUser()->user_type == 'merchant' || (hasPermission('manage_payment_accounts')))
                    <li>
                        <a href="{{Sentinel::getUser()->user_type == 'merchant' ? route('merchant.payment.method') : route('merchant.staff.payment.method')}}" class="{{ request()->routeIs(['merchant.payment.method', 'merchant.staff.payment.method']) ? 'active' : '' }}">
                            <span class="icon"><i class="las la-money-bill-alt"></i></span>
                            <span>{{ __('default_payout') }}</span>
                        </a>
                    </li>
                @endif
                @if(Sentinel::getUser()->user_type == 'merchant' || (hasPermission('manage_payment_accounts')))
                    <li>
                        <a href="{{Sentinel::getUser()->user_type == 'merchant' ? route('merchant.payment.accounts') : route('merchant.staff.payment.accounts')}}" class="{{ request()->routeIs(['merchant.payment.accounts', 'merchant.staff.payment.accounts']) ? 'active' : '' }}">
                            <span class="icon"><i class="las la-money-check"></i></span>
                            <span>{{ __('bank') }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{Sentinel::getUser()->user_type == 'merchant' ? route('merchant.mfs.accounts') : route('merchant.staff.mfs.accounts')}}" class="{{ request()->routeIs(['merchant.mfs.accounts', 'merchant.staff.mfs.accounts']) ? 'active' : '' }}">
                            <span class="icon"><i class="las la-mobile"></i></span>
                            <span>{{ __('mfs') }}</span>
                        </a>
                    </li>
                @endif
                @if (hasPermission('user_logout_from_devices'))
                    <li>
                        <a
                        onclick="logout_user_devices('logout-user-all-devices/', {{ $merchant->user->id }})" href="javascript:void(0);" onclick="logout_user_devices('/logout-other-devices', '')" id="delete-btn">
                            <span class="icon"><i class="lab la-css3-alt"></i></span>
                            <span> {{ __('logout_from_all_devices') }} </span>
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</div>




@include('merchant.profile.modals')
@include('common.script')
@push('css')
    <style>
        .default-tab-list.default-tab-list-v2 ul li a.nav-link.active::after {
            border: 0px;
        }
    </style>
@endpush
