<td class="action-card">
    <ul class="d-flex gap-3">
        @if (hasPermission('pages.edit'))
            <li>
                <a href="{{ route('pages.edit',$page->id) }}"><i class="las la-edit"></i></a>
            </li>
        @endif
        @if (hasPermission('pages.destroy'))
            <li>
                <a href="javascript:void(0)"
                    onclick="delete_row('{{ route('pages.destroy', $page->id) }}',null,true)"
                    data-toggle="tooltip"
                    data-original-title="{{ __('delete') }}"><i class="las la-trash-alt"></i></a>
            </li>
        @endif
    </ul>
</td>
