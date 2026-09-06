<ul class="d-flex justify-content-cneter gap-4">
    <li class="d-flex justify-content-center">
        <a href="" data-bs-toggle="modal" data-bs-target="#edit-shop"
        class="dropdown-item shop-update"
        data-url="{{ Sentinel::getUser()->user_type == 'merchant' ? '/merchant/shop/edit' : '/staff/shop-edit' }}"
        data-id="{{ $shop->id }}">
        <i class="las la-edit"></i>
        </a>
    </li>
    <li>
        @if (!$shop->default)
            <div class="tb-odr-btns d-md-inline d-flex justify-content-cneter">
                <a href="javascript:void(0);"
                    onclick="delete_row('{{ Sentinel::getUser()->user_type == 'merchant' ? '/merchant/shop/delete/' : '/staff/shop/delete/' }}', {{ $shop->id }})"
                    id="delete-btn" class="dropdown-item">
                    <i class="las la-trash-alt"></i>
                </a>
            </div>
        @endif
    </li>
</ul>
