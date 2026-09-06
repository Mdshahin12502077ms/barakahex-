
@if(hasPermission('merchant_staff_update'))
    <div class="text-center">
        <a href="{{route('detail.merchant.staff.edit', $query->id)}}"  class="fs-4" data-id="{{ $query->id }}"><i class="icon las la-edit"></i></a>
    </div>
@endif
