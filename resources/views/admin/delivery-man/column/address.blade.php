
<div>
    {{$delivery_man->address}}
    <div>
        Driving License:
            <a href="{{ getFileLink('80X80', $delivery_man->driving_license)  }}" target="_blank"> <i class="icon  las la-external-link-alt"></i> {{ __('driving_license') }}</a>

    </div>
</div>
