      <td>
        @if ($withdraw->status == 'pending')
            <span
                class="badge text-warning">{{ __($withdraw->status) }}</span><br>
        @elseif($withdraw->status == 'approved')
            <span
                class="badge text-info">{{ __($withdraw->status) }}</span><br>
        @elseif($withdraw->status == 'rejected' || $withdraw->status == 'cancelled')
            <span
                class="badge text-danger">{{ __($withdraw->status) }}</span><br>
            @if ($withdraw->status != 'cancelled')
                @if (@$withdraw->companyAccountReason)
                    <span
                        class="text-warning">{{ __('reject_reason') . ': ' }}{{ @$withdraw->companyAccountReason->reject_reason != '' ? __($withdraw->companyAccountReason->reject_reason) : '' }}</span><br>
                @endif
            @endif
            <span
                class="text">{{ __('at') . ': ' }}{{ $withdraw->updated_at != '' ? date('M d, Y h:i a', strtotime($withdraw->updated_at)) : '' }}</span>
        @else
            <span
                class="badge text-success">{{ __($withdraw->status) }}</span><br>
            @if ($withdraw->companyAccount->transaction_id != '')
                <span
                    class="badge text-info">{{ __('transaction_id') . ': ' . $withdraw->companyAccount->transaction_id }}</span><br>
            @endif
            <span
                class="text">{{ __('at') . ': ' }}{{ $withdraw->updated_at != '' ? date('M d, Y h:i a', strtotime($withdraw->updated_at)) : '' }}</span>
        @endif
    </td>
