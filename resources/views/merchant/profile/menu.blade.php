<div class="card-inner p-0">
    <ul class="nav pb-12 mb-20" id="pills-tab" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="{{strpos(url()->current(),'merchant/profile') || strpos(url()->current(),'staff/profile') ? 'active':''}}"
               href="{{ Sentinel::getUser()->user_type == 'merchant' ? route('merchant.profile') : route('merchant.staff.profile') }}">
                <span>{{__('personal_information')}}</span>
            </a>
        </li>
        @if(Sentinel::getUser()->user_type == 'merchant' || (hasPermission('manage_company_information')))
            <li class="nav-item" role="presentation">
                <a class="{{strpos(url()->current(),'merchant/company') || strpos(url()->current(),'staff/company') ? 'active':''}}"
                   href="{{ Sentinel::getUser()->user_type == 'merchant' ? route('merchant.company') : route('merchant.staff.company') }}">
                    <span>{{__('company_information')}}</span>
                </a>
            </li>
        @endif
{{--                <li><a class="{{strpos(url()->current(),'merchant/notifications')? 'active':''}}" href="{{route('merchant.notifications')}}"><i class="icon ni ni-bell"></i><span>{{__('notifications')}}</span></a></li>--}}
        <li class="nav-item" role="presentation">
            <a class="{{strpos(url()->current(),'merchant.account-activity') || strpos(url()->current(),'staff/account-activity')? 'active':''}}"
               href="{{ Sentinel::getUser()->user_type == 'merchant' ? route('merchant.account-activity') : route('merchant.staff.account-activity')}}">
                <span>{{__('login_activity')}}</span>
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="{{strpos(url()->current(),'merchant/security-settings') || strpos(url()->current(),'staff/security-settings')? 'active':''}}"
               href="{{Sentinel::getUser()->user_type == 'merchant' ? route('merchant.security-settings') : route('merchant.staff.security-settings')}}">
                <span>{{__('security_settings')}}</span>
            </a>
        </li>
        @if(Sentinel::getUser()->user_type == 'merchant' || (hasPermission('manage_payment_accounts')))
            <li class="nav-item" role="presentation">
                <a class="{{strpos(url()->current(),'merchant/payment/accounts') || strpos(url()->current(),'staff/payment/accounts') ? 'active':''}}"
                   href="{{Sentinel::getUser()->user_type == 'merchant' ? route('merchant.payment.accounts') : route('merchant.staff.payment.accounts')}}">
                    <span>{{__('payment_accounts')}}</span>
                </a>
            </li>
        @endif
        @if(Sentinel::getUser()->user_type == 'merchant' || (hasPermission('delivery_charge')))
            <li class="nav-item" role="presentation">
                <a class="{{strpos(url()->current(),'merchant/charge') || strpos(url()->current(),'staff/charge') ? 'active':''}}"
                   href="{{Sentinel::getUser()->user_type == 'merchant' ? route('merchant.charge') : route('merchant.staff.charge')}}">
                    <span>{{__('delivery_charge')}}</span>
                </a>
            </li>
        @endif
        @if(Sentinel::getUser()->user_type == 'merchant' || (hasPermission('cash_on_delivery_charge')))
            <li class="nav-item" role="presentation">
                <a class="{{strpos(url()->current(),'merchant/cod-charge') || strpos(url()->current(),'staff/cod-charge') ? 'active':''}}"
                   href="{{Sentinel::getUser()->user_type == 'merchant' ? route('merchant.cod.charge') : route('merchant.staff.cod.charge')}}">
                    <span>{{__('cash_on_delivery_charge')}}</span>
                </a>
            </li>
        @endif
        @if(Sentinel::getUser()->user_type == 'merchant' || (hasPermission('manage_shops')))
            <li class="nav-item" role="presentation">
                <a class="{{strpos(url()->current(),'merchant/shops') || strpos(url()->current(),'staff/shops') ? 'active':''}}"
                   href="{{Sentinel::getUser()->user_type == 'merchant' ? route('merchant.shops') : route('merchant.staff.shops')}}">
                    <span>{{__('shops')}}</span>
                </a>
            </li>
        @endif
        @if(Sentinel::getUser()->user_type == 'merchant' && @settingHelper('preferences')->where('title','read_merchant_api')->first()->merchant)
            <li class="nav-item" role="presentation">
                <a class="{{strpos(url()->current(),'merchant/api-credentials')? 'active':''}}"
                   href="{{route('merchant.api.credentials')}}">
                    <span>{{__('api_credentials')}}</span>
                </a>
            </li>
        @endif
        <li class="nav-item" role="presentation">
            <a href="javascript:void(0);" onclick="logout_user_devices('/logout-other-devices', '')" id="delete-btn">
                <span> {{__('logout_from_other_devices')}} </span>
            </a>
        </li>
    </ul>
</div>





