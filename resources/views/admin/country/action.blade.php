<ul class="d-flex gap-30 justify-content-end align-items-center">
    @if(hasPermission('country_update'))
        <li>
            <a class="edit_modal" href="javascript:void(0)"
               data-fetch_url="{{ route('countries.edit',$country->id) }}"
               data-route="{{ route('countries.update',$country->id) }}" data-modal="country"><i
                    class="las la-edit"></i></a>
        </li>
    @endif
    @if(hasPermission('country_delete'))
        <li>
            <a href="javascript:void(0)"
               onclick="delete_row('{{ route('countries.destroy', $country->id) }}')"
               data-toggle="tooltip"
               data-original-title="{{ __('delete') }}"><i class="las la-trash-alt"></i></a>
        </li>
    @endif
</ul>
