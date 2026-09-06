@if (hasPermission('fund_transfer_update') || hasPermission('fund_transfer_deslete'))
    <div class="action-card d-flex align-items-center justify-content-center">
        <div class="dropdown">
            <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="las la-ellipsis-v"></i>
            </a>
            <ul class="dropdown-menu">
                @if (hasPermission('fund_transfer_update'))
                    <li>
                        <a href="{{ route('admin.fund-transfer.edit', $fund_transfer->id) }}" class="dropdown-item"
                            href="javascript:void(0);">
                            {{ __('edit') }}
                        </a>
                    </li>
                @endif
                @if (hasPermission('fund_transfer_delete'))
                    <li><a href="javascript:void(0);"
                            onclick="delete_row('fund-transfer/delete/', {{ $fund_transfer->id }})" id="delete-btn"
                            class="dropdown-item">
                            {{ __('delete') }}
                        </a></li>
                @endif
            </ul>
        </div>
    </div>
@endif
