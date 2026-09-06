<ul class="d-flex gap-3">
    @if (hasPermission('about.create'))
        <li>
            <a href="{{ route('abouts.edit',$about->id) }}"><i
                        class="las la-edit"></i></a>
        </li>
    @endif
    @if (hasPermission('about.destroy'))
        <li>
            <a href="javascript:void(0)"
                onclick="delete_row('{{ route('abouts.destroy', $about->id) }}')"
                data-toggle="tooltip"
                data-original-title="{{ __('delete') }}"><i class="las la-trash-alt"></i></a>
        </li>
    @endif
</ul>
