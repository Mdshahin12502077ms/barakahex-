@if (hasPermission('withdraw_update') || hasPermission('withdraw_process') || hasPermission('withdraw_reject'))
    <div class="action-card d-flex align-items-center justify-content-center">
        <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="las la-ellipsis-v"></i>
        </a>
        @if ($payment->status == 'pending' || $payment->status == 'processed' || $payment->status == 'approved')
            <div class="dropdown-menu dropdown-menu-right">
                <ul class="link-list-opt no-bdr">
                    @if (hasPermission('withdraw_update'))
                        @if (($payment->status == 'pending' && $payment->id > 1939) || ($payment->status == 'approved' && $payment->id > 1939))
                            <li>
                                <a href="{{ route('admin.withdraw.edit', $payment->id) }}" class="dropdown-item">
                                    <span> {{ __('edit') }}</span>
                                </a>
                            </li>
                        @endif
                    @endif
                    <li>
                        <a href="{{ route('admin.withdraw.invoice', $payment->id) }}" class="dropdown-item">
                            <span> {{ __('invoice') }}</span>
                        </a>
                    </li>
                    @if (hasPermission('withdraw_process'))
                        @if ($payment->status == 'pending' || $payment->status == 'approved')
                            @if ($payment->status == 'pending')
                                <li>
                                    @if (hasPermission('add_to_bulk_withdraw'))
                                        <a href="javascript:void(0);" class="approve-withdraw dropdown-item" id="approve-withdraw"
                                            data-url="{{ route('get-batches') }}" data-id="{{ $payment->id }}" data-bs-toggle="modal"
                                            data-bs-target="#withdraw-approve">
                                            <span>{{ __('approve') }}</span>
                                        </a>
                                    @else
                                        <a href="javascript:void(0);"
                                            onclick="changeWithdrawStatus('{{ '/admin/withdraw-status/' }}', {{ $payment->id }}, 'approved')"
                                            data-id="{{ $payment->id }}" class="dropdown-item">
                                            <span>{{ __('approve') }}</span>
                                        </a>
                                    @endif
                                </li>
                            @endif
                            @if ($payment->status == 'approved')
                                <li>
                                    <a href="javascript:void(0);" class="change-batch dropdown-item" id="change-batch"
                                        data-id="{{ $payment->id }}" data-bs-toggle="modal" data-bs-target="#batch-change"
                                        data-url="{{ route('get-batches') }}">
                                        <span> {{ __('add_change_batch') }} </span>
                                    </a>
                                </li>
                            @endif

                            <li>
                                <a href="javascript:void(0);" class="process-withdraw dropdown-item" id="process-withdraw"
                                    data-bs-toggle="modal" data-bs-target="#withdraw-process" data-id="{{ $payment->id }}">
                                    <span> {{ __('paid') }} </span>
                                </a>
                            </li>

                        @endif
                    @endif
                    @if (hasPermission('withdraw_reject'))
                        @if (
                                ($payment->status == 'pending' && $payment->id > 1939) ||
                                ($payment->status == 'processed' && $payment->id > 1939) ||
                                ($payment->status == 'approved' && $payment->id > 1939)
                            )
                            <li>
                                <a href="javascript:void(0);" class="reject-payment dropdown-item" id="reject-payment"
                                    data-id="{{ $payment->id }}" data-bs-toggle="modal" data-bs-target="#payment-reject">
                                    <span> {{ __('reject') }} </span>
                                </a>
                            </li>
                        @endif
                    @endif
                </ul>
            </div>
        @endif
    </div>
@endif