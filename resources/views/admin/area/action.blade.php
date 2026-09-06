<ul class="d-flex gap-30 justify-content-end align-items-center">
    @if(hasPermission('area_update') || hasPermission('delivery_zone_update') || hasPermission('upazila_update') || hasPermission('district_update') || hasPermission('division_update') || hasPermission('country_update'))
        <li>
            <a class="edit_modal" href="javascript:void(0)"
               data-fetch_url="{{ route('areas.edit', $area->id) }}"
               data-route="{{ route('areas.update', $area->id) }}" data-modal="area"><i
                    class="las la-edit"></i></a>
        </li>
    @endif
    @if(hasPermission('area_delete') || hasPermission('delivery_zone_delete') || hasPermission('upazila_delete') || hasPermission('district_delete') || hasPermission('division_delete') || hasPermission('country_delete'))
        <li>
            <a href="javascript:void(0)"
               onclick="delete_row('{{ route('areas.destroy', $area->id) }}')"
               data-toggle="tooltip"
               data-original-title="{{ __('delete') }}"><i class="las la-trash-alt"></i></a>
        </li>
    @endif
</ul>
