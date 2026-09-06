@if(hasPermission('merchant_shop_update'))
<div class="text-center">
    <a href="" data-bs-toggle="modal" data-bs-target="#edit-shop" class="fs-4 shop-update" data-id="{{ $shop->id }}"><i class="icon las la-edit"></i></a>
</div>

@endif
