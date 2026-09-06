<div class="action-card d-flex align-items-center justify-content-center">
    @if(hasPermission('payment_method_update') || hasPermission('payment_method_delete'))
        <ul class="d-flex gap-30 justify-content-end">
            @if(hasPermission('payment_method_update'))
                <li>
                    <a href="{{route('admin.edit.payment.method', $payment->id)}}"> <i class="las la-edit"></i> </a>
                </li>
            @endif
            @if(hasPermission('payment_method_delete'))
                <li>
                    <a href="javascript:void(0);" onclick="delete_row('setting/payment-method/delete/', {{ $payment->id }})"
                        id="delete-btn">
                        <i class="las la-trash-alt"></i>
                    </a>
                </li>
            @endif
        </ul>
    @endif
</div>
