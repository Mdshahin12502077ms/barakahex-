<ul class="d-flex gap-3">
    @if (hasPermission('feature.edit'))
        <li>
            <a href="{{ route('features.edit',$feature->id) }}"><i
                        class="las la-edit"></i></a>
        </li>
    @endif
    @if (hasPermission('feature.destroy'))
        <li>
            <a href="javascript:void(0)"
                onclick="delete_row('{{ route('features.destroy', $feature->id) }}')"
                data-toggle="tooltip"
                data-original-title="{{ __('delete') }}"><i class="las la-trash-alt"></i></a>
        </li>
    @endif
</ul>
