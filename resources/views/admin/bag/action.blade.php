<ul class="d-flex gap-30 justify-content-center">
    @if(hasPermission('bag_read'))
        <li>
            <a href="{{ route('bag.show', $bag->id) }}" class="text-primary" title="{{ __('view') }}"><i class="las la-eye"></i></a>
        </li>
        <li>
            <a href="{{ route('bag.print-manifest', $bag->id) }}" class="text-success" target="_blank" title="{{ __('print') }}"><i class="las la-print"></i></a>
        </li>
    @endif
    @if(hasPermission('bag_update') && $bag->status == 'open')
        <li>
            <a href="{{ route('bag.show', $bag->id) }}" class="text-warning" title="{{ __('add_parcel') }}"
               style="display:inline-flex;align-items:center;gap:3px;font-size:13px;font-weight:600;">
                <i class="las la-plus-circle" style="font-size:16px;"></i>
                <span>{{ __('add_parcel') }}</span>
            </a>
        </li>
        <li>
            <a class="edit_modal" href="javascript:void(0)" data-fetch_url="{{ route('bag.edit', $bag->id) }}" title="{{ __('edit') }}"><i class="las la-edit"></i></a>
        </li>
    @endif
    @if(hasPermission('bag_delete') && $bag->status == 'open')
        <li>
            <a href="javascript:void(0)"
               onclick="delete_row('{{ route('bag.destroy', $bag->id) }}')"
               data-toggle="tooltip"
               title="{{ __('delete') }}"><i class="las la-trash-alt"></i></a>
        </li>
    @endif
</ul>
