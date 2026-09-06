@extends('backend.layouts.master')

@section('title')
    {{ __('parcel') . ' ' . __('lists') }}
@endsection
@section('style')
    <style>
        .text-fliter-btn {
            color: #8599b1 !important;
        }
    </style>
@endsection

@section('mainContent')
    <div class="container-fluid">
        <div class="row gx-20">
            <div class="col-lg-12">
                <div class="header-top d-flex justify-content-between align-items-center">
                    <h3 class="section-title">{{ __('lists') }}</h3>
                    <div class="nk-block-des text-soft">
                        <p>{{ __('you_have_total') }} {{ $parcels->total() }} {{ __('parcel') }}.</p>
                    </div>
                    <div class="oftions-content-right">
                        @if (@settingHelper('preferences')->where('title', 'create_parcel')->first()->merchant)
                            <a href="{{ route('merchant.parcel.create') }}" class="btn sg-btn-primary d-md-inline-flex"><i
                                    class="icon la la-plus"></i><span>{{ __('create_parcel') }}</span></a>
                        @else
                            <button class="btn btn-danger d-md-inline-flex"><i
                                    class="icon la la-plus"></i><span>{{ __('create') . ' ' . __('parcel') . ' (' . __('service_unavailable') . ')' }}</span></button>
                        @endif
                    </div>
                </div>
                <div>
                    <div class="bg-white redious-border p-20 p-sm-30 pt-sm-30">
                        <div class="card-inner-group">
                            <div class="card-inner position-relative card-tools-toggle">
                                <div class="card-title-group">
                                    <div class="card-tools">
                                        <div class="row filter-button mb-2">
                                            <div class="col-md-12">
                                                <a href="{{ route('merchant.parcel') }}"
                                                    class="badge {{ isset($slug) ? 'text-fliter-btn' : 'btn-primary' }}"><span>{{ __('all') }}</span></a>
                                                <a href="{{ route('merchant.parcel.filtering', 'pending') }}"
                                                    class="badge {{ isset($slug) ? ($slug == 'pending' ? 'btn-primary' : 'text-fliter-btn') : 'text-fliter-btn' }}"><span>{{ __('pending') }}</span></a>
                                                <a href="{{ route('merchant.parcel.filtering', 'received') }}"
                                                    class="badge {{ isset($slug) ? ($slug == 'received' ? 'btn-primary' : 'text-fliter-btn') : 'text-fliter-btn' }}"><span>{{ __('received_by_warehouse') }}</span></a>
                                                <a href="{{ route('merchant.parcel.filtering', 'delivered') }}"
                                                    class="badge {{ isset($slug) ? ($slug == 'delivered' ? 'btn-primary' : 'text-fliter-btn') : 'text-fliter-btn' }}"><span>{{ __('delivered') }}</span></a>
                                                <a href="{{ route('merchant.parcel.filtering', 'cancel') }}"
                                                    class="badge {{ isset($slug) ? ($slug == 'cancel' ? 'btn-primary' : 'text-fliter-btn') : 'text-fliter-btn' }}"><span>{{ __('cancelled') }}</span></a>
                                                <a href="{{ route('merchant.parcel.filtering', 'deleted') }}"
                                                    class="badge {{ isset($slug) ? ($slug == 'deleted' ? 'btn-primary' : 'text-fliter-btn') : 'text-fliter-btn' }}"><span>{{ __('deleted') }}</span></a>
                                            </div>
                                        </div>

                                    </div><!-- .card-tools -->
                                    <div class="card-tools mr-n1">
                                        <ul class="btn-toolbar gx-1">
                                            <li>
                                                <form action="{{ route('merchant.parcel.filter') }}" method="GET">
                                                    {{-- @csrf --}}
                                                    <ul class="btn-toolbar gx-1">
                                                        <li>
                                                            <div class="dropdown">
                                                                <a href="#" class="btn btn-trigger btn-icon dropdown-toggle"
                                                                    data-toggle="dropdown">
                                                                    <div class="dot dot-primary"></div>
                                                                    <i class="icon las la-filter"></i>
                                                                </a>
                                                                <div
                                                                    class="filter-wg dropdown-menu dropdown-menu-xl dropdown-menu-right">
                                                                    <div class="dropdown-head">
                                                                        <span
                                                                            class="sub-title dropdown-title">{{ __('filter') }}</span>

                                                                    </div>
                                                                    <div class="dropdown-body dropdown-body-rg">
                                                                        <div class="row gx-6 gy-3">
                                                                            <div class="col-12">
                                                                                <div class="mb-3">
                                                                                    <input type="text" class="form-control"
                                                                                        name="customer_name"
                                                                                        placeholder="{{ __('enter') . ' ' . __('customer_name') }}">
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-12">
                                                                                <div class="mb-3">
                                                                                    <input type="text" class="form-control"
                                                                                        name="customer_invoice_no"
                                                                                        placeholder="{{ __('enter') . ' ' . __('invoice_no') }}">
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-12">
                                                                                <div class="mb-3">
                                                                                    <input type="text" class="form-control"
                                                                                        name="phone_number"
                                                                                        placeholder="{{ __('enter') . ' ' . __('phone_number') }}">
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-12">
                                                                                <div class="mb-3">
                                                                                    <input type="text" name="created_at"
                                                                                        class="form-control date-picker"
                                                                                        id="created_at">
                                                                                    <label class="form-label-outlined"
                                                                                        for="created_at">{{ __('created_at') }}</label>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-12">
                                                                                <div class="mb-3">
                                                                                    <input type="text" name="pickup_date"
                                                                                        class="form-control date-picker"
                                                                                        id="pickup_date">
                                                                                    <label class="form-label-outlined"
                                                                                        for="pickup_date">{{ __('pickup_date') }}</label>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-12">
                                                                                <div class="mb-3">
                                                                                    <input type="text" name="delivery_date"
                                                                                        class="form-control date-picker"
                                                                                        id="delivery_date">
                                                                                    <label class="form-label-outlined"
                                                                                        for="delivery_date">{{ __('delivery_date') }}</label>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-12">
                                                                                <div class="mb-3">
                                                                                    <input type="text" name="delivered_date"
                                                                                        class="form-control date-picker"
                                                                                        id="delivered_date">
                                                                                    <label class="form-label-outlined"
                                                                                        for="delivered_date">{{ __('delivered_date') }}</label>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-12">
                                                                                <div class="mb-3">
                                                                                    <select
                                                                                        class="form-select form-select-sm"
                                                                                        name="status">
                                                                                        <option value="any">
                                                                                            {{ __('any_status') }}
                                                                                        </option>
                                                                                        @foreach (\Config::get('parcel.parcel_status') as $parcel_status)
                                                                                            <option
                                                                                                value="{{ $parcel_status }}">
                                                                                                {{ __($parcel_status) }}
                                                                                            </option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-12">
                                                                                <div class="mb-3">
                                                                                    <select
                                                                                        class="form-select form-select-sm"
                                                                                        name="weight">
                                                                                        <option value="any">
                                                                                            {{ __('any_weight') }}
                                                                                        </option>
                                                                                        @foreach ($charges as $charge)
                                                                                            <option
                                                                                                value="{{ $charge->weight }}">
                                                                                                {{ $charge->weight }}
                                                                                            </option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-12">
                                                                                <div class="mb-3">
                                                                                    <select
                                                                                        class="form-select form-select-sm"
                                                                                        name="parcel_type">
                                                                                        <option value="any">
                                                                                            {{ __('any_type') }}
                                                                                        </option>
                                                                                        <option value="same_day">
                                                                                            {{ __('same_day') }}
                                                                                        </option>
                                                                                        <!-- <option value="next_day">
                                                                                                {{ __('next_day') }}</option> -->
                                                                                        <option value="sub_city">
                                                                                            {{ __('sub_city') }}
                                                                                        </option>
                                                                                        <option value="sub_urban_area">
                                                                                            {{ __('sub_urban_area') }}
                                                                                        </option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-12">
                                                                                <div class="mb-3">
                                                                                    <select
                                                                                        class="form-select form-select-sm"
                                                                                        name="location">
                                                                                        <option value="any">
                                                                                            {{ __('any_location') }}
                                                                                        </option>
                                                                                        @foreach ($cod_charges as $cod_charge)
                                                                                            <option
                                                                                                value="{{ $cod_charge->location }}">
                                                                                                {{ __($cod_charge->location) }}
                                                                                            </option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-12 text-right">
                                                                                <div class="d-flex justify-content-between">
                                                                                    <div class="mb-3">
                                                                                        <button type="submit"
                                                                                            name="download" value="1"
                                                                                            class="btn sg-btn-primary">{{ __('download') }}</button>
                                                                                    </div>
                                                                                    <div class="mb-3">
                                                                                        <button type="submit"
                                                                                            class="btn sg-btn-primary">{{ __('filter') }}</button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div><!-- .filter-wg -->
                                                            </div><!-- .dropdown -->
                                                        </li><!-- li -->
                                                    </ul><!-- .btn-toolbar -->
                                                </form>
                                            </li><!-- li -->
                                        </ul><!-- .btn-toolbar -->
                                    </div><!-- .card-tools -->
                                </div><!-- .card-title-group -->
                            </div>
                            <div class="card-inner p-0">
                                <div class="nk-tb-list nk-tb-ulist">
                                    <div class="nk-tb-item nk-tb-head">
                                        <div class="nk-tb-col"><span class="sub-text"><strong>#</strong></span></div>
                                        <div class="nk-tb-col"><span class="sub-text"><strong>{{ __('no') }}</strong></span>
                                        </div>
                                        <div class="nk-tb-col tb-col-lg"><span
                                                class="sub-text"><strong>{{ __('merchant') }}</strong></span></div>
                                        <div class="nk-tb-col tb-col-lg"><span
                                                class="sub-text"><strong>{{ __('customer') }}</strong></span></div>
                                        <div class="nk-tb-col"><span
                                                class="sub-text"><strong>{{ __('status') }}</strong></span></div>

                                        <div class="nk-tb-col"><span
                                                class="sub-text"><strong>{{ __('options') }}</strong></span></div>
                                    </div>

                                    @foreach ($parcels as $key => $parcel)
                                        <div class="nk-tb-item" id="row_{{ $parcel->id }}">
                                            <input type="hidden" value="{{ $parcel->id }}" id="id">
                                            <div class="nk-tb-col">
                                                <span class="pl-3">{{ $key + 1 }}</span>
                                            </div>

                                            <div class="nk-tb-col">
                                                <a href="{{ route('merchant.parcel.detail', $parcel->id) }}">
                                                    <table>
                                                        <tr>
                                                            <td>{{ __('id') }}:#{{ $parcel->parcel_no }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>{{ __('invno') }}:{{ $parcel->customer_invoice_no }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>{{ $parcel->created_at != '' ? date('M d, Y h:i a', strtotime($parcel->created_at)) : '' }}
                                                            </td>
                                                        </tr>
                                                        @if ($parcel->parcel_type == 'frozen')
                                                            <tr>
                                                                <td class="text-primary">{{ __('pickup') }}:
                                                                    {{ $parcel->pickup_date != '' ? date('M d, Y', strtotime($parcel->pickup_date)) : '' }}
                                                                    {{ $parcel->pickup_time != '' ? date('h:i a', strtotime($parcel->pickup_time)) : '' }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-primary">{{ __('delivery') }}:
                                                                    {{ $parcel->delivery_date != '' ? date('M d, Y', strtotime($parcel->delivery_date)) : '' }}
                                                                    {{ $parcel->pickup_time != '' ? date('h:i a', strtotime($parcel->pickup_time)) : '' }}
                                                                </td>
                                                            </tr>
                                                        @else
                                                            <tr>
                                                                <td class="text-primary">{{ __('pickup') }}:
                                                                    {{ $parcel->pickup_date != '' ? date('M d, Y', strtotime($parcel->pickup_date)) : '' }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-primary">{{ __('delivery') }}:
                                                                    {{ $parcel->delivery_date != '' ? date('M d, Y', strtotime($parcel->delivery_date)) : '' }}
                                                                </td>
                                                            </tr>
                                                        @endif
                                                        @if ($parcel->status == 'delivered' || $parcel->status == 'delivered-and-verified')
                                                            <tr>
                                                                <td class="text-primary">{{ __('delivered_at') }}:
                                                                    {{ $parcel->event != '' ? date('M d, Y g:i A', strtotime($parcel->event->created_at)) : '' }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-primary">{{ __('delivered_by') }}:
                                                                    {{ $parcel->deliveryMan->user->first_name . ' ' . $parcel->deliveryMan->user->last_name }}
                                                                </td>
                                                            </tr>
                                                        @endif
                                                        @if ($parcel->status == 'received')
                                                            <tr>
                                                                <td class="text-primary">{{ __('pickup_by') }}:
                                                                    {{ $parcel->pickupMan->user->first_name . ' ' . $parcel->pickupMan->user->last_name }}
                                                                </td>
                                                            </tr>
                                                        @endif
                                                        @if ($parcel->status == 'pickup-assigned' || $parcel->status == 're-schedule-pickup')
                                                            {{-- .date('M d, Y g:i A', strtotime($parcel->pickupPerson->created_at))
                                                            --}}
                                                            <tr>
                                                                <td class="text-primary">{{ __('pickup_man') }}:
                                                                    {{ $parcel->pickupMan != '' ? $parcel->pickupMan->user->first_name . ' ' . $parcel->pickupMan->user->last_name : '' }}
                                                                </td>
                                                            </tr>
                                                        @endif
                                                        @if ($parcel->status == 'return-assigned-to-merchant')
                                                            <tr>
                                                                <td class="text-primary">{{ __('returned_at') }}:
                                                                    {{ $parcel->event != '' ? date('M d, Y h:i a', strtotime($parcel->event->created_at)) : '' }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-primary">{{ __('return_delivery_man') }}:
                                                                    {{ $parcel->returnDeliveryMan != '' ? $parcel->returnDeliveryMan->user->first_name . ' ' . $parcel->returnDeliveryMan->user->last_name : '' }}
                                                                </td>
                                                            </tr>
                                                        @endif
                                                        @if ($parcel->status == 'returned-to-merchant')
                                                            <tr>
                                                                <td class="text-primary">{{ __('returned_at') }}:
                                                                    {{ $parcel->returnEvent != '' ? date('M d, Y h:i a', strtotime($parcel->returnEvent->created_at)) : '' }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-primary">{{ __('returned_by') }}:
                                                                    {{ $parcel->returnDeliveryMan != '' ? $parcel->returnDeliveryMan->user->first_name . ' ' . $parcel->returnDeliveryMan->user->last_name : '' }}
                                                                </td>
                                                            </tr>
                                                        @endif
                                                        @if ($parcel->status == 'delivery-assigned' || $parcel->status == 're-schedule-delivery')
                                                            {{-- .date('M d, Y g:i A',
                                                            strtotime($parcel->deliveryPerson->created_at)) --}}
                                                            <tr>
                                                                <td class="text-primary">{{ __('delivery_man') }}:
                                                                    {{ $parcel->deliveryMan != '' ? $parcel->deliveryMan->user->first_name . ' ' . $parcel->deliveryMan->user->last_name : '' }}
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    </table>
                                                </a>
                                            </div>

                                            <div class="nk-tb-col tb-col-lg column-max-width">
                                                <table width="70%">
                                                    <tr>
                                                        <td>{{ @$parcel->merchant->company }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>{{ @$parcel->pickup_shop_phone_number }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>{{ @$parcel->pickup_address }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <table class="text-primary" width="100%">
                                                                <tr>
                                                                    <td width="50%">
                                                                        {{ __('weight') . ': ' . $parcel->weight . __('kg') }}
                                                                    </td>
                                                                    <td width="50%">
                                                                        {{ __('charge') . ': ' . format_price($parcel->total_delivery_charge)  }}
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td width="50%">
                                                                        {{ __('COD') . ': ' . format_price($parcel->price)  }}
                                                                    </td>
                                                                    <td width="50%">
                                                                        {{ __('payable') . ': ' . format_price($parcel->payable)  }}
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td width="50%">
                                                                        {{ __('selling_price') . ': ' . format_price($parcel->selling_price)  }}
                                                                    </td>
                                                                </tr>
                                                            </table>

                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div class="nk-tb-col tb-col-lg column-max-width">
                                                <table width="70%">
                                                    <tr>
                                                        <td>{{ @$parcel->customer_name }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>{{ @$parcel->customer_phone_number }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>{{ @$parcel->customer_address }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <table class="text-primary" width="100%">
                                                                <tr>
                                                                    <td width="50%">
                                                                        {{ __('location') . ': ' . __($parcel->location) }}
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div class="nk-tb-col column-max-width">
                                                @if ($parcel->status == 'pending')
                                                    <span class="badge text-warning">{{ __($parcel->status) }}</span><br>
                                                @elseif($parcel->status == 'deleted')
                                                    <span class="badge text-danger">{{ __('deleted') }}</span><br>
                                                @elseif($parcel->status == 'cancel')
                                                    <span class="badge text-danger">{{ __('cancelled') }}</span><br>
                                                @elseif($parcel->status == 'pickup-assigned')
                                                    <span class="badge text-pink">{{ __('pickup-assigned') }}</span><br>
                                                @elseif($parcel->status == 're-schedule-pickup')
                                                    <span class="badge text-purple">{{ __('re-schedule-pickup') }}</span><br>
                                                @elseif($parcel->status == 'received-by-pickup-man')
                                                    <span class="badge text-yale">{{ __('received-by-pickup-man') }}</span><br>
                                                @elseif($parcel->status == 'received')
                                                    <span class="badge text-blue">{{ __('received_by_warehouse') }}</span><br>
                                                @elseif($parcel->status == 'transferred-to-branch')
                                                    <span class="badge text-pigeon">{{ __('transferred-to-branch') }}</span><br>
                                                @elseif($parcel->status == 'transferred-received-by-branch')
                                                    <span
                                                        class="badge text-prussian">{{ __('transferred-received-by-branch') }}</span><br>
                                                @elseif($parcel->status == 'delivery-assigned')
                                                    <span class="badge text-pear">{{ __('delivery-assigned') }}</span><br>
                                                @elseif($parcel->status == 're-schedule-delivery')
                                                    <span class="badge text-brown">{{ __('re-schedule-delivery') }}</span><br>
                                                @elseif($parcel->status == 'returned-to-warehouse')
                                                    @if ($parcel->is_partially_delivered)
                                                        <span class="badge text-mint">{{ __('partially-delivered') }}</span><br>
                                                    @endif
                                                    <span class="badge text-warning">{{ __('returned-to-warehouse') }}</span><br>
                                                @elseif($parcel->status == 'return-assigned-to-merchant')
                                                    @if ($parcel->is_partially_delivered)
                                                        <span class="badge text-mint">{{ __('partially-delivered') }}</span><br>
                                                    @endif
                                                    <span
                                                        class="badge text-indigo">{{ __('return-assigned-to-merchant') }}</span><br>
                                                @elseif($parcel->status == 'partially-delivered')
                                                    <span class="badge text-mint">{{ __('partially-delivered') }}</span><br>
                                                @elseif($parcel->status == 'delivered')
                                                    <span class="badge text-success">{{ __('delivered') }}</span><br>
                                                @elseif($parcel->status == 'delivered-and-verified')
                                                    <span class="badge text-success">{{ __('delivered-and-verified') }}</span><br>
                                                @elseif($parcel->status == 'returned-to-merchant')
                                                    @if ($parcel->is_partially_delivered)
                                                        <span class="badge text-mint">{{ __('partially-delivered') }}</span><br>
                                                    @endif
                                                    <span class="badge text-success">{{ __('returned-to-merchant') }}</span><br>
                                                @elseif($parcel->status == 're-request')
                                                    <span class="badge text-warning">{{ __('re-request') }}</span><br>
                                                @endif
                                                <span class="badge text-info">{{ __($parcel->parcel_type) }}</span><br><br>

                                                @if ($parcel->short_url != '')
                                                    <div class="d-flex">
                                                        <label>{{ __('tracking_url') . ': ' }}</label>
                                                        <div class="copy-to-clipboard">
                                                            <input readonly type="text" class="text-primary"
                                                                id="{{ $parcel->short_url }}" data-text="{{ __('copied') }}"
                                                                value="{{ __($parcel->short_url) }}">
                                                        </div>
                                                    </div> <br><br>
                                                @endif
                                                @if ($parcel->note != '' && $parcel->note != null)
                                                    <span>{{ __('note') }}: {{ __($parcel->note) }}</span>
                                                @endif
                                            </div>
                                            <div class="nk-tb-col ">
                                                <div class="tb-odr-btns d-md-inline">
                                                    <a href="{{ route('merchant.parcel.detail', $parcel->id) }}"
                                                        class="btn btn-sm btn-primary btn-tooltip"
                                                        data-original-title="{{ __('details') }}"><i
                                                            class="icon las la-eye"></i></a>
                                                </div>
                                                <div class="tb-odr-btns d-md-inline">
                                                    <a href="{{ route('merchant.parcel.print', $parcel->id) }}" target="_blank"
                                                        class="btn btn-sm btn-warning btn-tooltip"
                                                        data-original-title="{{ __('print') }}"><i
                                                            class="icon las la-print"></i></a>
                                                </div>
                                                @if ($parcel->status == 'pending' || $parcel->status == 'pickup-assigned' || $parcel->status == 're-schedule-pickup')
                                                    <div class="tb-odr-btns d-md-inline">
                                                        <a href="{{ route('merchant.parcel.edit', $parcel->id) }}"
                                                            class="btn btn-sm btn-info btn-tooltip"
                                                            data-original-title="{{ __('edit') }}"><i
                                                                class="icon la la-edit"></i></a>
                                                    </div>
                                                    <div class="tb-odr-btns d-md-inline">
                                                        <a href="javascript:void(0);"
                                                            class="delete-parcel btn btn-sm btn-danger btn-tooltip text-light"
                                                            data-original-title="{{ __('deleted') }}" id="delete-parcel"
                                                            data-toggle="modal" data-target="#parcel-delete"><i
                                                                class="icon  las la-trash"></i></a>
                                                    </div>
                                                @endif
                                                @if (
                                                        $parcel->status == 'received-by-pickup-man' ||
                                                        $parcel->status == 'received' ||
                                                        $parcel->status == 'transferred-to-branch' ||
                                                        $parcel->status == 'transferred-received-by-branch'
                                                    )
                                                    <div class="tb-odr-btns d-md-inline">
                                                        <a href="javascript:void(0);"
                                                            class="cancel-parcel btn btn-sm btn-danger btn-tooltip text-light"
                                                            data-original-title="{{ __('cancel') }}" id="cancel-parcel"
                                                            data-toggle="modal" data-target="#parcel-cancel"><i
                                                                class="icon las la-times"></i></a>
                                                    </div>
                                                @endif


                                                @if ($parcel->status == 'cancel')
                                                    <div class="tb-odr-btns d-md-inline">
                                                        <a href="javascript:void(0);"
                                                            class="parcel-re-request btn btn-sm btn-success btn-tooltip"
                                                            data-original-title="{{ __('re_request') }}" id="parcel-re-request"
                                                            data-toggle="modal" data-target="#re-request-parcel"><i
                                                                class="icon las la-arrow-left-circle"></i></a>
                                                    </div>
                                                @endif
                                                <div class="tb-odr-btns d-md-inline">
                                                    <a href="{{ route('merchant.parcel.duplicate', $parcel->id) }}"
                                                        class="btn btn-sm btn-secondary btn-tooltip"
                                                        data-original-title="{{ __('duplicate_parcel') }}"><i
                                                            class="icon las la-copy"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                            </div>
                            <div class="card-inner p-2">
                                <div class="-md g-3">
                                    <div class="g">
                                        {!! $parcels->appends(Request::except('page'))->links() !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" id="parcel-cancel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('cancel_parcel') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('merchant.parcel-cancel') }}" method="POST" class="form-validate is-alter">
                        @csrf
                        <input type="hidden" name="id" value="" id="cancel-parcel-id">
                        <div class="mb-3">
                            <label class="form-label" for="area">{{ __('cancel_note') }} <span
                                    class="text-danger">*</span></label>
                            <textarea name="cancel_note" class="form-control" required>{{ old('cancel_note') }}</textarea>
                        </div>
                        <div class="mb-3 text-right">
                            <button type="submit" class="btn btn-lg btn-primary">{{ __('submit') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" id="parcel-delete">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('delete_parcel') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('merchant.parcel-delete') }}" method="POST" class="form-validate is-alter">
                        @csrf
                        <input type="hidden" name="id" value="" id="delete-parcel-id">
                        <div class="mb-3">
                            <label class="form-label" for="area">{{ __('delete_note') }}</label>
                            <textarea name="cancel_note" class="form-control">{{ old('delete_note') }}</textarea>
                        </div>
                        <div class="mb-3 text-right">
                            <button type="submit" class="btn btn-lg btn-primary">{{ __('submit') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" id="re-request-parcel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('re_request') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('merchant.parcel.re-request') }}" method="POST" class="form-validate is-alter">
                        @csrf
                        <input type="hidden" name="id" value="" id="re-request-parcel-id">
                        <div class="mb-3">
                            <label class="form-label" for="area">{{ __('note') }} </label>
                            <textarea name="note" class="form-control">{{ old('note') }}</textarea>
                        </div>
                        <div class="mb-3 text-right">
                            <button type="submit" class="btn btn-lg btn-primary">{{ __('submit') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    @include('common.delete-ajax')
    @include('common.change-status-ajax')
    @include('merchant.parcel.change-parcel-status')
@endpush