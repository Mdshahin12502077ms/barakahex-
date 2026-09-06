<ul class="d-flex gap-30 justify-content-end align-items-center">
    @if(hasPermission('district_update') || hasPermission('division_update') || hasPermission('country_update'))
        <li>
            <a class="edit_modal" href="javascript:void(0)"
               data-fetch_url="{{ route('districts.edit', $district->id) }}"
               data-route="{{ route('districts.update', $district->id) }}" data-modal="district"><i
                    class="las la-edit"></i></a>
        </li>
    @endif
    @if(hasPermission('district_delete') || hasPermission('division_delete') || hasPermission('country_delete'))
        <li>
            <a href="javascript:void(0)"
               onclick="delete_row('{{ route('districts.destroy', $district->id) }}')"
               data-toggle="tooltip"
               data-original-title="{{ __('delete') }}"><i class="las la-trash-alt"></i></a>
        </li>
    @endif
</ul>
