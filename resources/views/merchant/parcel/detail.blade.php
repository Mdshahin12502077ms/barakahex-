@extends('backend.layouts.master')

@section('title')
    {{ __('parcel') . ' ' . __('details') }}
@endsection
@section('style')
@push('script')
    <link id="skin-default" rel="stylesheet" href="{{ static_asset('admin/')}}/css/timeline.css">
    <link id="skin-default" rel="stylesheet" href="{{ static_asset('admin/')}}/css/material-design-iconic-font.min.css">
@endpush
@endsection

@section('mainContent')
<div class="container-fluid">
    <div class="row gx-20">
        <div class="col-lg-12">
            <div class="header-top d-flex justify-content-between align-items-center mb-12">
                <h3 class="section-title">{{ __('parcel_detail') }}</h3>
                <div class="oftions-content-right">
                    <a href="{{ url()->previous() }}" class="d-flex align-items-center btn sg-btn-primary gap-2">
                        <i class="las la-arrow-left"></i>
                        <span>{{ __('back') }}</span>
                    </a>
                </div>
            </div>
            <div class="bg-white redious-border p-20 p-sm-30 pt-sm-30">
                <div class="d-flex justify-content-between mb-3">
                    <div>
                        <div class="card-title">
                            <button type="button" data-text="{{ $parcel->parcel_no }}"
                                class="copy-to-clipboard btn btn-default text-info mx-0 px-0 border-0" >#{{ $parcel->parcel_no }}</button>
                        </div>
                        <div class="card-title">
                            <h6 class="title">
                                {{ $parcel->status == 'delivery-assigned' && $parcel->location != 'dhaka' ? __('in_transit') : __($parcel->status) }}{{ $parcel->is_partially_delivered ? '(' . __('partially-delivered') . ')' : '' }}
                            </h6>
                        </div>
                    </div>
                    <div>
                        <div class="card-title">
                            <a href="{{ Sentinel::getUser()->user_type == 'merchant' ? route('merchant.parcel.print', $parcel->id) : route('merchant.staff.parcel.print', $parcel->id) }}"
                                target="_blank" class="btn btn-icon btn-warning text-white"> <i
                                    class="icon las la-print"></i> </a>
                        </div>
                    </div>
                </div>
                <div class="row d-flex">
                    <div class="col-xl-6">
                        <div class="card wave parcel__details d-flex flex-column h-100">
                            <div class="card-header">{{ __('sender') }}</div>
                            <div class="card-body">
                                <span>{{ __('merchant_name') }}:&nbsp;</span> <span
                                    class="text">{{ @$parcel->merchant_id == 1802 && $parcel->user->user_type == 'merchant_staff' ? @$parcel->user->first_name . ' ' . @$parcel->user->last_name : $parcel->merchant->user->first_name . ' ' . $parcel->merchant->user->last_name }}</span><br>
                                <span>{{ __('company_name') }}:&nbsp;</span> <span
                                    class="text">{{ $parcel->merchant->company }}</span><br>
                                <span>{{ __('pickup_number') }}:&nbsp;</span> <span
                                    class="text">{{ $parcel->pickup_shop_phone_number }}</span><br>

                                <span>{{ __('pickup_address') }}:&nbsp;</span> <span
                                    class="text">{{ $parcel->pickup_address }}</span><br>
                                <span>{{ __('email') }}:&nbsp;&nbsp;</span> <span
                                    class="text">{{ $parcel->merchant->user->email }}</span><br>

                                <span>{{ __('created_at') }}:&nbsp;</span> <span
                                    class="text">{{ date('M d, Y h:i a', strtotime($parcel->created_at)) }}</span><br>
                                @if ($parcel->parcel_type == 'frozen')
                                    <span>{{ __('pickup') }}: </span> <span
                                        class="text">{{ $parcel->pickup_date != '' ? date('M d, Y', strtotime($parcel->pickup_date)) : '' }}
                                        {{ $parcel->pickup_time != '' ? date('h:i a', strtotime($parcel->pickup_time)) : '' }}</span>
                                    <span>{{ __('delivery') }}: </span> <span
                                        class="text">{{ $parcel->delivery_date != '' ? date('M d, Y', strtotime($parcel->delivery_date)) : '' }}
                                        {{ $parcel->pickup_time != '' ? date('h:i a', strtotime($parcel->pickup_time)) : '' }}</span><br>
                                @else
                                    <span>{{ __('pickup') }}: </span> <span
                                        class="text">{{ $parcel->pickup_date != '' ? date('M d, Y', strtotime($parcel->pickup_date)) : '' }}</span><br>
                                    <span>{{ __('delivery') }}: </span> <span
                                        class="text">{{ $parcel->delivery_date != '' ? date('M d, Y', strtotime($parcel->delivery_date)) : '' }}</span><br>
                                @endif
                                @if ($parcel->status == 'delivered' || $parcel->status == 'delivered-and-verified')
                                    <span>{{ __('delivered_at') }}: </span> <span
                                        class="text">{{ $parcel->event != '' ? date('M d, Y g:i A', strtotime($parcel->event->created_at)) : '' }}</span><br>
                                    <span>{{ __('delivered_by') }}: </span> <span
                                        class="text">{{ $parcel->deliveryMan->user->first_name . ' ' . $parcel->deliveryMan->user->last_name }}</span><br>
                                @endif
                                @if ($parcel->status == 'received')
                                    <span>{{ __('pickup_by') }}: </span> <span
                                        class="text">{{ $parcel->pickupMan->user->first_name . ' ' . $parcel->pickupMan->user->last_name }}</span><br>
                                @endif
                                @if ($parcel->status == 'pickup-assigned' || $parcel->status == 're-schedule-pickup')
                                    <span>{{ __('pickup_man') }}: </span> <span
                                        class="text">{{ $parcel->pickupMan != '' ? $parcel->pickupMan->user->first_name . ' ' . $parcel->pickupMan->user->last_name : '' }}</span><br>
                                @endif
                                @if (
                                    ($parcel->status == 'delivery-assigned' || $parcel->status == 're-schedule-delivery'))
                                    <span>{{ __('delivery_man') }}: </span> <span
                                        class="text">{{ $parcel->deliveryMan != '' ? $parcel->deliveryMan->user->first_name . ' ' . $parcel->deliveryMan->user->last_name : '' }}</span><br>
                                @endif

                                <div class="action__btn mt-4">
                                    <span class="btn border">{{__('parcel_type')}}:&nbsp;&nbsp;<p
                                            class="text">{{ __($parcel->parcel_type) }} </p></span>
                                    <span class="btn border">{{ __('total_charge') }}:&nbsp;&nbsp;<p
                                            class="text">{{ format_price($parcel->total_delivery_charge)}} </p></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-6">
                        <div class="card wave parcel__details d-flex flex-column h-100">
                            <div class="card-header">{{ __('receiver') }}</div>
                            <div class="card-body">
                                <span>{{ __('id') }}:&nbsp;&nbsp;</span> <span
                                    class="text">#{{ $parcel->parcel_no }}</span><br>
                                <span>{{ __('invno') }}:&nbsp;&nbsp;</span> <span
                                    class="text">{{ $parcel->customer_invoice_no }}</span><br>
                                <span>{{ __('customer_name') }}:&nbsp;&nbsp;</span> <span
                                    class="text">{{ $parcel->customer_name }}</span><br>
                                <span>{{ __('customer') . ' ' . __('phone') }}:&nbsp;&nbsp;</span> <span
                                    class="text">{{ $parcel->customer_phone_number }}</span><br>

                                <span>{{ __('customer') . ' ' . __('address') }}:&nbsp;&nbsp;</span> <span
                                    class="text">{{ $parcel->customer_address }}</span><br>
                                <span>{{ __('location') }}:&nbsp;&nbsp;</span> <span
                                    class="text">{{ __($parcel->location) }}</span><br>
                                @if (!blank($parcel->destinationBranch))
                                    <span>{{ __('target_branch') }}:&nbsp;&nbsp;</span>
                                    <span class="badge badge-info bg-info text-white font-12">{{ $parcel->destinationBranch->name }}</span><br>
                                @endif
                                @if ($parcel->note != '' && $parcel->note != null)
                                    <span>{{ __('note') }}:&nbsp;&nbsp;</span> <span
                                        class="text">{{ $parcel->note }}</span><br>
                                @endif

                                <div class="action__btn mt-4">
                                    <span class="btn border">{{__('weight')}}:&nbsp;&nbsp;<p
                                            class="text">{{ $parcel->weight.' '.__(setting('default_weight')) }} </p></span>
                                    <span class="btn border">{{ __('COD') }}:&nbsp;&nbsp;<p
                                            class="text">{{ format_price($parcel->price)}} </p></span>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                @if(!empty($parcel->movements) && $parcel->movements->count() > 0)
                    <div class="card mt-4 border-0 shadow-sm">
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-bold"><i class="las la-route text-primary me-2"></i>{{ __('hub_and_branch_movement_history') }}</h5>
                            <span class="badge bg-primary text-white">{{ $parcel->movements->count() }} {{ __('movements') }}</span>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('from_branch') }}</th>
                                            <th>{{ __('to_branch') }}</th>
                                            <th>{{ __('sent_at') }}</th>
                                            <th>{{ __('received_at') }}</th>
                                            <th>{{ __('status') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($parcel->movements as $key => $movement)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>
                                                    <div class="fw-bold">{{ @$movement->fromBranch->name ?? 'N/A' }}</div>
                                                    <small class="text-muted">{{ @$movement->fromBranch->address }}</small>
                                                </td>
                                                <td>
                                                    <div class="fw-bold">{{ @$movement->toBranch->name ?? 'N/A' }}</div>
                                                    <small class="text-muted">{{ @$movement->toBranch->address }}</small>
                                                </td>
                                                <td>
                                                    <small class="text-muted"><i class="las la-clock"></i> {{ $movement->sent_at ? date('M d, Y h:i A', strtotime($movement->sent_at)) : '-' }}</small>
                                                </td>
                                                <td>
                                                    @if ($movement->status == 'received')
                                                        <small class="text-success"><i class="las la-check-circle"></i> {{ $movement->received_at ? date('M d, Y h:i A', strtotime($movement->received_at)) : '-' }}</small>
                                                    @else
                                                        <span class="badge bg-warning text-dark"><i class="las la-hourglass-half"></i> {{ __('pending_receive') }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($movement->status == 'received')
                                                        <span class="badge bg-success text-white"><i class="las la-check"></i> {{ __('received') }}</span>
                                                    @elseif ($movement->status == 'in_transit')
                                                        <span class="badge bg-warning text-dark"><i class="las la-shipping-fast"></i> {{ __('in_transit') }}</span>
                                                    @else
                                                        <span class="badge bg-secondary text-white">{{ __($movement->status) }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($parcel->events())
                    <div class="card history mt-5">
                        <div class="card-body">
                            <div class="row history-row">
                                <div class="col-lg-10">
                                    <ul class="cbp_tmtimeline">
                                        @foreach ($parcel->events as $event)
                                            <li>
                                                <time class="cbp_tmtime"
                                                    datetime="2017-11-04T18:30"><span>{{ date('M d, Y', strtotime($event->created_at)) }}</span>
                                                    <span>{{ date('h:i a', strtotime($event->created_at)) }}</span></time>
                                                @if ($event->title == 'parcel_cancel_event')
                                                    <div class="cbp_tmicon bg-blush"><i class="zmdi zmdi-close"></i>
                                                    </div>
                                                    <div class="cbp_tmlabel empty">
                                                        <p>{{ __($event->title) }}</p>
                                                        <p><strong>{{ $event->cancel_note != '' ? __('reason') . ': ' . $event->cancel_note : '' }}</strong>
                                                        </p>
                                                    </div>
                                                @elseif($event->title == 'assign_pickup_man_event')
                                                    <div class="cbp_tmicon bg-info"><i class="zmdi zmdi-label"></i>
                                                    </div>
                                                    <div class="cbp_tmlabel">
                                                        <p> {{ __($event->title) }}</p>
                                                        <p>{{ $event->cancel_note != '' ? __('note') . ': ' . $event->cancel_note : '' }}
                                                        </p>
                                                        @if (@$event->pickupPerson)
                                                            <p>{{ __('pickup_man') }}: <strong><span
                                                                        class="text">{{ $event->pickupPerson->user->first_name . ' ' . $event->pickupPerson->user->last_name }}</span></strong>
                                                            </p>
                                                            <p>{{ __('phone_number') }}: <strong><span
                                                                        class="text">{{ $event->pickupPerson->phone_number }}</span></strong>
                                                            </p>
                                                        @endif
                                                    </div>
                                                @elseif($event->title == 'parcel_re_schedule_pickup_event')
                                                    <div class="cbp_tmicon bg-orange"><i
                                                            class="zmdi zmdi-time-restore"></i></div>
                                                    <div class="cbp_tmlabel">
                                                        <p> {{ __($event->title) }}</p>
                                                        <p>{{ $event->cancel_note != '' ? __('note') . ': ' . $event->cancel_note : '' }}
                                                        </p>
                                                        @if (@$event->pickupPerson)
                                                            <p>{{ __('pickup_man') }}:
                                                                <strong>{{ $event->pickupPerson->user->first_name . ' ' . $event->pickupPerson->user->last_name }}</strong>
                                                            </p>
                                                            <p>{{ __('phone_number') }}: <strong><span
                                                                        class="text">{{ $event->pickupPerson->phone_number }}</strong>
                                                            </p>
                                                        @endif
                                                    </div>
                                                @elseif($event->title == 'assign_delivery_man_event')
                                                    <div class="cbp_tmicon bg-info"><i class="zmdi zmdi-label"></i>
                                                    </div>
                                                    <div class="cbp_tmlabel">

                                                        @if ($parcel->location == 'dhaka')
                                                            <p>{{ __($event->title) }}</p>
                                                            <p>{{ $event->cancel_note != '' ? __('note') . ': ' . $event->cancel_note : '' }}
                                                            </p>
                                                            <p>{{ __('delivery_man') }}:
                                                                <strong>{{ $event->deliveryPerson->user->first_name . ' ' . $event->deliveryPerson->user->last_name }}</strong>
                                                            </p>
                                                            <p>{{ __('phone_number') }}:
                                                                <strong>{{ $event->deliveryPerson->phone_number }}</strong>
                                                            </p>
                                                        @else
                                                            <p>{{ __('in_transit') }}</p>
                                                            <p>{{ $event->cancel_note != '' ? __('note') . ': ' . $event->cancel_note : '' }}
                                                            </p>
                                                        @endif
                                                    </div>
                                                @elseif($event->title == 'parcel_re_schedule_delivery_event')
                                                    <div class="cbp_tmicon bg-orange"><i
                                                            class="zmdi zmdi-time-restore"></i></div>
                                                    <div class="cbp_tmlabel">
                                                        @if ($parcel->location == 'dhaka')
                                                            <p>{{ __($event->title) }}</p>
                                                            <p>{{ $event->cancel_note != '' ? __('note') . ': ' . $event->cancel_note : '' }}
                                                            </p>
                                                            <p>{{ __('delivery_man') }}:
                                                                <strong>{{ $event->deliveryPerson->user->first_name . ' ' . $event->deliveryPerson->user->last_name }}</strong>
                                                            </p>
                                                            <p>{{ __('phone_number') }}:
                                                                <strong>{{ $event->deliveryPerson->phone_number }}</strong>
                                                            </p>
                                                        @else
                                                            <p>{{ __($event->title) }}</p>
                                                            <p>{{ $event->cancel_note != '' ? __('note') . ': ' . $event->cancel_note : '' }}
                                                            </p>
                                                        @endif
                                                    </div>
                                                @elseif($event->title == 'parcel_return_to_wirehouse')
                                                    <div class="cbp_tmicon bg-orange"><i class="icon las la-undo"></i>
                                                    </div>
                                                    <div class="cbp_tmlabel">{{ __($event->title) }}</div>
                                                @elseif($event->title == 'parcel_return_assign_to_merchant_event')
                                                    <div class="cbp_tmicon bg-info"><i class="zmdi zmdi-label"></i>
                                                    </div>
                                                    <div class="cbp_tmlabel">
                                                        <p>{{ __($event->title) }}</p>
                                                        <p>{{ $event->cancel_note != '' ? __('note') . ': ' . $event->cancel_note : '' }}
                                                        </p>

                                                        @if (@$event->deliveryPerson)
                                                            <p>{{ __('delivery_man') }}:
                                                                <strong>{{ $event->deliveryPerson->user->first_name . ' ' . $event->deliveryPerson->user->last_name }}</strong>
                                                            </p>
                                                            <p>{{ __('phone_number') }}:
                                                                <strong>{{ $event->deliveryPerson->phone_number }}</strong>
                                                            </p>
                                                        @endif
                                                    </div>
                                                @elseif($event->title == 'parcel_delivered_event')
                                                    <div class="cbp_tmicon bg-green"><i class="zmdi zmdi-check"></i>
                                                    </div>
                                                    <div class="cbp_tmlabel">
                                                        <p>{{ __($event->title) }} </p>
                                                        <p>{{ $event->cancel_note != '' ? __('note') . ': ' . $event->cancel_note : '' }}
                                                        </p>
                                                        @if (@$event->deliveryPerson)
                                                            <p>{{ __('delivery_man') }}:
                                                                <strong>{{ $event->deliveryPerson->user->first_name . ' ' . $event->deliveryPerson->user->last_name }}</strong>
                                                            </p>
                                                            <p>{{ __('phone_number') }}:
                                                                <strong>{{ $event->deliveryPerson->phone_number }}</strong>
                                                            </p>
                                                        @endif
                                                    </div>
                                                @elseif($event->title == 'parcel_received_event')
                                                    <div class="cbp_tmicon bg-green"><i class="zmdi zmdi-store"></i>
                                                    </div>
                                                    <div class="cbp_tmlabel">
                                                        <p>{{ __($event->title) }}</p>
                                                        <p>{{ $event->cancel_note != '' ? __('note') . ': ' . $event->cancel_note : '' }}
                                                        </p>
                                                    </div>
                                                @elseif($event->title == 'parcel_update_event')
                                                    <div class="cbp_tmicon bg-orange"><i class="zmdi zmdi-edit"></i>
                                                    </div>
                                                    <div class="cbp_tmlabel">
                                                        <p> {{ __($event->title) }}</p>
                                                        <p>{{ $event->cancel_note != '' ? __('note') . ': ' . $event->cancel_note : '' }}
                                                        </p>
                                                    </div>
                                                @elseif($event->title == 'parcel_re_request_event')
                                                    <div class="cbp_tmicon bg-orange"><i class="zmdi zmdi-edit"></i>
                                                    </div>
                                                    <div class="cbp_tmlabel">
                                                        <p> {{ __($event->title) }}</p>
                                                        <p>{{ $event->cancel_note != '' ? __('note') . ': ' . $event->cancel_note : '' }}
                                                        </p>
                                                    </div>
                                                @elseif($event->title == 'parcel_returned_to_merchant_event')
                                                    <div class="cbp_tmicon bg-green"><i class="zmdi zmdi-check"></i>
                                                    </div>
                                                    <div class="cbp_tmlabel">
                                                        <p> {{ __($event->title) }}</p>
                                                        <p>{{ $event->cancel_note != '' ? __('note') . ': ' . $event->cancel_note : '' }}
                                                        </p>
                                                    </div>
                                                @else
                                                    <div class="cbp_tmicon"><i class="zmdi zmdi-check"></i></div>
                                                    <div class="cbp_tmlabel">
                                                        <p>{{ __($event->title) }}</p>
                                                        <p>{{ $event->cancel_note != '' ? __('note') . ': ' . $event->cancel_note : '' }}
                                                        </p>
                                                    </div>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
