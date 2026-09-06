@extends('backend.layouts.master')

@section('title')
    {{ __('parcel') . ' ' . __('details') }}
@endsection
@section('style')
    <link id="skin-default" rel="stylesheet" href="{{ static_asset('admin/') }}/css/timeline.css">
    <link id="skin-default" rel="stylesheet" href="{{ static_asset('admin/') }}/css/material-design-iconic-font.min.css">
@endsection

@section('mainContent')
    <div class="container-fluid">
        <div class="row gx-20">
            <div class="col-lg-12">
                <div class="header-top d-flex justify-content-between align-items-center">
                            <h3 class="section-title">{{ __('parcel_detail') }}</h3>
                        <div class="oftions-content-right">
                            <a href="{{ url()->previous() }}" class="btn sg-btn-primary d-md-inline-flex"><i
                                    class="icon las la-arrow-left"></i><span>{{ __('back') }}</span></a>
                        </div>
                </div>
                <div >
                    <div class="row g-gs">
                        <div class="col-md-12 col-sm-12  mb-4">
                            <div class="card">
                                <div class="">
                                    <div class="card-inner">
                                        <div class="card-title-group">
                                            <div class="card-title">
                                                <h6 class="title">{{ __('id') }}: #{{ $parcel->parcel_no }}</h6>
                                            </div>
                                            <div class="card-title">
                                                <h6 class="title">
                                                    {{ __($parcel->status) }}{{ $parcel->is_partially_delivered ? '(' . __('partially-delivered') . ')' : '' }}
                                                </h6>
                                            </div>
                                            <div class="card-title">
                                                <a href="{{ route('merchant.parcel.print', $parcel->id) }}" target="_blank"
                                                    class="btn btn-icon btn-warning text-white"> <i class="icon las la-print"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="card wave">
                                <div class="card-header">{{ __('sender') }}</div>
                                <div class="card-body">
                                    <span>{{ __('merchant_name') }}:&nbsp;</span> <span
                                        class="text">{{ $parcel->merchant->user->first_name . ' ' . $parcel->merchant->user->last_name }}</span><br>
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
                                    @if ($parcel->status == 'delivery-assigned' || $parcel->status == 're-schedule-delivery')
                                        <span>{{ __('delivery_man') }}: </span> <span
                                            class="text">{{ $parcel->deliveryMan != '' ? $parcel->deliveryMan->user->first_name . ' ' . $parcel->deliveryMan->user->last_name : '' }}</span><br>
                                    @endif
                                    <div class="d-flex mt-2">
                                        <span class="btn shadow-lg">{{ __('parcel_type') }}:&nbsp;&nbsp;<p class="text">
                                                {{ __($parcel->parcel_type) }} </p></span>
                                        <span class="btn shadow-lg">{{ __('total_charge') }}:&nbsp;&nbsp;<p class="text">
                                                {{ format_price($parcel->total_delivery_charge) }} </p>
                                             </span>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <tr>
                            <div class="col-xl-6">
                                <div class="card wave">
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
                                        @if ($parcel->note != '' && $parcel->note != null)
                                            <span>{{ __('note') }}:&nbsp;&nbsp;</span> <span
                                                class="text">{{ $parcel->note }}</span><br>
                                        @endif
                                        <div class="d-flex mt-2">
                                            <span class="btn shadow-lg">{{ __('weight') }}:&nbsp;&nbsp;<p class="text">
                                                    {{ $parcel->weight . ' ' . __(setting('default_weight')) }} </p></span>
                                            <span class="btn shadow-lg">{{ __('COD') }}:&nbsp;&nbsp;<p class="text">
                                                    {{ format_price($parcel->price) }} </p> </span>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </tr>
                    </div>
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
                                                            <p>{{ __($event->title) }}</p>
                                                            <p>{{ __('delivery_man') }}:
                                                                <strong>{{ $event->deliveryPerson->user->first_name . ' ' . $event->deliveryPerson->user->last_name }}</strong>
                                                            </p>
                                                            <p>{{ __('phone_number') }}:
                                                                <strong>{{ $event->deliveryPerson->phone_number }}</strong>
                                                            </p>
                                                        </div>
                                                    @elseif($event->title == 'parcel_re_schedule_delivery_event')
                                                        <div class="cbp_tmicon bg-orange"><i
                                                                class="zmdi zmdi-time-restore"></i></div>
                                                        <div class="cbp_tmlabel">
                                                            <p>{{ __($event->title) }}</p>
                                                            <p>{{ __('delivery_man') }}:
                                                                <strong>{{ $event->deliveryPerson->user->first_name . ' ' . $event->deliveryPerson->user->last_name }}</strong>
                                                            </p>
                                                            <p>{{ __('phone_number') }}:
                                                                <strong>{{ $event->deliveryPerson->phone_number }}</strong>
                                                            </p>
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
                                                        </div>
                                                    @elseif($event->title == 'parcel_update_event')
                                                        <div class="cbp_tmicon bg-orange"><i class="zmdi zmdi-edit"></i>
                                                        </div>
                                                        <div class="cbp_tmlabel">
                                                            <p> {{ __($event->title) }}</p>
                                                        </div>
                                                    @elseif($event->title == 'parcel_re_request_event')
                                                        <div class="cbp_tmicon bg-orange"><i class="zmdi zmdi-edit"></i>
                                                        </div>
                                                        <div class="cbp_tmlabel">
                                                            <p> {{ __($event->title) }}</p>
                                                        </div>
                                                    @elseif($event->title == 'parcel_returned_to_merchant_event')
                                                        <div class="cbp_tmicon bg-green"><i class="zmdi zmdi-check"></i>
                                                        </div>
                                                        <div class="cbp_tmlabel">
                                                            <p> {{ __($event->title) }}
                                                        </div>
                                                    @else
                                                        <div class="cbp_tmicon"><i class="zmdi zmdi-check"></i></div>
                                                        <div class="cbp_tmlabel">
                                                            <p>{{ __($event->title) }}</p>
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
