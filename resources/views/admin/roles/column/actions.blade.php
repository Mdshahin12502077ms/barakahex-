@if (hasPermission('role_update') || (hasPermission('role_delete') && $query->id != 1))
    <div class="action-card">
        <ul class="d-flex  justify-content-center">
            @if (hasPermission('role_update'))
                <li>
                    <a class="dropdown-item" href="{{ route('roles.edit', $query->id) }}">
                        <i class="las la-edit"></i>
                    </a>
                </li>
            @endif
            @if (hasPermission('role_delete'))
                <li><a class="dropdown-item" href="javascript:void(0);"
                        onclick="delete_row('roles/', {{ $query->id }})" id="delete-btn">
                        <i class="las la-trash-alt"></i>
                    </a>
                </li>
            @endif
        </ul>
    </div>
@endif
