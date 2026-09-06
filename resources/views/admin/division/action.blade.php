<ul class="d-flex gap-30 justify-content-end align-items-center">
    @if(hasPermission('division_update') || hasPermission('country_update'))
        <li>
            <a class="edit_modal" href="javascript:void(0)"
               data-fetch_url="{{ route('divisions.edit', $division->id) }}"
               data-route="{{ route('divisions.update', $division->id) }}" data-modal="division"><i
                    class="las la-edit"></i></a>
        </li>
    @endif
    @if(hasPermission('division_delete') || hasPermission('country_delete'))
        <li>
            <a href="javascript:void(0)"
               onclick="delete_row('{{ route('divisions.destroy', $division->id) }}')"
               data-toggle="tooltip"
               data-original-title="{{ __('delete') }}"><i class="las la-trash-alt"></i></a>
        </li>
    @endif
</ul>
