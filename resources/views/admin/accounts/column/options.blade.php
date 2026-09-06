@if ((hasPermission('income_update') || hasPermission('income_delete')) && $income->create_type == 'user_defined')
<div class="action-card d-flex align-items-center justify-content-center">
    <ul class="d-flex justify-content-center">
        @if (@$income->merchantAccount->payment_withdraw_id == null && @$income->merchantAccount->is_paid == false)
            @if (hasPermission('income_update'))
                <li>
                    <a class="dropdown-item" href="{{ $income->delivery_man_id == '' && $income->merchant_id != '' ? route('incomes.receive.from.merchant.edit', $income->id) : route('incomes.edit', $income->id) }}"
                        href="javascript:void(0);">
                        <i class="las la-edit"></i>
                    </a>
                </li>
            @endif
            @if (hasPermission('income_delete'))
                <li>
                    <a class="dropdown-item" href="javascript:void(0);" onclick="delete_row('income/delete/', {{ $income->id }})"
                        id="delete-btn">
                        <i class="las la-trash-alt"></i>
                    </a>
                </li>
            @endif
        @elseif(@$income->merchantAccount->payment_withdraw_id != null || @$income->merchantAccount->is_paid == true)
            <span class="badge text-success">{{ __('adjusted_in_payment') }}</span>
        @endif
    </ul>
</div>
@endif
