@extends('backend.layouts.master')

@section('title')
    {{ __('total_summery') }}
@endsection

@section('mainContent')
    <div class="container-fluid">
        <div class="row gx-20">
            <div class="col-lg-12">
                <div class="header-top d-flex justify-content-between align-items-center mb-12">
                    <h3 class="section-title">{{ __('summary') }}</h3>
                </div>
                <form action="{{ route('admin.total_summery.report') }}" class="form-validate" method="GET">
                    <div class="bg-white redious-border p-20 p-sm-30">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card-inner">
                                    <div class="row g-gs">
                                        <div class="col-12 col-md-3 col-lg-3">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('start_date') }} <span
                                                        class="text-danger">*</span></label>
                                                    <input type="text" class="form-control date-picker" name="start_date"
                                                        autocomplete="off" required placeholder="{{ __('start_date') }}"
                                                        value="{{ request()->get('start_date') }}">
                                                @if ($errors->has('start_date'))
                                                    <div class="invalid-feedback help-block">
                                                        <p>{{ $errors->first('start_date') }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-3 col-lg-3">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('end_date') }} <span
                                                        class="text-danger">*</span></label>
                                                    <input type="text" class="form-control date-picker" name="end_date"
                                                        autocomplete="off" required placeholder="{{ __('end_date') }}"
                                                        value="{{ request()->get('end_date') }}">
                                                @if ($errors->has('end_date'))
                                                    <div class="invalid-feedback help-block">
                                                        <p>{{ $errors->first('end_date') }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-3 col-lg-3 mt-4">
                                            <div class="mb-3">
                                                <label class="form-label"></label>
                                                <button type="submit"  class="btn sg-btn-primary resubmit">{{ __('search') }}</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if (isset($data))
        <div class="container-fluid">
            <div class="row gx-20">
                <div class="col-lg-12">
                    <div class="header-top d-flex justify-content-between align-items-center mt-12">
                        <h3 class="section-title">{{ __('summery_between') }}
                            {{ $data['start_date'] != '' ? date('M d, Y', strtotime($data['start_date'])) : '' }}
                            - {{ $data['end_date'] != '' ? date('M d, Y', strtotime($data['end_date'])) : '' }}
                        </h3>
                    </div>

                    <div id="report-show">
                        <div class="row g-gs">
                            <div class="col-xxl-3 col-md-8 col-lg-6">
                                <div class="h-100 bg-white redious-border p-10 p-sm-20">
                                    <div class="card-inner">
                                        <div class="card-title-group mb-2">
                                            <div class="card-title">
                                                <h6 class="title">{{ __('parcel_info') }}</h6>
                                            </div>
                                        </div>
                                            <div class="card-inner p-0">
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th>{{ __('title') }}</th>
                                                            <th>{{ __('pcs') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>{{ __('total_parcels') }}
                                                            </td>
                                                            <td>{{ $data['total_parcels'] }}
                                                                {{ __('pcs') }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>{{ __('parcel_delivered') }}
                                                            </td>
                                                            <td> {{ $data['delivered'] }}
                                                                {{ __('pcs') }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>{{ __('parcel') . ' ' . __('partially-delivered') }}
                                                            </td>
                                                            <td>
                                                                {{ $data['partially-delivered'] }}
                                                                {{ __('pcs') }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>{{ __('parcel') . ' ' . __('pending-return') }}
                                                            </td>
                                                            <td>
                                                                {{ $data['pending-return'] }}
                                                                {{ __('pcs') }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>{{ __('parcel_returned_to_merchant') }}
                                                            </td>
                                                            <td>
                                                                {{ $data['returned_to_merchant'] }}
                                                                {{ __('pcs') }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>{{ __('parcel') . ' ' . __('deleted') }}</td>
                                                            <td> {{ $data['deleted'] }} {{ __('pcs') }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>{{ __('parcel_cancelled') }}</td>
                                                            <td>
                                                                {{ $data['cancelled'] }}
                                                                {{ __('pcs') }}
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xxl-3 col-md-8 col-lg-6">
                                <div class="h-100 bg-white redious-border p-10 p-sm-20">
                                    <div class="card-inner">
                                        <div class="card-title-group mb-2">
                                            <div class="card-title">
                                                <h6 class="title">{{ __('profit_info') }}</h6>
                                            </div>
                                        </div>
                                        <div class="">
                                            <div class="card-inner p-0">
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th>{{ __('title') }}</th>
                                                            <th>{{ __('amount') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>{{ __('total_charge_including_vat') }}</td>
                                                            <td>{{ format_price(abs($profits['total_charge_vat'])) }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>{{ __('total_delivery_charge') }}</td>
                                                            <td>{{ format_price($profits['total_delivery_charge']) }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>{{ __('total_vat') }}</td>
                                                            <td>{{ format_price($profits['total_vat']) }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>{{ __('fragile_liquid_charge') }}</td>
                                                            <td>{{ format_price($profits['total_fragile_charge']) }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>{{ __('packaging_charge') }}</td>
                                                            <td>{{ format_price($profits['total_packaging_charge']) }}
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <td class="text-bold"><h6>{{ __('total_profit') }}</h6>
                                                            </td>
                                                            <td class="text-bold"><h6>{{ format_price($profits['total_profit']) }}</h6>
                                                            </td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xxl-3 col-md-8 col-lg-6">
                                <div class="h-100 bg-white redious-border p-10 p-sm-20">
                                    <div class="card-inner">
                                        <div class="card-title-group mb-2">
                                            <div class="card-title">
                                                <h6 class="title">{{ __('payable_to_merchant') }}</h6>
                                            </div>
                                        </div>
                                        <div class="card-inner p-0">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('title') }}</th>
                                                        <th>{{ __('amount') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>{{ __('total_payable_to_merchant') }}({{ __('cod') }})</td>
                                                        <td>{{ format_price($profits['total_payable_to_merchant']) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>{{ __('total_paid_to_merchant') }}({{ __('with_pending') }})
                                                        </td>
                                                        <td>{{ format_price($profits['total_paid_to_merchant']) }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>{{ __('total_paid_by_merchant') }}</td>
                                                        <td>{{ format_price($profits['total_paid_by_merchant']) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>{{ __('total_charge_including_vat') }}</td>
                                                        <td>{{ format_price($profits['total_charge_vat']) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>{{ __('current_payable_to_merchant') }}</td>
                                                        <td>{{ format_price($profits['current_payable']) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>{{ __('pending_payments') }}</span></td>
                                                        <td>{{ format_price($profits['pending_payments']) }}</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-bold"><h6>{{ __('current_payable_with_pending') }}</h6>
                                                        </td>
                                                        <td class="text-bold"><h6>{{ format_price($profits['current_payable'] + $profits['pending_payments']) }}</h6>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xxl-3 col-md-8 col-lg-6">
                                <div class="h-100 bg-white redious-border p-10 p-sm-20">
                                    <div class="card-inner">
                                        <div class="card-title-group mb-2">
                                            <div class="card-title">
                                                <h6 class="title">{{ __('cash_collection_info') }}</h6>
                                            </div>
                                        </div>
                                        <div class="card-inner p-0">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('title') }}</th>
                                                        <th>{{ __('amount') }}</td>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>{{ __('total_cash_on_delivery') }}</td>
                                                        <td>{{ format_price($profits['total_cash_on_delivery']) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>{{ __('total_paid_by_delivery_man') }}</td>
                                                        <td>{{ format_price($profits['total_paid_by_delivery_man']) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>{{ __('total_paid_to_delivery_charge') }}</td>
                                                        <td>{{ format_price($profits['total_delivery_charge']) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-bold"><h6>{{ __('current_due_to_delivery_man') }}</h6></td>
                                                        <td class="text-bold"><h6>{{ format_price($profits['total_cash_on_delivery'] - $profits['total_paid_by_delivery_man'] - $profits['total_delivery_charge']) }}</h6>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xxl-12 col-md-12 col-lg-12 mt-4">
                                <div class="h-100 bg-white redious-border p-10 p-sm-20">
                                    <div class="card-inner">
                                        <div class="card-title-group mb-2">
                                            <div class="card-title">
                                                <h6 class="title">{{ __('bank_cash_info') }}</h6>
                                            </div>
                                        </div>
                                        <div class="card-inner p-0">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('title') }}</th>
                                                        <th>{{ __('amount') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>{{ __('total_paid_by_delivery_man') }}</td>
                                                        <td>{{ format_price($profits['total_paid_by_delivery_man']) }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>{{ __('total_paid_by_merchant') }}</td>
                                                        <td>{{ format_price($profits['total_paid_by_merchant']) }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>{{ __('pending_payments') }}</td>
                                                        <td>{{ format_price($profits['pending_payments']) }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>{{ __('total_bank_opening_balance') }}</td>
                                                        <td>{{ format_price($profits['total_bank_opening_balance']) }}
                                                        </td>
                                                    </tr>
                                                    <tr class="text-danger">
                                                        <td>{{ __('total_paid_to_merchant') }}({{ __('with_pending') }})
                                                        </td>
                                                        <td>{{ format_price($profits['total_paid_to_merchant']) }}
                                                        </td>
                                                    </tr>
                                                    <tr class="text-danger">
                                                        <td>{{ __('expense') }}</td>
                                                        <td>{{ format_price($profits['total_expense_from_account']) }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-bold"><h6>{{ __('current_cash_balance') }}</h6>
                                                        </td>
                                                        <td class="text-bold">
                                                            <h6>{{ format_price($profits['total_paid_by_delivery_man'] + $profits['total_paid_by_merchant'] + $profits['pending_payments'] + $profits['total_bank_opening_balance'] - $profits['total_paid_to_merchant'] - $profits['total_expense_from_account']) }}</h6>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
