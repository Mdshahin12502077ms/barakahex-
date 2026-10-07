<input type="hidden" class="text_copied" value="{{ __('text_copied') }}">
<input type="hidden" class="text_copied_fail" value="{{ __('text_copied_fail') }}">@php
    $user = Sentinel::getUser();
@endphp

@if($user && ($user->user_type == 'delivery_man' || $user->user_type == 'delivery'))


    <div class="deliveryman-bottom-nav">
        <a href="{{ route('deliveryman.deliveries') }}" class="nav-item {{ menuActivation(['delivery-man/deliveries*', 'delivery-man/parcel-detail/*'], 'active') }}">
            <i class="las la-shipping-fast"></i>
            <span>{{ __('Deliveries') }}</span>
        </a>

        <a href="{{ route('deliveryman.pickups') }}" class="nav-item {{ menuActivation(['delivery-man/pickups*'], 'active') }}">
            <i class="las la-truck-loading"></i>
            <span>{{ __('Pickups') }}</span>
        </a>

        <div class="nav-item-center">
            <a href="{{ route('deliveryman.dashboard') }}" class="floating-home {{ menuActivation(['delivery-man/dashboard'], 'active') }}">
                <i class="las la-home"></i>
                <span>{{ __('Home') }}</span>
            </a>
        </div>

        <a href="{{ route('deliveryman.return.tasks') }}" class="nav-item {{ menuActivation(['delivery-man/return-tasks*'], 'active') }}">
            <i class="las la-undo-alt"></i>
            <span>{{ __('Returns') }}</span>
        </a>

        <a href="{{ route('deliveryman.profile') }}" class="nav-item {{ menuActivation(['delivery-man/profile*'], 'active') }}">
            <i class="las la-user-circle"></i>
            <span>{{ __('Profile') }}</span>
        </a>
    </div>
@endif
