<ul class="d-flex gap-3">
    @if (hasPermission('news_and_event.edit'))
        <li>
            <a href="{{ route('news-and-events.edit',$news_and_event->id) }}"><i
                        class="las la-edit"></i></a>
        </li>
    @endif
    @if (hasPermission('news_and_event.destroy'))
        <li>
            <a href="javascript:void(0)"
                onclick="delete_row('{{ route('news-and-events.destroy', $news_and_event->id) }}')"
                data-toggle="tooltip"
                data-original-title="{{ __('delete') }}"><i class="las la-trash-alt"></i></a>
        </li>
    @endif
</ul>
