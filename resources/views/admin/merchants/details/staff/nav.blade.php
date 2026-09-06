<ul class="nav pb-12 mb-20" id="pills-tab" role="tablist">
    <li class="nav-item">
        <a class="nav-link  ps-0 {{strpos(url()->current(),'merchant-staff/personal-info') ? 'active':''}}"
           href="{{route('detail.merchant.staff.personal.info', $staff->id)}}">
            <span>{{__('personal_information')}}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link  ps-0 {{strpos(url()->current(),'merchant-staff-account-activity') ? 'active':''}}"
           href="{{ route('detail.merchant.staffs.account-activity', $staff->id) }}">
            <span>{{__('login_activity')}}</span>
        </a>
    </li>
    @if(hasPermission('user_logout_from_devices'))
        <li class="nav-item">
            <a href="javascript:void(0);" class="nav-link  ps-0 " onclick="logout_user_devices('logout-user-all-devices/',{{$staff->id}})" id="delete-btn">
                 <span> {{__('logout_from_all_devices')}} </span>
            </a>
        </li>
    @endif
</ul>
