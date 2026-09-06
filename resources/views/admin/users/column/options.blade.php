@if (hasPermission('user_update') || hasPermission('user_delete'))
    <div class="action-card">
        <ul class="d-flex justify-content-center">
            @if (hasPermission('user_update'))
                <li>
                    <a class="dropdown-item" href="{{ route('user.edit', $query->id) }}">
                        <i class="las la-edit"></i>
                    </a>
                </li>
            @endif
            @if (hasPermission('user_delete') && !$query->is_super_admin)  {{-- Check if the user is not a super admin --}}
                <li>
                    <a class="dropdown-item" href="javascript:void(0);" onclick="delete_row('user/delete/', {{ $query->id }})" id="delete-btn">
                        <i class="las la-trash-alt"></i>
                    </a>
                </li>
            @endif
        </ul>
    </div>
@endif

