<div class="action-card">
    <ul class="d-flex justify-content-start align-items-center">
        <li>
            <a id="{{ $parcel->parcel_no }}" data-bs-toggle="{{ $parcel->parcel_no }}"
                data-text="{{ $parcel->parcel_no }}" class="parcel-id-copy copy-to-clipboard"
                href="javascript:void(0)"><i class="icon la la-copy"></i></a>
        </li>
        <div class="dropdown">
            <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="las la-ellipsis-v"></i>
            </a>
            <ul class="dropdown-menu">
                @if (hasPermission('parcel_update'))
                    @if (
        $parcel->status == 'pending' ||
        $parcel->status == 'pickup-assigned' ||
        $parcel->status == 're-schedule-pickup' ||
        $parcel->status == 'received-by-pickup-man' ||
        $parcel->status == 'received' ||
        $parcel->status == 'transferred-to-branch' ||
        $parcel->status == 'delivery-assigned' ||
        $parcel->status == 're-schedule-delivery' ||
        ($parcel->status == 'returned-to-warehouse' && $parcel->is_partially_delivered == false) ||
        (($parcel->status == 'return-assigned-to-merchant' && $parcel->is_partially_delivered == false) ||
            hasPermission('send_to_paperfly'))
    )
                        <li>
                            <a class="dropdown-item" href="{{ route('parcel.edit', $parcel->id) }}" data-id="{{ $parcel->id }}">
                                <span> {{ __('edit') }}</span>
                            </a>
                        </li>
                    @endif
                @endif
                @if ($parcel->status != 'pending' && $parcel->status != 'delivered-and-verified' && hasPermission('parcel_backward'))
                    @if ($parcel->status == 'cancel' || $parcel->status == 'deleted')
                        <li><a href="javascript:void(0);" class="reverse-from-cancel dropdown-item" data-id="{{ $parcel->id }}"
                                id="reverse-from-cancel" data-bs-toggle="modal"
                                data-bs-target="#parcel-reverse-from-cancel"><span>
                                    {{ __('backward') . ' (' . __($parcel->status_before_cancel) }}) </span></a></li>
                                    @else
                        <li><a href="javascript:void(0);" class="delivery-reverse dropdown-item" data-id="{{ $parcel->id }}"
                                id="reverse-delivery" data-bs-toggle="modal" data-bs-target="#delivery-reverse"
                                data-url="{{ route('parcel.reverse.options') }}"><span>
                                    {{ __('backward') }} </span></a></li>
                    @endif
                @endif

                @if (
    $parcel->status == 'cancel' &&
    ($parcel->status_before_cancel == 'received-by-pickup-man' ||
        $parcel->status_before_cancel == 'received' ||
        $parcel->status_before_cancel == 'transferred-to-branch' ||
        $parcel->status_before_cancel == 'transferred-received-by-branch' ||
        $parcel->status_before_cancel == 'delivery-assigned' ||
        $parcel->status_before_cancel == 're-schedule-delivery')
)

                    @if (hasPermission('parcel_returned_to_warehouse'))
                        <li><a href="javascript:void(0);" class="delivery-return dropdown-item" data-id="{{ $parcel->id }}"
                                id="delivery-return" data-bs-toggle="modal" data-bs-target="#return-delivery"><span>
                                    {{ __('returned_to_warehouse') }} </span></a></li>
                    @endif

                @endif


                @if ($parcel->status == 'pending')
                    @if (hasPermission('parcel_pickup_assigned'))
                        <li><a href="javascript:void(0);" class="assign-pickup-man dropdown-item" data-id="{{ $parcel->id }}"
                                id="assign-pickup-man" data-bs-toggle="modal" data-bs-target="#assign-pickup"> <span>
                                    {{ __('assign_pickup_man') }} </span></a></li>
                    @endif

                    @if (hasPermission('parcel_delete'))
                        <li><a href="javascript:void(0);" class="delete-parcel dropdown-item" data-id="{{ $parcel->id }}"
                                id="delete-parcel" data-bs-toggle="modal" data-bs-target="#parcel-delete"> <span>
                                    {{ __('delete') }} </span></a></li>
                    @endif
                @elseif($parcel->status == 'pickup-assigned' || $parcel->status == 're-schedule-pickup')
                    @if (hasPermission('parcel_reschedule_pickup'))
                        <li><a href="javascript:void(0);" class="reschedule-pickup dropdown-item" data-id="{{ $parcel->id }}"
                                id="reschedule-pickup" data-bs-toggle="modal" data-bs-target="#re-schedule-pickup"> <span>
                                    {{ __('re_schedule_pickup') }} </span></a></li>
                    @endif

                    @if (hasPermission('parcel_received'))
                        <li>
                            <a href="javascript:void(0);" class="receive-parcel-pickup dropdown-item"
                                data-id="{{ $parcel->id }}" id="receive-parcel-pickup" data-bs-toggle="modal"
                                data-bs-target="#parcel-receive-by-pickupman">
                                {{ __('received_by_pickup_man') }}
                            </a>
                        </li>
                    @endif

                    @if (hasPermission('parcel_received'))
                        <li>
                            <a href="javascript:void(0);" class="receive-parcel dropdown-item" data-id="{{ $parcel->id }}"
                                id="receive-parcel" data-bs-toggle="modal" data-bs-target="#parcel-receive">
                                {{ __('received_by_warehouse') }}
                            </a>
                        </li>
                    @endif
                    @if (hasPermission('parcel_delete'))
                        <li>
                            <a href="javascript:void(0);" class="delete-parcel dropdown-item" data-id="{{ $parcel->id }}"
                                id="delete-parcel" data-bs-toggle="modal" data-bs-target="#parcel-delete">
                                <span> {{ __('delete') }}</span>
                            </a>
                        </li>
                    @endif
                @elseif($parcel->status == 'received-by-pickup-man')
                    @if (hasPermission('parcel_received'))
                        <li>
                            <a href="javascript:void(0);" class="receive-parcel dropdown-item" data-id="{{ $parcel->id }}"
                                id="receive-parcel" data-bs-toggle="modal" data-bs-target="#parcel-receive">
                                {{ __('received_by_warehouse') }}
                            </a>
                        </li>
                    @endif
                    @if (hasPermission('parcel_cancel'))
                        <li>
                            <a href="javascript:void(0);" class="cancel-parcel dropdown-item" data-id="{{ $parcel->id }}"
                                id="cancel-parcel" data-bs-toggle="modal" data-bs-target="#parcel-cancel">
                                <span> {{ __('cancel') }} </span>
                            </a>
                        </li>
                    @endif
                @elseif($parcel->status == 'received' || $parcel->status == 'transferred-received-by-branch')
                    @if (hasPermission('parcel_transfer_to_branch'))
                        <li>
                            <a href="javascript:void(0);" class="transfer-to-branch dropdown-item" data-id="{{ $parcel->id }}"
                                id="transfer-to-branch" data-url="{{ route('parcel.transfer.options') }}" data-bs-toggle="modal"
                                data-bs-target="#parcel-transfer-to-branch">
                                <span> {{ __('transfer_to_branch') }} </span>
                            </a>
                        </li>
                    @endif
                    @if (hasPermission('parcel_delivery_assigned'))
                        <li>
                            <a href="javascript:void(0);" class="assign-delivery-man dropdown-item" data-id="{{ $parcel->id }}"
                                id="assign-delivery-man" data-bs-toggle="modal" data-bs-target="#assign-delivery">
                                <span> {{ __('assign_delivery_man') }} </span>
                            </a>
                        </li>
                    @endif
                    @if (hasPermission('parcel_cancel'))
                        <li>
                            <a href="javascript:void(0);" class="cancel-parcel dropdown-item" data-id="{{ $parcel->id }}"
                                id="cancel-parcel" data-bs-toggle="modal" data-bs-target="#parcel-cancel">
                                <span> {{ __('cancel') }} </span>
                            </a>
                        </li>
                    @endif
                @elseif($parcel->status == 'transferred-to-branch')
                    @if (hasPermission('parcel_transfer_receive_to_branch'))
                        <li>
                            <a href="javascript:void(0);" class="transfer-receive-to-branch dropdown-item"
                                data-id="{{ $parcel->id }}" id="transfer-receive-to-branch" data-bs-toggle="modal"
                                data-bs-target="#parcel-transfer-receive-to-branch"> <span>
                                    {{ __('transfer_receive_to_branch') }} </span>
                            </a>
                        </li>
                    @endif
                    @if (hasPermission('parcel_cancel'))
                        <li>
                            <a href="javascript:void(0);" class="cancel-parcel dropdown-item" data-id="{{ $parcel->id }}"
                                id="cancel-parcel" data-bs-toggle="modal" data-bs-target="#parcel-cancel">
                                <span> {{ __('cancel') }} </span>
                            </a>
                        </li>
                    @endif
                @elseif($parcel->status == 'delivery-assigned' || $parcel->status == 're-schedule-delivery')

                    @if (hasPermission('parcel_returned_to_warehouse'))
                        <li>
                            <a href="javascript:void(0);" class="delivery-return dropdown-item" data-id="{{ $parcel->id }}"
                                id="delivery-return" data-bs-toggle="modal" data-bs-target="#return-delivery"> <span>
                                    {{ __('returned_to_warehouse') }} </span>
                            </a>
                        </li>
                    @endif
                    @if (hasPermission('parcel_delivered'))
                        <li>
                            <a href="javascript:void(0);" class="delivery-parcel-partially dropdown-item"
                                data-id="{{ $parcel->id }}" id="delivery-parcel-partially" data-bs-toggle="modal"
                                data-bs-target="#parcel-delivered-partially">
                                <span> {{ __('partially-delivered') }} </span>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" class="delivery-parcel dropdown-item" data-id="{{ $parcel->id }}"
                                id="delivery-parcel" data-bs-toggle="modal" data-bs-target="#parcel-delivered-bulk"> <span>
                                    {{ __('delivered') }} </span>
                            </a>
                        </li>
                    @endif
                    @if (hasPermission('parcel_cancel'))
                        <li>
                            <a href="javascript:void(0);" class="cancel-parcel dropdown-item" data-id="{{ $parcel->id }}"
                                id="cancel-parcel" data-bs-toggle="modal" data-bs-target="#parcel-cancel">
                                <span> {{ __('cancel') }} </span>
                            </a>
                        </li>
                    @endif
                @elseif($parcel->status == 'returned-to-warehouse')
                    @if (hasPermission('parcel_reschedule_delivery'))
                        <li>
                            <a href="javascript:void(0);" class="reschedule-delivery dropdown-item" data-id="{{ $parcel->id }}"
                                id="reschedule-delivery" data-bs-toggle="modal" data-bs-target="#re-schedule-delivery">
                                <span> {{ __('re_schedule_delivery') }} </span>
                            </a>
                        </li>
                    @endif
                        @if (hasPermission('parcel_return_assigned_to_merchant'))
                            <li>
                                <a href="javascript:void(0);" class="return-assign-to-merchant dropdown-item"
                                    data-id="{{ $parcel->id }}" id="return-assign-to-merchant" data-bs-toggle="modal"
                                    data-bs-target="#return-assign-tomerchant">
                                    <span> {{ __('return_assign_to_merchant') }} </span>
                                </a>
                            </li>
                        @endif
                @elseif($parcel->status == 'return-assigned-to-merchant')
                    @if (hasPermission('parcel_returned_to_merchant'))
                        <li>
                            <a href="javascript:void(0);" class="parcel-returned-to-merchant dropdown-item"
                                data-id="{{ $parcel->id }}" id="parcel-returned-to-merchant" data-bs-toggle="modal"
                                data-bs-target="#returned-to-merchant">
                                <span> {{ __('returned_to_merchant') }} </span>
                            </a>
                        </li>
                    @endif

                @endif
                <li>
                    <a class="dropdown-item" data-id="{{ $parcel->id }}"
                        href="{{ route('admin.parcel.detail', $parcel->id) }}">
                        <span> {{ __('view') }}</span>
                    </a>
                </li>
                @if (hasPermission('parcel_create'))
                    <li>
                        <a class="dropdown-item" data-id="{{ $parcel->id }}"
                            href="{{ route('admin.parcel.duplicate', $parcel->id) }}">
                            <span> {{ __('duplicate') }}</span>
                        </a>
                    </li>
                @endif
                <li>
                    <a class=" dropdown-item" data-id="{{ $parcel->id }}"
                        href="{{ route('admin.parcel.print', $parcel->id) }}" target="_blank">
                        <span> {{ __('print') }}</span>
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" data-id="{{ $parcel->id }}"
                        href="{{ route('admin.parcel.sticker', $parcel->id) }}" target="_blank">
                        <span> {{ __('sticker_print') }}</span>
                    </a>
                </li>
                @if ($parcel->status == 'pickup-assigned' || $parcel->status == 're-schedule-pickup')
                    <li>
                        <a class="dropdown-item" data-id="{{ $parcel->id }}"
                            href="{{ route('admin.parcel.notify.pickupman', $parcel->id) }}">
                            <span> {{ __('notify_pickup_man') }}</span>
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </ul>
</div>