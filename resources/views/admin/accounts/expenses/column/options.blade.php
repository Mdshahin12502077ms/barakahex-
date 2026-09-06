@if (hasPermission('expense_update') || hasPermission('expense_delete'))
    <div class="action-card d-flex align-items-center justify-content-center">
        <ul class="d-flex justify-content-center">
            @if ($expense->create_type == 'user_defined')
                @if (hasPermission('expense_update'))
                    <li><a class="dropdown-item" href="{{ route('expenses.edit', $expense->id) }}" href="javascript:void(0);">
                            <i class="las la-edit"></i>
                        </a>
                    </li>
                @endif
                @if (hasPermission('expense_delete'))
                    <li><a class="dropdown-item" href="javascript:void(0);" onclick="delete_row('expense/delete/', {{ $expense->id }})"
                            id="delete-btn">
                            <i class="las la-trash-alt"></i>
                        </a>
                    </li>
                @endif
            @endif
        </ul>
    </div>
@endif
