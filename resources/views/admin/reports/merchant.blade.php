@extends('backend.layouts.master')

@section('title')
    {{ __('merchant_total_summary') }}
@endsection

@section('mainContent')
    <div class="container-fluid">
        <div class="row gx-20">
            <div class="col-lg-12">
                <div class="header-top d-flex justify-content-between align-items-center mb-12">
                    <h3 class="section-title">{{ __('merchant_total_summary') }}</h3>
                </div>
                <form action="{{ route('admin.search.merchant.summary') }}" class="form-validate" method="GET">
                    <div class="bg-white redious-border p-20 p-sm-30">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card-inner">
                                    <div class="row g-gs">
                                        <div class="col-12 col-md-3 col-lg-3">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('start_date') }} <span
                                                        class="text-danger">*</span></label>
                                                <div class="form-control-wrap focused">
                                                    <input type="text" class="form-control date-picker" name="start_date"
                                                        autocomplete="off"  placeholder="{{ __('start_date') }}"
                                                        value="{{ request()->get('start_date') }}" required>
                                                </div>
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
                                                <div class="form-control-wrap focused">
                                                    <input type="text" class="form-control date-picker" name="end_date"
                                                        autocomplete="off" required placeholder="{{ __('end_date') }}"
                                                        value="{{ request()->get('end_date') }}">
                                                </div>
                                                @if ($errors->has('end_date'))
                                                    <div class="invalid-feedback help-block">
                                                        <p>{{ $errors->first('end_date') }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-3 col-lg-3">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('merchant') }}</label>
                                                <select id="merchant-live-search" name="merchant" required
                                                    class="form-control merchant-live-search">
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-3 col-lg-3 mt-4">
                                            <div class="mb-3 d-flex gap-2">
                                                <label class="form-label"></label>
                                                <button type="submit" class="btn sg-btn-primary resubmit">{{ __('search') }}</button>
                                                @if (isset($data))
                                                    @if(hasPermission('download_closing_report'))
                                                        <li>
                                                            <a class="d-flex align-items-center btn sg-btn-primary" href="{{ route('admin.merchant.closing.report', $merchant->id) }}"> {{__('download')}}</a>
                                                        </li>
                                                    @endif
                                                @endif
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
                    <div class="header-top d-flex justify-content-between align-items-center my-2">
                        <h3 class="section-title">{{ __('summery_between') }}
                            {{ $date['start_date'] != '' ? date('M d, Y', strtotime($date['start_date'])) : '' }}
                            - {{ $date['end_date'] != '' ? date('M d, Y', strtotime($date['end_date'])) : '' }}
                        </h3>
                    </div>
                    <div class="row g-gs">
                        <div class="col-xxl-6 col-md-6 col-lg-6">
                            <div class="bg-white redious-border p-20 p-sm-30 h-100">
                                <div class="card-inner">
                                    <div class="card-title-group mb-2">
                                        <div class="card-title">
                                            <h6 class="title">{{ __('parcel_statistics') }}</h6>
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
                                                @foreach ($data as $event => $count)
                                                    <tr>
                                                        <td>{{ __($event) }}</td>
                                                        <td> {{ $count }} {{ __('pcs') }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xxl-6 col-md-6 col-lg-6">
                            <div class="bg-white redious-border p-20 p-sm-30 h-100">
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
                                                    <td>{{ __('total_paid_to_merchant') }}({{ __('with_pending') }})</td>
                                                    <td>{{ format_price($profits['total_paid_to_merchant']) }}</td>
                                                </tr>
                                                <tr>
                                                    <td>{{ __('total_paid_by_merchant') }}</td>
                                                    <td>{{ format_price($profits['total_paid_by_merchant']) }}</td>
                                                </tr>
                                                <tr>
                                                    <td>{{ __('total_charge_including_vat') }}</td>
                                                    <td>
                                                        {{ format_price($profits['total_charge_vat']) }}</td>
                                                </tr>
                                                <tr>
                                                    <td><h6>{{ __('current_payable_to_merchant') }}</h6>
                                                    </td>
                                                    <td><h6>{{ format_price($profits['current_payable']) }}</h6>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>{{ __('pending_payments') }}</td>
                                                    <td>{{ format_price($profits['pending_payments']) }}</td>
                                                </tr>
                                                <tr>
                                                    <td><h6>{{ __('current_payable_with_pending') }}</h6>
                                                    </td>
                                                    <td>
                                                        <h6>{{ format_price($profits['current_payable'] + $profits['pending_payments']) }}</h6>
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
    @endif
@endsection
@include('live_search.merchants')
