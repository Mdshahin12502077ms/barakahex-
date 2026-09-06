<div class="col-xxl-3 col-lg-4 col-md-4">
    <div class="bg-white redious-border py-3 py-sm-30 mb-30">
        <div class="email-tamplate-sidenav">
            <div>
                <div class="user-info-panel align-items-center d-flex gap-3 justify-content-center mb-3">
                    <div class="users-img">
                        <img src="{{getFileLink('80X80', $user->image_id)}}" alt="{{$user->first_name}}" class="redious-border">
                    </div>
                    <div class="user-info">
                        <h4>{{$staff->first_name.' '.$staff->last_name}}</h4>
                        <span>{{$staff->email}}</span>
                        <div class="user-balance {{ $staff->balance($staff->id) < 0  ? 'text-danger': '' }}">{{ format_price($staff->balance($staff->id)) }}
                        </div>
                    </div>
                </div>
            </div>
            <ul class="default-sidenav">
                <li>
                    <a href="{{route('detail.staff.personal.info', $user->id)}}" class="{{ request()->routeIs('detail.merchant.staff.personal.info') ? 'active' : '' }}">
                        <span class="icon"><i class="las la-feather"></i></span>
                        <span>{{ __('personal_information') }}</span>
                    </a>
                </li>
                @if(hasPermission('account_read') && !blank($user->accounts($user->id)))
                    <li>
                        <a href="{{route('staff.accounts', $user->id)}}" class="{{ request()->routeIs('detail.merchant.staffs.account-activity') ? 'active' : '' }}">
                            <span class="icon"><i class="las la-palette"></i></span>
                            <span>{{ __('accounts') }}</span>
                        </a>
                    </li>
                @endif
                @if(hasPermission('user_account_activity_read'))
                    <li>
                        <a href="{{route('detail.staff.account-activity', $user->id)}}" class="{{ request()->routeIs('detail.merchant.staffs.account-activity') ? 'active' : '' }}">
                            <span class="icon"><i class="las la-palette"></i></span>
                            <span>{{ __('login_activity') }}</span>
                        </a>
                    </li>
                @endif
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
