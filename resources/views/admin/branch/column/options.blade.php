<div class="action-card">
    <ul class="d-flex  justify-content-center">
        @if (hasPermission('branch_update'))
            <li>
                <a class="dropdown-item" href="{{ route('admin.branch.edit', $query->id) }}" >
                    <i class="las la-edit"></i>
                </a>
            </li>
        @endif
        @if (hasPermission('branch_delete'))
            <li>
                <a class="dropdown-item" href="javascript:void(0);" onclick="delete_row('branch/delete/', {{ $query->id }})" id="delete-btn">
                    <i class="las la-trash-alt"></i>
                </a>
            </li>
        @endif
    </ul>
</div>
