
<div class="col-xxl-3 col-lg-4 col-md-4">
    <div class="bg-white redious-border py-3 py-sm-30 mb-30">
        <div class="email-tamplate-sidenav">
            <div>
                <div class="user-info-panel align-items-center justify-content-center mb-3">
                    <div class="profile-img align-items-center justify-content-center d-flex mb-2">
                        <img class="selected-img" src="{{ getFileLink('80X80', $staff->image_id) }}"
                        alt="favicon" class="redious-border">
                    </div>
                    <div class="user-info d-flex justify-content-center align-items-center">
                        <div>
                            <h4 class="text-center">{{ $staff->first_name . ' ' . $staff->last_name }}</h4>
                            <span class="text-center">{{ $staff->email }}</span>
                            <div class="text-center {{ $staff->balance($staff->id) < 0 ? 'text-danger' : '' }}">
                                {{ format_price($staff->balance($staff->id)) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <ul class="default-sidenav">
                <li>
                    <a href="{{route('merchant.staff.personal.info', $staff->id)}}" class="{{ request()->routeIs('merchant.staff.personal.info') ? 'active' : '' }}">
                        <span class="icon"><i class="las la-feather"></i></span>
                        <span>{{ __('personal_informations') }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('merchant.staffs.account-activity', $staff->id) }}" class="{{ request()->routeIs('merchant.staffs.account-activity') ? 'active' : '' }}">
                        <span class="icon"><i class="las la-palette"></i></span>
                        <span>{{ __('company_information') }}</span>
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0);" onclick="logout_user_devices('/merchant/logout-staff-all-devices/{{$staff->id}}','')" id="delete-btn" class="{{ request()->routeIs('detail.merchant.staffs') ? 'active' : '' }}">
                        <span class="icon"><i class="lab la-css3-alt"></i></span>
                        <span>{{ __('logout_from_all_devices') }}</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>

@include('common.script')
