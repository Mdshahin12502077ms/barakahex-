@if (hasPermission('notice_update') || hasPermission('notice_delete'))
    <div class="action-card d-flex align-items-center justify-content-center">
        <ul class="d-flex gap-30 justify-content-cneter">
            @if (hasPermission('notice_update'))
                <li>
                    <a href="{{ route('notice.edit', $notice->id) }}">
                        <i class="las la-edit"></i>
                    </a>
                </li>
            @endif
            @if (hasPermission('notice_delete'))
                <li>
                    <a href="javascript:void(0);" onclick="delete_row('notice/delete/', {{ $notice->id }})"
                        id="delete-btn">
                        <i class="las la-trash-alt"></i>
                    </a>
                </li>
            @endif
        </ul>
    </div>
@endif
