<div class=" action-card">
    <ul class="d-flex justify-content-center align-items-center">

        @if ($withdraw->status == 'pending' || $withdraw->status == 'processed' || $withdraw->status == 'approved')
            <li>
                <a href="{{ Sentinel::getUser()->user_type == 'merchant' ? route('merchant.invoice', $withdraw->id) : route('merchant.staff.invoice', $withdraw->id) }}"
                    class="" data-original-title="{{ __('view') }}"><i
                        class="icon las la-eye"></i></a>
            </li>
            @if ($withdraw->status == 'pending' && $withdraw->id > 1939)
                <li>
                    <a href="{{ Sentinel::getUser()->user_type == 'merchant' ? route('merchant.withdraw.edit', $withdraw->id) : route('merchant.staff.withdraw.edit', $withdraw->id) }}"
                        class="" data-original-title="{{ __('edit') }}">
                        <i class="icon la la-edit"></i>
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0);"
                        onclick="changeWithdrawStatus('{{ Sentinel::getUser()->user_type == 'merchant' ? '/merchant/withdraw-status/' : '/staff/withdraw-status/' }}', {{ $withdraw->id }}, 'cancelled')"
                        id="delete-btn" class="text-danger"
                        data-original-title="{{ __('cancel') }}">
                        <i class="icon las la-trash"></i>
                    </a>
                </li>
            @endif
        @endif
    </ul>
</div>


{{-- <div class=" action-card">
    <ul class="d-flex justify-content-center align-items-center">
        <li>
            <a href="{{ route('merchant.staff.edit', $query->id) }}" class=""
                data-original-title="{{ __('edit') }}"><i class="icon la la-edit"></i></a>
        </li>
    </ul>
</div> --}}
