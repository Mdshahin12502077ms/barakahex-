<div>
    <ul class="gx-1">
        <li>
            <span type="button" class="btn btn-icon btn-trigger btn-tooltip copy-to-clipboard" data-text="{{ $parcel->parcel_no }}" data-original-title="{{__('copy')}}"><i class="icon las la-copy"></i></span>
        </li>
        <li>
            <div class="dropdown">
                <a href="#" class="dropdown-toggle btn btn-icon btn-trigger btn-tooltip" data-original-title="{{__('options')}}" data-toggle="dropdown">
                    <i class="icon la lamore-h"></i></a>
                <div class="dropdown-menu dropdown-menu-right">
                    <ul class="link-list-opt no-bdr">
                        @if(hasPermission('parcel_update'))
                            @if($parcel->status == 'pending'
                            || $parcel->status == 'pickup-assigned'
                            || $parcel->status == 're-schedule-pickup'
                            || $parcel->status == 'received-by-pickup-man'
                            || $parcel->status == "received"
                            || $parcel->status == "transferred-to-branch"
                            || $parcel->status == "delivery-assigned"
                            || $parcel->status == "re-schedule-delivery"
                            || ($parcel->status == "returned-to-warehouse" && $parcel->is_partially_delivered == false)
                            || ($parcel->status == "return-assigned-to-merchant" && $parcel->is_partially_delivered == false
                            || hasPermission('send_to_paperfly'))
                            )
                                <li><a href="{{route('parcel.edit', $parcel->id)}}"><i class="icon las la-edit"></i> <span> {{__('edit')}}</span></a></li>
                            @endif
                        @endif
                        @if(($parcel->status != "pending" && $parcel->status != 'delivered-and-verified') && hasPermission('parcel_backward'))
                            @if($parcel->status == 'cancel' || $parcel->status == 'deleted')
                                <li><a href="javascript:void(0);" class="reverse-from-cancel" id="reverse-from-cancel" data-bs-toggle="modal" data-bs-target="#parcel-reverse-from-cancel"><i class="icon las la-arrow-left"></i> <span> {{__('backward').' ('.__($parcel->status_before_cancel)}}) </span></a></li>
                            @else
                                <li><a href="javascript:void(0);" class="delivery-reverse" id="reverse-delivery" data-bs-toggle="modal" data-bs-target="#delivery-reverse" data-url="{{ route('parcel.reverse.options') }}"><i class="icon las la-arrow-left"></i> <span> {{__('backward')}} </span></a></li>
                            @endif
                        @endif

                        @if(($parcel->status == "cancel") &&
                        ($parcel->status_before_cancel == 'received-by-pickup-man' ||
                        $parcel->status_before_cancel == 'received' ||
                        $parcel->status_before_cancel == 'transferred-to-branch' ||
                        $parcel->status_before_cancel == 'transferred-received-by-branch' ||
                        $parcel->status_before_cancel == 'delivery-assigned' ||
                        $parcel->status_before_cancel == 're-schedule-delivery'))

                            @if(hasPermission('parcel_returned_to_warehouse'))
                                <li><a href="javascript:void(0);" class="delivery-return" id="delivery-return" data-bs-toggle="modal" data-bs-target="#return-delivery"><i class="icon la la-plus"></i> <span> {{__('returned_to_warehouse')}} </span></a></li>
                            @endif

                        @endif


                        @if($parcel->status == "pending")
                            @if(hasPermission('parcel_pickup_assigned'))
                                <li><a href="javascript:void(0);" class="assign-pickup-man" id="assign-pickup-man" data-bs-toggle="modal" data-bs-target="#assign-pickup"><i class="icon la la-plus"></i> <span> {{__('assign_pickup_man')}} </span></a></li>
                            @endif
                            {{-- parcel delete --}}
                            @if(hasPermission('parcel_delete'))
                                <li><a href="javascript:void(0);" class="delete-parcel" id="delete-parcel" data-bs-toggle="modal" data-bs-target="#parcel-delete"><i class="icon  las la-trash"></i> <span> {{__('delete')}} </span></a></li>
                            @endif
                                {{-- parcel delete --}}
                        @elseif($parcel->status == "pickup-assigned" || $parcel->status == "re-schedule-pickup")
                            @if(hasPermission('parcel_reschedule_pickup'))
                                <li><a href="javascript:void(0);" class="reschedule-pickup" id="reschedule-pickup" data-bs-toggle="modal" data-bs-target="#re-schedule-pickup"><i class="icon la la-plus"></i> <span> {{__('re_schedule_pickup')}} </span></a></li>
                            @endif

                            @if(hasPermission('parcel_received'))
                                <li><a href="javascript:void(0);" class="receive-parcel-pickup" id="receive-parcel-pickup" data-bs-toggle="modal" data-bs-target="#parcel-receive-by-pickupman"><i class="icon la la-plus"></i> {{__('received_by_pickup_man')}} </span></a></li>
                            @endif

                            @if(hasPermission('parcel_received'))
                                <li><a href="javascript:void(0);" class="receive-parcel" id="receive-parcel" data-bs-toggle="modal" data-bs-target="#parcel-receive"><i class="icon la la-plus"></i> {{__('received_by_warehouse')}} </span></a></li>
                            @endif
                            {{-- parcel delete --}}
                            @if(hasPermission('parcel_delete'))
                            <li><a href="javascript:void(0);" class="delete-parcel" id="delete-parcel" data-bs-toggle="modal" data-bs-target="#parcel-delete"><i class="icon  las la-trash"></i> <span> {{__('delete')}} </span></a></li>
                            @endif
                            {{-- parcel delete --}}

                        @elseif($parcel->status == "received-by-pickup-man")

                            @if(hasPermission('parcel_received'))
                                <li><a href="javascript:void(0);" class="receive-parcel" id="receive-parcel" data-bs-toggle="modal" data-bs-target="#parcel-receive"><i class="icon la la-plus"></i> {{__('received_by_warehouse')}} </span></a></li>
                            @endif
                            @if(hasPermission('parcel_cancel'))
                                <li><a href="javascript:void(0);" class="cancel-parcel" id="cancel-parcel" data-bs-toggle="modal" data-bs-target="#parcel-cancel"><i class="las la-times"></i> <span> {{__('cancel')}} </span></a></li>
                            @endif

                        @elseif($parcel->status == "received" || $parcel->status == 'transferred-received-by-branch')
                            @if(hasPermission('parcel_transfer_to_branch'))
                                <li><a href="javascript:void(0);" class="transfer-to-branch" id="transfer-to-branch" data-url="{{ route('parcel.transfer.options') }}"  data-bs-toggle="modal" data-bs-target="#parcel-transfer-to-branch"><i class="icon la la-plus"></i> <span> {{__('transfer_to_branch')}} </span></a></li>
                            @endif
                            @if(hasPermission('parcel_delivery_assigned'))
                                <li><a href="javascript:void(0);" class="assign-delivery-man" id="assign-delivery-man" data-bs-toggle="modal" data-bs-target="#assign-delivery"><i class="icon la la-plus"></i> <span> {{__('assign_delivery_man')}} </span></a></li>
                            @endif
                            @if(hasPermission('parcel_cancel'))
                                <li><a href="javascript:void(0);" class="cancel-parcel" id="cancel-parcel" data-bs-toggle="modal" data-bs-target="#parcel-cancel"><i class="las la-times"></i> <span> {{__('cancel')}} </span></a></li>
                            @endif

                        @elseif($parcel->status == "transferred-to-branch")
                            @if(hasPermission('parcel_transfer_receive_to_branch'))
                                <li><a href="javascript:void(0);" class="transfer-receive-to-branch" id="transfer-receive-to-branch" data-bs-toggle="modal" data-bs-target="#parcel-transfer-receive-to-branch"><i class="icon la la-plus"></i> <span> {{__('transfer_receive_to_branch')}} </span></a></li>
                            @endif
                            @if(hasPermission('parcel_cancel'))
                                <li><a href="javascript:void(0);" class="cancel-parcel" id="cancel-parcel" data-bs-toggle="modal" data-bs-target="#parcel-cancel"><i class="las la-times"></i> <span> {{__('cancel')}} </span></a></li>
                            @endif
                        @elseif($parcel->status == "delivery-assigned" || $parcel->status == "re-schedule-delivery")
                            @if(hasPermission('parcel_reschedule_delivery'))
                                <li><a href="javascript:void(0);" class="reschedule-delivery" id="reschedule-delivery" data-bs-toggle="modal" data-bs-target="#re-schedule-delivery"><i class="icon la la-plus"></i> <span> {{__('re_schedule_delivery')}} </span></a></li>
                            @endif
                            @if(hasPermission('parcel_returned_to_warehouse'))
                                <li><a href="javascript:void(0);" class="delivery-return" id="delivery-return" data-bs-toggle="modal" data-bs-target="#return-delivery"><i class="icon la la-plus"></i> <span> {{__('returned_to_warehouse')}} </span></a></li>
                            @endif
                            @if(hasPermission('parcel_delivered'))
                                    <li><a href="javascript:void(0);" class="delivery-parcel-partially" id="delivery-parcel-partially" data-bs-toggle="modal" data-bs-target="#parcel-delivered-partially"><i class="icon la la-plus"></i> <span> {{__('partially-delivered')}} </span></a></li>
                                    <li><a href="javascript:void(0);" class="delivery-parcel" id="delivery-parcel" data-bs-toggle="modal" data-bs-target="#parcel-delivered"><i class="icon la la-plus"></i> <span> {{__('delivered')}} </span></a></li>
                            @endif
                            @if(hasPermission('parcel_cancel'))
                                <li><a href="javascript:void(0);" class="cancel-parcel" id="cancel-parcel" data-bs-toggle="modal" data-bs-target="#parcel-cancel"><i class="las la-times"></i> <span> {{__('cancel')}} </span></a></li>
                            @endif
                        @elseif($parcel->status == 'partially-delivered')
                            @if(hasPermission('parcel_returned_to_warehouse'))
                                <li><a href="javascript:void(0);" class="delivery-return" id="delivery-return" data-bs-toggle="modal" data-bs-target="#return-delivery"><i class="icon la la-plus"></i> <span> {{__('returned_to_warehouse')}} </span></a></li>
                            @endif
                        @elseif($parcel->status == "returned-to-warehouse")
                            @if(hasPermission('parcel_return_assigned_to_merchant'))
                                <li><a href="javascript:void(0);" class="return-assign-to-merchant" id="return-assign-to-merchant" data-bs-toggle="modal" data-bs-target="#return-assign-tomerchant"><i class="icon la la-plus"></i> <span> {{__('return_assign_to_merchant')}} </span></a></li>
                            @endif
                        @elseif($parcel->status == "return-assigned-to-merchant")
                            @if(hasPermission('parcel_returned_to_merchant'))
                                <li><a href="javascript:void(0);" class="parcel-returned-to-merchant" id="parcel-returned-to-merchant" data-bs-toggle="modal" data-bs-target="#returned-to-merchant"><i class="icon la la-plus"></i> <span> {{__('returned_to_merchant')}} </span></a></li>
                            @endif

                        @endif
                        <li><a href="{{ route('admin.parcel.detail',$parcel->id) }}"> <i class="icon las la-eye"></i><span> {{__('view')}}</span> </a></li>
                        @if(hasPermission('parcel_create'))
                            <li><a href="{{ route('admin.parcel.duplicate',$parcel->id) }}"> <i class="icon las la-copy"></i><span> {{__('duplicate')}}</span> </a></li>
                        @endif
                        @if($parcel->status == "received" || $parcel->status == 'transferred-received-by-branch')
                            @if($parcel->tracking_number == '' && hasPermission('send_to_paperfly' && $parcel->location!= 'dhaka') && ($parcel->branch_id == 1 || $parcel->branch_id == 7))
                                <li><a href="javascript:void(0);" class="create-paperfly-parcel" data-url="{{ route('admin.get-district') }}" id="create-paperfly-parcel" data-bs-toggle="modal" data-bs-target="#create-paperflyparcel"><i class="icon la la-plus"></i> <span> {{__('send_to_paperfly')}} </span></a></li>
                            @endif
                        @endif

                        <li><a href="{{ route('admin.parcel.print',$parcel->id) }}" target="_blank"> <i class="icon las la-print"></i><span> {{__('print')}}</span> </a></li>
                        <li><a href="{{ route('admin.parcel.sticker',$parcel->id) }}" target="_blank"> <i class="icon las la-print"></i><span> {{__('sticker_print')}}</span> </a></li>
                        @if($parcel->status == "pickup-assigned" || $parcel->status == "re-schedule-pickup")
                            <li><a href="{{ route('admin.parcel.notify.pickupman',$parcel->id) }}"> <i class="icon las la-bell"></i><span> {{__('notify_pickup_man')}}</span> </a></li>
                        @endif
                    </ul>
                </div>
            </div>
        </li>
    </ul>
</div>
