@if (hasPermission('bulk_withdraw_update') ||
        hasPermission('bulk_withdraw_process') ||
        hasPermission('download_payment_sheet') ||
        hasPermission('bulk_withdraw_delete'))
    <div class="action-card d-flex align-items-center justify-content-center">
        <div class="dropdown">
            <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="las la-ellipsis-v"></i>
            </a>
            <ul class="dropdown-menu">
                @if (hasPermission('bulk_withdraw_update'))
                    @if ($bulk_payment->status == 'pending')
                        <li>
                            <a href="{{ route('admin.withdraws.bulk.edit', $bulk_payment->id) }}" class="dropdown-item">
                                <span> {{ __('edit') }}</span>
                            </a>
                        </li>
                    @endif
                @endif
                @if (hasPermission('bulk_withdraw_process'))
                    @if ($bulk_payment->status == 'pending' && !blank($bulk_payment->withdraws))
                        <li>
                            <a href="javascript:void(0);" class="process-withdraw dropdown-item" id="process-withdraw" data-id="{{ $bulk_payment->id }}"
                                data-bs-toggle="modal" data-bs-target="#withdraw-process">
                                <span> {{ __('process') }} </span>
                            </a>
                        </li>
                    @endif
                @endif
                @if (hasPermission('download_payment_sheet'))
                    <li>
                        <a href="{{ route('admin.payment.report', $bulk_payment->id) }}" class="dropdown-item">
                            <span> {{ __('Payment Sheet') }}</span>
                        </a>
                    </li>
                @endif
                <li>
                    <a href="{{ route('admin.withdraw.invoice.bulk', $bulk_payment->id) }}" class="dropdown-item">
                        <span> {{ __('invoice') }}</span>
                    </a>
                </li>
                @if (hasPermission('bulk_withdraw_delete') && blank($bulk_payment->withdraws))
                    <li>
                        <a href="javascript:void(0);"
                            onclick="delete_row('bulk-withdraw-delete/', {{ $bulk_payment->id }})" id="delete-btn"
                            class="dropdown-item">
                            <span> {{ __('delete') }} </span>
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
@endif
