@php
    $user = Sentinel::getUser();
@endphp
<header class="navbar-dark-v1">
    <div class="header-position">
        <span class="sidebar-toggler">
            <i class="las la-times"></i>
        </span>
        <div class="dashboard-logo d-flex justify-content-center align-items-center py-20">
            <a class="logo" href="{{ route('deliveryman.dashboard') }}">
                <img src="{{ setting('admin_logo') && @is_file_exists(setting('admin_logo')['original_image']) ? get_media(setting('admin_logo')['original_image']) : get_media('images/default/logo/logo_light.png') }}"
                    alt="Logo">
            </a>
            <a class="logo-icon" href="{{ route('deliveryman.dashboard') }}">
                <img src="{{ setting('admin_mini_logo') && @is_file_exists(setting('admin_mini_logo')['original_image']) ? get_media(setting('admin_mini_logo')['original_image']) : get_media('images/default/logo/logo_mini_light.png') }}"
                    alt="Logo">
            </a>
        </div>
        <nav class="side-nav">
            <ul>
                <li class="{{ menuActivation(['delivery-man/dashboard'], 'active') }}">
                    <a href="{{ route('deliveryman.dashboard') }}">
                        <i class="las la-tachometer-alt"></i>
                        <span>{{ __('dashboard') }}</span>
                    </a>
                </li>

                <li class="{{ menuActivation(['delivery-man/pickups*'], 'active') }}">
                    <a href="{{ route('deliveryman.pickups') }}">
                        <i class="las la-truck-loading"></i>
                        <span>{{ __('pickups') }}</span>
                    </a>
                </li>

                <li class="{{ menuActivation(['delivery-man/deliveries*', 'delivery-man/parcel-detail/*'], 'active') }}">
                    <a href="{{ route('deliveryman.deliveries') }}">
                        <i class="las la-shipping-fast"></i>
                        <span>{{ __('deliveries') }}</span>
                    </a>
                </li>

                <li class="{{ menuActivation(['delivery-man/return-tasks*'], 'active') }}">
                    <a href="{{ route('deliveryman.return.tasks') }}">
                        <i class="las la-undo-alt"></i>
                        <span>{{ __('return_tasks') }}</span>
                    </a>
                </li>

                

                <li class="{{ menuActivation(['delivery-man/accounts*'], 'active') }}">
                    <a href="{{ route('deliveryman.accounts') }}">
                        <i class="las la-wallet"></i>
                        <span>{{ __('cod_and_earnings') }}</span>
                    </a>
                </li>

                <li class="{{ menuActivation(['delivery-man/profile*'], 'active') }}">
                    <a href="{{ route('deliveryman.profile') }}">
                        <i class="las la-user-circle"></i>
                        <span>{{ __('profile') }}</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('logout') }}">
                        <i class="las la-sign-out-alt"></i>
                        <span>{{ __('logout') }}</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</header>
