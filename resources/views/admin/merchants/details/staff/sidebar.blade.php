
<div class="col-xxl-3 col-lg-4 col-md-4">
    <div class="bg-white redious-border py-3 py-sm-30 mb-30">
        <div class="email-tamplate-sidenav">
            <div>
                <div class="user-info-panel align-items-center justify-content-center mb-3">
                    <div class="profile-img align-items-center justify-content-center d-flex mb-2">
                        <img src="{{ getFileLink('80X80', $staff->image_id) }}" alt="{{$staff->first_name}}">
                    </div>
                    <div class="user-info d-flex justify-content-center align-items-center">
                        <div>
                            <h4 class="text-center">{{$staff->first_name.' '.$staff->last_name}}</h4>
                            <span class="text-center">{{$staff->email}}</span>
                            <div class="user-balance text-center {{ $staff->balance($staff->id) < 0  ? 'text-danger': '' }}">{{ format_price($staff->balance($staff->id)) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <ul class="default-sidenav">
                <li>
                    <a href="{{route('detail.merchant.staff.personal.info', $staff->id)}}" class="{{ request()->routeIs('detail.merchant.staff.personal.info') ? 'active' : '' }}">
                        <span class="icon"><i class="las la-feather"></i></span>
                        <span>{{ __('personal_information') }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('detail.merchant.staffs.account-activity', $staff->id) }}" class="{{ request()->routeIs('detail.merchant.staffs.account-activity') ? 'active' : '' }}">
                        <span class="icon"><i class="las la-palette"></i></span>
                        <span>{{ __('login_activity') }}</span>
                    </a>
                </li>
                @if(hasPermission('user_logout_from_devices'))
                    <li>
                        <a href="javascript:void(0);" class="{{ request()->routeIs('logout-user-all-devices') ? 'active' : '' }}" onclick="logout_user_devices('logout-user-all-devices/',{{$staff->id}})" id="delete-btn">
                            <span class="icon"><i class="las la-heading"></i></span>
                            <span>{{ __('logout_from_all_devices') }}</span>
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</div>
@include('common.script')
