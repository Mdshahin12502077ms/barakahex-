
    <div class="col-xxl-3 col-lg-4 col-md-4">
        <div class="bg-white redious-border py-3 py-sm-30 mb-30">
            <div class="email-tamplate-sidenav">
                <div>
                    <div class="user-info-panel align-items-center justify-content-center mb-3">
                        <div class="profile-img align-items-center justify-content-center d-flex mb-2">
                            {{-- @if(!blank($delivery_man->user->image) && file_exists($delivery_man->user->image->image_small_two)) --}}
                            <img src="{{getFileLink('80X80', $delivery_man->user->image_id)}}" alt="{{$delivery_man->user->first_name}}" class="redious-border">
                            {{-- @else
                                <img src="{{static_asset('admin/images/default/user40x40.jpg')}}" alt="{{$delivery_man->user->first_name}}" class="redious-border">
                            @endif --}}
                            {{-- <img src="{{ optional($delivery_man->user->image)->image_small_two ? asset(optional($delivery_man->user->image)->image_small_two) : getFileLink('80X80', []) }}"> --}}
                        </div>
                        <div class="user-info d-flex justify-content-center align-items-center">
                            <div>
                                <h4 class="text-center">{{$delivery_man->user->first_name.' '.$delivery_man->user->last_name}}</h4>
                                <span class="text-center">{{$delivery_man->user->email}}</span>
                                <div class="user-balance text-center {{ $delivery_man->balance($delivery_man->id) < 0  ? 'text-danger': '' }}">
                                    {{ format_price($delivery_man->balance($delivery_man->id)) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <ul class="default-sidenav">
                    <li>
                        <a href="{{route('detail.delivery.man.personal.info', $delivery_man->id)}}" class="{{ request()->routeIs('detail.delivery.man.personal.info') ? 'active' : '' }}">
                            <span class="icon"><i class="las la-feather"></i></span>
                            <span>{{ __('personal_information') }}</span>
                        </a>
                    </li>
                    @if(hasPermission('deliveryman_account_activity_read'))
                        <li>
                            <a href="{{route('detail.delivery.man.account-activity', $delivery_man->id)}}" class="{{ request()->routeIs('detail.delivery.man.account-activity') ? 'active' : '' }}">
                                <span class="icon"><i class="las la-palette"></i></span>
                                <span>{{ __('login_activity') }}</span>
                            </a>
                        </li>
                    @endif
                    @if(hasPermission('deliveryman_payment_logs_read'))
                        <li>
                            <a href="{{route('detail.delivery.man.statements', $delivery_man->id)}}" class="{{ request()->routeIs('detail.delivery.man.statements') ? 'active' : '' }}">
                                <span class="icon"><i class="las la-heading"></i></span>
                                <span>{{ __('payout_logs') }}</span>
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
    @include('common.script')

