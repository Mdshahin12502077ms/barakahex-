<ul class="d-flex gap-3">
    @if (hasPermission('service.edit'))
        <li>
            <a href="{{ route('services.edit',$service->id) }}"><i
                        class="las la-edit"></i></a>
        </li>
    @endif
    @if (hasPermission('service.destroy'))
        <li>
            <a href="javascript:void(0)"
                onclick="delete_row('{{ route('services.destroy', $service->id) }}')"
                data-toggle="tooltip"
                data-original-title="{{ __('delete') }}"><i class="las la-trash-alt"></i></a>
        </li>
    @endif
</ul>
