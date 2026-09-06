<ul class="nav pb-12 mb-20" id="pills-tab" role="tablist">
    <li class="nav-item" role="presentation">
        <a class="nav-link  ps-0 {{strpos(url()->current(),'staff-personal-info') ? 'active':''}}"
           href="{{route('merchant.staff.personal.info', $staff->id)}}">
            <span>{{__('personal_information')}}</span>
        </a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link ps-0 {{strpos(url()->current(),'staff-account-activity') ? 'active':''}}"
           href="{{ route('merchant.staffs.account-activity', $staff->id) }}">
            <span>{{__('login_activity')}}</span>
        </a>
    </li>
    <li class="nav-item" role="presentation">
        <a href="javascript:void(0);" onclick="logout_user_devices('/merchant/logout-staff-all-devices/{{$staff->id}}','')" id="delete-btn">
            <span> {{__('logout_from_all_devices')}} </span>
        </a>
    </li>
</ul>
