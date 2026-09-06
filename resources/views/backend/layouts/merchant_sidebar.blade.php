@php
    $user = Sentinel::getUser();
@endphp
<header class="navbar-dark-v1">
    <div class="header-position">
        <span class="sidebar-toggler">
            <i class="las la-times"></i>
        </span>
        <div class="dashboard-logo d-flex justify-content-center align-items-center py-20">
            <a class="logo" href="{{ route('merchant.dashboard') }}">
                <img src="{{ setting('admin_logo') && @is_file_exists(setting('admin_logo')['original_image']) ? get_media(setting('admin_logo')['original_image']) : get_media('images/default/logo/logo_light.png') }}"
                    alt="Logo">
            </a>
            <a class="logo-icon" href="{{ route('merchant.dashboard') }}">
                <img src="{{ setting('admin_mini_logo') && @is_file_exists(setting('admin_mini_logo')['original_image']) ? get_media(setting('admin_mini_logo')['original_image']) : get_media('images/default/logo/logo_mini_light.png') }}"
                    alt="Logo">
            </a>
        </div>
        <nav class="side-nav">
            <ul>
                <li>
                    <a href="{{ route('merchant.dashboard') }}">
                        <i class="las la-tachometer-alt"></i>
                        <span>{{ __('dashboard') }}</span>
                    </a>
                </li>

                @if ($user && $user->user_type == 'merchant')
                    <li class="{{ menuActivation(['merchant/parcels', 'merchant/parcel/*'], 'active') && request('status') != 'returned-to-merchant' ? 'active' : '' }}">
                        <a href="{{ route('merchant.parcel') }}">
                            <i class="icon las la-box"></i>
                            <span>{{ __('parcels') }}</span>
                        </a>
                    </li>
                    <li class="{{ request('status') == 'returned-to-merchant' || (isset($slug) && $slug == 'returned-to-merchant') ? 'active' : '' }}">
                        <a href="{{ route('merchant.parcel.filtering', 'returned-to-merchant') }}">
                            <i class="icon las la-undo-alt"></i>
                            <span>{{ __('returned_parcels') }}</span>
                        </a>
                    </li>
                    <li
                        class="{{ menuActivation(['merchant/withdraws', 'merchant/statements', 'merchant/payment-invoice/*', 'merchant/request-withdraw'], 'active') }}">
                        <a href="{{ route('merchant.withdraw') }}">
                            <i class="icon las la-wallet"></i>
                            <span>{{ __('merchant_settlement') }}</span>
                        </a>
                    </li>
                @endif

                @if (Sentinel::getUser()->user_type == 'merchant')
                    <li class="{{ menuActivation(['merchant/staffs', 'merchant/staff/*'], 'active') }}">
                        <a href="{{ route('merchant.staffs') }}">
                            <i class="icon las la-users"></i>
                            <span>{{ __('staffs') }}</span>
                        </a>
                    </li>


                    <li class="{{ menuActivation(['merchant/charge'], 'active') }}">
                        <a href="{{ route('merchant.charge') }}">
                            <i class="las la-dollar-sign"></i>
                            <span>{{ __('charge') }}</span>
                        </a>
                    </li>
                    <li class="{{ menuActivation(['merchant/shops'], 'active') }}">
                        <a href="{{ route('merchant.shops') }}">
                            <i class="las la-store"></i>
                            <span>{{ __('shops') }}</span>
                        </a>
                    </li>
                    {{-- <li
                        class="{{ menuActivation(['merchant/products*', 'merchant/warehouse*', 'merchant/stock/index', 'merchant/stock/stock_in'], 'active') }}">
                        --}}
                        {{-- <a href="#whm-menu" class="dropdown-icon" data-bs-toggle="collapse" role="button" --}} {{--
                            aria-expanded="{{ menuActivation(['merchant/products*', 'merchant/warehouse*', 'merchant/stock/index', 'merchant/stock/stock_in', 'admin/user-create'], 'true', 'false') }}"
                            --}} {{-- aria-controls="staff-menu">--}}
                            {{-- <i class="las la-warehouse"></i>--}}
                            {{-- <span>{{ __('whm') }}</span>--}}
                            {{-- </a>--}}
                        {{-- <ul id="whm-menu" --}} {{--
                            class="sub-menu collapse {{ menuActivation(['merchant/products*', 'merchant/warehouse*', 'merchant/stock/index', 'merchant/stock/stock_in', 'admin/user-create'], 'show') }}">
                            --}}
                            {{-- <li>--}}
                                {{-- <a href="{{ route('merchant.products') }}" --}} {{--
                                    class="{{ menuActivation(['merchant/products*'], 'active') }}"><span>{{ __('products')
                                        }}</span></a>--}}
                                {{-- </li>--}}
                            {{-- <li>--}}
                                {{-- <a href="{{ route('merchant.warehouse') }}" --}} {{--
                                    class="{{ menuActivation(['merchant/warehouse*'], 'active') }}"><span>{{ __('warehouse')
                                        }}</span></a>--}}
                                {{-- </li>--}}
                            {{-- <li>--}}
                                {{-- <a href="{{ route('merchant.stock.list',['type' => 'stock_in']) }}" --}} {{--
                                    class="{{ menuActivation(['merchant/stock/stock_in', 'admin/user/*'], 'active') }}"><span>{{
                                        __('stock_in') }}</span></a>--}}
                                {{-- </li>--}}
                            {{-- <li>--}}
                                {{-- <a href="{{ route('merchant.stock.list',['type' => 'index']) }}" --}} {{--
                                    class="{{ menuActivation(['merchant/stock/index'], 'active') }}"><span>{{
                                        __('in_out_history') }}</span></a>--}}
                                {{-- </li>--}}

                            {{-- </ul>--}}
                        {{-- </li>--}}
                    @if (@settingHelper('preferences')->where('title', 'read_merchant_api')->first()->merchant)
                        <li class="{{ menuActivation(['merchant/api-credentials'], 'active') }}">
                            <a href="{{ route('merchant.api.credentials') }}">
                                <i class="lab la-js-square"></i>
                                <span>{{ __('api_credentials') }}</span>
                            </a>
                        </li>
                    @endif
                    <li class="{{ menuActivation(['merchant/company', 'merchant/account/*'], 'active') }}">
                        <a href="{{ route('merchant.company') }}">
                            <i class="icon las la-user-alt"></i>
                            <span>{{ __('settings') }}</span>
                        </a>
                    </li>
                    <li class="{{ menuActivation(['notifications'], 'active') }}">
                        <a href="{{ route('all.notifications') }}">
                            <i class="icon las la-bell"></i>
                            <span>{{ __('notifications') }}</span>
                        </a>
                    </li>
                @elseif(Sentinel::getUser()->user_type == 'merchant_staff')
                    @if (hasPermission('manage_parcel'))
                        <li
                            class="{{ menuActivation(['staff/parcels', 'staff/import', 'staff/request-parcel', 'staff/parcel-edit/*', 'staff/parcel-duplicate/*', 'staff/parcel-detail/*'], 'active') }}">
                            <a href="{{ route('merchant.staff.parcel') }}">
                                <i class="icon las la-box"></i>
                                <span>{{ __('parcel') }}</span>
                            </a>
                        </li>
                    @endif
                    @if (hasPermission('manage_payment'))
                        <li
                            class="{{ menuActivation(['staff/withdraws', 'staff/statements', 'staff/payment-invoice/*'], 'active') }}">
                            <a href="{{ route('merchant.staff.withdraw') }}">
                                <i class="icon las la-wallet"></i>
                                <span>{{ __('payout') }}</span>
                            </a>
                        </li>
                    @endif

                    @if (hasPermission('delivery_charge') || hasPermission('cash_on_delivery_charge'))
                        <li class="{{ menuActivation(['merchant/staff/charge'], 'active') }}">
                            <a href="{{ route('merchant.staff.charge') }}">
                                <i class="las la-memory"></i>
                                <span>{{ __('delivery_charge') }}</span>
                            </a>
                        </li>
                    @endif
                    @if (hasPermission('manage_shops'))
                        <li class="{{ menuActivation(['merchant/staff/shops'], 'active') }}">
                            <a href="{{ route('merchant.staff.shops') }}">
                                <i class="las la-store"></i>
                                <span>{{ __('shops') }}</span>
                            </a>
                        </li>
                    @endif
                    @if (hasPermission('manage_company_information') || hasPermission('manage_payment_accounts') || Sentinel::getUser()->user_type == 'merchant')
                        <li class="{{ menuActivation(['staff/company', '*admin/merchant-personal-info/'], 'active') }}">
                            <a href="{{ route('merchant.staff.company') }}">
                                <i class="icon las la-user-alt"></i>
                                <span>{{ __('Settings') }}</span>
                            </a>
                        </li>
                    @endif
                @endif
            </ul>
        </nav>
    </div>
</header>