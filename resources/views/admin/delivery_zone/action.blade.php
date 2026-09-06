<ul class="d-flex gap-30 justify-content-end align-items-center">
    @if(hasPermission('delivery_zone_update') || hasPermission('upazila_update') || hasPermission('district_update') || hasPermission('division_update') || hasPermission('country_update'))
        <li>
            <a class="edit_modal" href="javascript:void(0)"
               data-fetch_url="{{ route('delivery-zones.edit', $deliveryZone->id) }}"
               data-route="{{ route('delivery-zones.update', $deliveryZone->id) }}" data-modal="delivery_zone"><i
                    class="las la-edit"></i></a>
        </li>
    @endif
    @if(hasPermission('delivery_zone_delete') || hasPermission('upazila_delete') || hasPermission('district_delete') || hasPermission('division_delete') || hasPermission('country_delete'))
        <li>
            <a href="javascript:void(0)"
               onclick="delete_row('{{ route('delivery-zones.destroy', $deliveryZone->id) }}')"
               data-toggle="tooltip"
               data-original-title="{{ __('delete') }}"><i class="las la-trash-alt"></i></a>
        </li>
    @endif
</ul>
