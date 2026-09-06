
<div class="col-xxl-3 col-lg-4 col-md-4">
    <div class="bg-white redious-border py-3 py-sm-30 mb-30">
        <div class="email-tamplate-sidenav">
            <ul class="default-sidenav">
                <li>
                    <a href="{{ route('sms.setting') }}" class="{{ request()->routeIs('sms.setting') ? 'active' : '' }}">
                        <span class="icon"><i class="icon las la-sms"></i></span>
                        <span>{{ __('sms_gateway') }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('preference.setting') }}" class="{{ request()->routeIs('preference.setting') ? 'active' : '' }}">
                        <span class="icon"><i class="icon las la-compress"></i></span>
                        <span>{{ __('preference') }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('charges.setting') }}" class="{{ request()->routeIs('charges.setting') ? 'active' : '' }}">
                        <span class="icon"><i class="icon las la-wallet"></i></span>
                        <span>{{ __('charges') }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('packaging.charge.setting') }}" class="{{ request()->routeIs('packaging.charge.setting') ? 'active' : '' }}">
                        <span class="icon"><i class="icon las la-wallet"></i></span>
                        <span>{{ __('packaging_type_and_charges') }}</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>

@include('common.script')

