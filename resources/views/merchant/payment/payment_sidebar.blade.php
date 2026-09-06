<div class="card-aside card-aside-left user-aside toggle-slide toggle-slide-left toggle-break-lg" data-content="userAside" data-toggle-screen="lg" data-toggle-overlay="true">
    <div class="card-inner-group" data-simplebar>
        <div class="card-inner">
            <div class="user-card">
                <div class="user-avatar bg-primary">
                    @if(!blank(\Sentinel::getUser()->image) && file_exists(\Sentinel::getUser()->image->image_small_two))
                        <img src="{{static_asset(\Sentinel::getUser()->image->image_small_two)}}" alt="{{\Sentinel::getUser()->first_name}}">
                    @else
                        <img src="{{static_asset('admin/images/default/user40x40.jpg')}}" alt="{{\Sentinel::getUser()->first_name}}">
                    @endif
                </div>
                <div class="user-info">
                    <span class="lead-text">{{\Sentinel::getUser()->first_name.' '.\Sentinel::getUser()->last_name}}</span>
                    <span class="sub-text">{{\Sentinel::getUser()->email}}</span>
                </div>
            </div>
        </div>
        <div class="card-inner p-0">
            <ul class="link-list-menu">
                <li>
                    <a class="{{strpos(url()->current(),'merchant/payment/accounts')? 'active':''}}" href="{{route('merchant.payment.accounts')}}">
                    <span>{{__('bank_account')}}</span>
                </a>
            </li>
                <li>
                    <a class="{{strpos(url()->current(),'merchant/payment/account/others')? 'active':''}}" href="{{route('merchant.payment.accounts.others')}}">
                        </em><span>{{__('others_account')}}</span>
                </a>
            </li>
            </ul>
        </div>
    </div>
</div>
@include('merchant.profile.modals')
