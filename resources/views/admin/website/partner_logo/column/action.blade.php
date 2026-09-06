<ul class="d-flex gap-3">
    @if (hasPermission('partner_logo.edit'))
        <li>
            <a href="{{ route('partner-logo.edit', $partner_logo->id) }}"><i
                        class="las la-edit"></i></a>
        </li>
    @endif
    @if (hasPermission('partner_logo.destroy'))
        <li>
            <a href="javascript:void(0)"
                onclick="delete_row('{{ route('partner-logo.destroy', $partner_logo->id) }}')"
                data-toggle="tooltip"
                data-original-title="{{ __('delete') }}"><i class="las la-trash-alt"></i></a>
        </li>
    @endif
</ul>
