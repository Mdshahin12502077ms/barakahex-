<div>
    @if ($payment->status == 'pending')
        <a href="{{ route('admin.withdraw.invoice', $payment->id) }}">
            <span class="badge text-warning">{{ __($payment->status) }}</span><br>
        </a>
    @elseif($payment->status == 'approved')
        <div>
            <span class="badge text-info">{{ __($payment->status) }}</span>
            @if ($payment->approvedBy)
                <small class="text-muted d-block" style="font-size: 11px;">Approved By: {{ $payment->approvedBy->first_name }}</small>
            @endif
        </div>
        @if ($payment->withdrawBatch && hasPermission('bulk_withdraw_read'))
            <a href="{{ route('admin.withdraw.invoice.bulk', $payment->withdrawBatch->id) }}">
                <span class="badge text-info">{{ __('batch_no') . ': ' . $payment->withdrawBatch->batch_no }}</span><br>
            </a>
        @endif
    @elseif($payment->status == 'rejected' || $payment->status == 'cancelled')
        <span class="badge text-danger">{{ __($payment->status) }}</span><br>
        @if ($payment->status != 'cancelled')
            @if (@$payment->companyAccountReason)
                <span
                    class="text-warning">{{ __('reject_reason') . ': ' }}{{ @$payment->companyAccountReason->reject_reason != '' ? __($payment->companyAccountReason->reject_reason) : '' }}</span><br>
            @endif
        @endif
        <span>{{ __('at') . ': ' }}{{ $payment->updated_at != '' ? date('M d, Y h:i a', strtotime($payment->updated_at)) : '' }}</span>
    @else
        <a href="{{ route('admin.withdraw.invoice', $payment->id) }}">
            <span class="badge text-success">{{ $payment->status == 'processed' ? __('paid') : __($payment->status) }}</span><br>
        </a>
        @if ($payment->approvedBy)
            <small class="text-muted d-block" style="font-size: 11px;">Approved By: {{ $payment->approvedBy->first_name }}</small>
        @endif
        @if ($payment->paidBy)
            <small class="text-muted d-block" style="font-size: 11px;">Paid By: {{ $payment->paidBy->first_name }}</small>
        @endif
        @if ($payment->companyAccount->transaction_id != '' && !$payment->withdrawBatch)
            <span
                class="badge text-info">{{ __('transaction_id') . ': ' . $payment->companyAccount->transaction_id }}</span><br>
        @endif
        @if ($payment->withdrawBatch && hasPermission('bulk_withdraw_read'))
            <a href="{{ route('admin.withdraw.invoice.bulk', $payment->withdrawBatch->id) }}">
                <span class="badge text-info">{{ __('batch_no') . ': ' . $payment->withdrawBatch->batch_no }}</span><br>
            </a>
        @endif
        <span>{{ __('at') . ': ' }}{{ $payment->updated_at != '' ? date('M d, Y h:i a', strtotime($payment->updated_at)) : '' }}</span>
    @endif
</div>
