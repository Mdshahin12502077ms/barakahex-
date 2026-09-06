@if (hasPermission('deliveryman_update') || hasPermission('deliveryman_delete'))
    <div class="action-card">
        <ul class="d-flex justify-content-center">
            @if (hasPermission('deliveryman_update'))
                <li>
                    <a href="{{ route('delivery.man.edit', $delivery_man->id) }}" class="dropdown-item py-2"
                        href="javascript:void(0);">
                        <i class="las la-edit"></i>
                    </a>
                </li>
            @endif
            @if (hasPermission('deliveryman_delete'))
                <li>
                    <a href="javascript:void(0);"
                        onclick="delete_row('delivery-man/delete/', {{ $delivery_man->id }})" id="delete-btn"
                        class="dropdown-item py-2">
                        <i class="las la-trash-alt"></i>
                    </a>
                </li>
            @endif
        </ul>
    </div>
@endif

