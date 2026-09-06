<span>
    <div class="setting-check">
            <input type="checkbox"
                class="custom-control-input default-change"
                {{ $shop->default ? 'checked' : '' }}
                value="{{ $shop->id }}"
                data-merchant="{{ Sentinel::getUser()->user_type == 'merchant' ? Sentinel::getUser()->merchant->id : Sentinel::getUser()->merchant_id }}"
                data-url="{{ Sentinel::getUser()->user_type == 'merchant' ? route('merchant.default.shop') : route('merchant.staff.default.shop') }}"
                id="customSwitch2-{{ $shop->id }}">
            <label class="custom-control-label"
                for="customSwitch2-{{ $shop->id }}"></label>
    </div>
</span>
