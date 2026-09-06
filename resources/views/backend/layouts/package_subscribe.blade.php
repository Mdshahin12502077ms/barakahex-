<ul class="dropdown-menu popup-card">
    <li><span>{{ __('notifications') }}</span></li>
    <div class="pusher-notification">
        @if (!empty($notifications))
            @foreach ($notifications as $notification)
                @php
                    $route = '';
                    if (Sentinel::getUser()->user_type == 'staff') {
                        $route = route('notification.update', ['id' => $notification->notification_user_id]);
                    } elseif (Sentinel::getUser()->user_type == 'merchant') {
                        $route = route('merchant.notification.update', ['id' => $notification->notification_user_id]);
                    } else {
                        $route = route('merchant.staff.notification.update', ['id' => $notification->notification_user_id]);
                    }
                @endphp
                <li>
                    <a class="dropdown-item" href="{{ $route }}" style="text-align: left">
                        <div class="notification-content">
                            <img class="user-avater"
                                src="{{ optional(optional($notification->createdBy)->image)->image_small_two ? asset(optional(optional($notification->createdBy)->image)->image_small_two) : getFileLink('80X80', []) }}">
                            <div class="notification-text">
                                <h6>{{ @$notification->createdBy->first_name . ' ' . @$notification->createdBy->last_name}}</h6>
                                <p>{{ @$notification->description }}</p>
                            </div>
                            <span class="notification-time">{{ date('H A', strtotime($notification->created_at)) }}</span>
                        </div>
                    </a>
                </li>
            @endforeach
        @endif
    </div>
    <li><a class="dropdown-item text-center" href="{{ route('all.notifications') }}">{{ __('read_all') }}</a></li>
</ul>