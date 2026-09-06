@extends('backend.layouts.master')
@section('title')
    {{ __('transaction_history') }}
@endsection
@section('mainContent')
    <div class="container-fluid">
        <div class="row gx-20">
            <div class="col-lg-12">
                <div class="header-top d-flex justify-content-between align-items-center mb-12">
                    <h3 class="section-title">{{ __('transaction_history') }}</h3>
                </div>
                <form action="{{ route('admin.search.transaction') }}" class="form-validate" method="GET" enctype="multipart/form-data">
                    <div class="bg-white redious-border p-20 p-sm-30">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card-inner">
                                    <div class="row g-gs">
                                        <div class="col-12 col-md-6 col-lg-3">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('start_date') }} <span
                                                        class="text-danger">*</span></label>
                                                    <input type="text" class="form-control date-picker" required
                                                        name="start_date" autocomplete="off"
                                                        placeholder="{{ __('start_date') }}"
                                                        value="{{ request()->get('start_date') }}">
                                                @if ($errors->has('start_date'))
                                                    <div class="invalid-feedback help-block">
                                                        <p>{{ $errors->first('start_date') }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6 col-lg-3">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('end_date') }} <span
                                                        class="text-danger">*</span></label>
                                                    <input type="text" class="form-control date-picker" required
                                                        name="end_date" autocomplete="off"
                                                        placeholder="{{ __('end_date') }}"
                                                        value="{{ request()->get('end_date') }}">
                                                @if ($errors->has('end_date'))
                                                    <div class="invalid-feedback help-block">
                                                        <p>{{ $errors->first('end_date') }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6 col-lg-3">
                                            <div class="mb-3">
                                                <label class="form-label">
                                                    {{ __('report_type') }}
                                                    <span class="text-danger">*</span>
                                                </label>
                                                    <select class="without_search form-select form-control" name="report_type" required>
                                                        <option value="">{{ __('select_report_type') }}</option>
                                                        <option value="statement"
                                                            {{ request()->get('report_type') == 'statement' ? 'selected' : '' }}>
                                                            {{ __('statement') }}</option>
                                                        <option value="summery"
                                                            {{ request()->get('report_type') == 'summery' ? 'selected' : '' }}>
                                                            {{ __('summary') }}</option>
                                                    </select>
                                                @if ($errors->has('type'))
                                                    <div class="invalid-feedback help-block">
                                                        <p>{{ $errors->first('type') }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6 col-lg-3">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('select_purpose') }} <span
                                                        class="text-danger">*</span></label>
                                                    <select class="without_search form-select form-control search-type" required
                                                        name="purpose">
                                                        <option value="">{{ __('select_type') }}</option>
                                                        <option value="total-charge-with-vat"
                                                            {{ request()->get('purpose') == 'total-charge-with-vat' ? 'selected' : '' }}>
                                                            {{ __('total_charge_with_vat') }}</option>
                                                        <option value="charge"
                                                            {{ request()->get('purpose') == 'charge' ? 'selected' : '' }}>
                                                            {{ __('charge') }}</option>
                                                        <option value="vat"
                                                            {{ request()->get('purpose') == 'vat' ? 'selected' : '' }}>
                                                            {{ __('vat') }}</option>
                                                        <option value="delivery_man"
                                                            {{ request()->get('purpose') == 'delivery_man' ? 'selected' : '' }}>
                                                            {{ __('delivery_man') }}</option>
                                                        <option value="merchant"
                                                            {{ request()->get('purpose') == 'merchant' ? 'selected' : '' }}>
                                                            {{ __('merchant') }}</option>
                                                    </select>
                                                @if ($errors->has('type'))
                                                    <div class="invalid-feedback help-block">
                                                        <p>{{ $errors->first('type') }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6 col-lg-3 {{ request()->get('purpose') == 'merchant' ? 'd-block':'d-none'  }}" id="merchant-area">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('merchant') }}</label>
                                                    <select id="merchant-live-search" name="merchant" class="form-control merchant-live-search">
                                                    </select>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6 col-lg-3 {{ request()->get('purpose') == 'delivery_man' ? 'd-block':'d-none'  }}" id="delivery-area">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('delivery_man') }}</label>
                                                <select id="delivery-man-live-search" data-url="{{ route('get-delivery-man-live') }}" name="delivery_man" class="form-control delivery-man-live-search">
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-3 ">
                                        <div class="col-sm-12 d-flex justify-content-end">
                                            <div class="mb-3">
                                                <button type="submit" class="btn sg-btn-primary resubmit">{{ __('search') }}</button>
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

    @if (isset($charges))
        <div class="container-fluid">
            <div class="row gx-20">
                <div class="col-lg-12">
                    <div class="header-top mt-2">
                        <div>
                            <h3 class="section-title">{{ __('lists') }}</h3>
                        </div>
                        <div>
                            <p>{{ __('you_have_total') }} {{ $charges->total() }} {{ __('charges') }}.</p>
                        </div>
                    </div>
                    <section class="oftions">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="bg-white redious-border p-20 p-sm-30">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="default-list-table table-responsive">
                                                    <table class="table">
                                                        <thead>
                                                            <tr>
                                                                <th>#</th>
                                                                <th>{{ __('date') }}</th>
                                                                <th>{{ __('parcel_no') }}</th>
                                                                <th class="text-end">{{ __('amount') }}</th>
                                                            </tr>
                                                        </thead>
                                                        @php $balance = 0; @endphp
                                                        <tbody>
                                                            @foreach ($charges as $key => $charge)
                                                                <tr id="row_{{ $charge->id }}">
                                                                    <td>
                                                                        <span>{{ $key + 1 }}</span>
                                                                    </td>
                                                                    <td>
                                                                        {{ $charge->date != '' ? date('M d, Y', strtotime($charge->date)) : '' }}
                                                                    </td>
                                                                    <td>
                                                                        <a href="{{ route('admin.parcel.detail', $charge->parcel->id) }}">
                                                                            <div>
                                                                                <div>{{ __('id') }}:#{{ $charge->parcel->parcel_no }}
                                                                                </div>
                                                                                <div>{{ __('invno') }}:{{ $charge->parcel->customer_invoice_no }}
                                                                                </div>
                                                                            </div>
                                                                        </a>
                                                                    </td>
                                                                    <td class="text-end">
                                                                        @if ($charge->type == 'income')
                                                                            <span>{{ format_price($charge->amount) }}</span>
                                                                        @else
                                                                            <span class="text-danger">{{ format_price($charge->amount) }}</span>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                            <tr>
                                                                <td colspan="3" class=""><strong><h6 class="text-bold">{{ __('remaining_balance') }}</h6></strong>
                                                                </td>
                                                                <td class="text-end"><strong><h6 class="text-bold text-right">{{ format_price($data['grand_total']) }}</h6></strong>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="card-inner p-2">
                                                <div class="-md g-3">
                                                    <div class="g">
                                                        {!! $charges->appends(Request::except('page'))->links() !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    @endif
    @if (isset($summery_charges))
        <div class="container-fluid">
            <div class="row gx-20">
                <div class="col-lg-12">
                    <div class="header-top mt-2">
                        <h3 class="section-title">{{ __('lists') }}</h3>
                        <div>
                            <p>{{ __('you_have_total') }} {{ $summery_charges->total() }}
                                {{ __('charges') }}.</p>
                        </div>
                    </div>

                    <section class="oftions">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="bg-white redious-border p-20 p-sm-30">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="default-list-table table-responsive">
                                                    <table class="table">
                                                        <thead>
                                                            <tr>
                                                                <th>#</th>
                                                                <th>{{ __('date') }}</th>
                                                                <th>{{ __('credit') }}</th>
                                                                <th class="text-end">{{ __('debit') }}</th>
                                                            </tr>
                                                        </thead>
                                                        @php
                                                            $balance = 0;
                                                            $i = 0;
                                                        @endphp
                                                        <tbody>
                                                            @foreach ($main_datas as $key => $charge)
                                                                <tr>
                                                                    <td>
                                                                        <span>{{ ++$i }}</span>
                                                                    </td>
                                                                    <td>
                                                                        {{ $key != '' ? date('M d, Y', strtotime($key)) : '' }}
                                                                    </td>
                                                                    <td>
                                                                        @if (data_get($charge, 'data1.type') == 'expense')
                                                                            {{ format_price(data_get($charge, 'data1.amount')) }}
                                                                            @php $balance += data_get($charge, 'data1.amount'); @endphp
                                                                        @elseif(data_get($charge, 'data2.type') == 'expense')
                                                                            {{ format_price(data_get($charge, 'data2.amount')) }}
                                                                            @php $balance += data_get($charge, 'data2.amount'); @endphp
                                                                        @else
                                                                            <span>0.00</span>
                                                                        @endif
                                                                    </td>
                                                                    <td class="text-danger text-end">
                                                                        @if (data_get($charge, 'data1.type') == 'income')
                                                                            {{ format_price(data_get($charge, 'data1.amount')) }}
                                                                            @php $balance -= data_get($charge, 'data1.amount'); @endphp
                                                                        @elseif(data_get($charge, 'data2.type') == 'income')
                                                                            {{ format_price(data_get($charge, 'data2.amount')) }}
                                                                            @php $balance -= data_get($charge, 'data2.amount'); @endphp
                                                                        @else
                                                                            <span>0.00</span>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                            <tr>
                                                                <td colspan="2">{{ __('total') }}</td>
                                                                <td>{{ format_price($data['income']) }}
                                                                </td>
                                                                <td class="text-danger text-end">{{ format_price($data['expense']) }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="3"><strong><h6 class="text-bold">{{ __('remaining_balance') }}</h6>
                                                                    </strong></td>
                                                                <td class="text-end ml-2"><strong><h6 class="text-bold text-right">{{ format_price($data['grand_total']) }}</h6></strong></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="card-inner p-2">
                                                <div class="-md g-3">
                                                    <div class="g">
                                                        {!! $summery_charges->appends(Request::except('page'))->links() !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    @endif

    @if (isset($vats))
        <div class="container-fluid">
            <div class="row gx-20">
                <div class="col-lg-12">
                    <div class="header-top mt-2">
                        <h3 class="section-title">{{ __('lists') }}</h3>
                        <div>
                            <p>{{ __('you_have_total') }} {{ $vats->total() }} {{ __('vats') }}.</p>
                        </div>
                    </div>
                    <section class="oftions">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="bg-white redious-border p-20 p-sm-30">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="default-list-table table-responsive">
                                                    <table class="table">
                                                        <thead>
                                                            <tr>
                                                                <th>#</th>
                                                                <th>{{ __('date') }}</th>
                                                                <th>{{ __('details') }}</th>
                                                                <th class="text-end">{{ __('amount') }}</th>
                                                            </tr>
                                                        </thead>
                                                        @php $balance = 0; @endphp
                                                        <tbody>
                                                            @foreach ($vats as $key => $vat)
                                                                <tr id="row_{{ $vat->id }}">
                                                                    <td>
                                                                        <span>{{ $key + 1 }}</span>
                                                                    </td>
                                                                    <td>
                                                                        {{ $vat->date != '' ? date('M d, Y', strtotime($vat->date)) : '' }}
                                                                    </td>
                                                                    <td>
                                                                        <span>{{ __($vat->details) }}</span>
                                                                        <a href="{{ route('admin.parcel.detail', $vat->parcel->id) }}">
                                                                            <div>
                                                                                <div>
                                                                                    <div>{{ __('parcel_id') }}:#{{ $vat->parcel->parcel_no }}
                                                                                    </div>
                                                                                </div>
                                                                                <div>
                                                                                    <div>{{ __('invno') }}:{{ $vat->parcel->customer_invoice_no }}
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </a>
                                                                    </td>
                                                                    <td class="text-end">
                                                                        @if ($vat->type == 'income')
                                                                            {{ format_price($vat->amount) }}
                                                                        @else
                                                                            {{ format_price($vat->amount) }}f
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                            <tr>
                                                                <td colspan="3"><strong><h6 class="text-bold">{{ __('remaining_balance') }}</h6></strong></td>
                                                                <td class="text-end"><strong><h6 class="text-bold text-right">{{ format_price($data['grand_total']) }}</h6></strong></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="card-inner p-2">
                                                <div class="-md g-3">
                                                    <div class="g">
                                                        {!! $vats->appends(Request::except('page'))->links() !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    @endif

    @if (isset($summery_vats))
        <div class="container-fluid">
            <div class="row gx-20">
                <div class="col-lg-12">
                    <div class="header-top mt-2">
                        <h3 class="section-title">{{ __('lists') }}</h3>
                        <div>
                            <p>{{ __('you_have_total') }} {{ $summery_vats->total() }} {{ __('charges') }}.
                            </p>
                        </div>
                    </div>
                    <section class="oftions">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="bg-white redious-border p-20 p-sm-30">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="default-list-table table-responsive">
                                                    <table class="table">
                                                        <thead>
                                                            <tr>
                                                                <th>#</th>
                                                                <th>{{ __('date') }}</th>
                                                                <th>{{ __('credit') }}</th>
                                                                <th class="text-end">{{ __('debit') }}</th>
                                                            </tr>
                                                        </thead>
                                                        @php
                                                            $balance = 0;
                                                            $i = 0;
                                                        @endphp
                                                        <tbody>
                                                            @foreach ($main_datas as $key => $vat)
                                                                <tr>
                                                                    <td>
                                                                        <span>{{ ++$i }}</span>
                                                                    </td>
                                                                    <td>
                                                                        {{ $key != '' ? date('M d, Y', strtotime($key)) : '' }}
                                                                    </td>
                                                                    <td>
                                                                        @if (data_get($vat, 'data1.type') == 'income')
                                                                            {{ format_price(data_get($vat, 'data1.amount')) }}
                                                                            @php $balance += data_get($vat, 'data1.amount'); @endphp
                                                                        @elseif(data_get($vat, 'data2.type') == 'income')
                                                                            {{ format_price(data_get($vat, 'data2.amount')) }}
                                                                            @php $balance += data_get($vat, 'data2.amount'); @endphp
                                                                        @else
                                                                            <span>0.00</span>
                                                                        @endif
                                                                    </td>
                                                                    <td class="text-danger text-end">
                                                                        @if (data_get($vat, 'data1.type') == 'expense')
                                                                            {{ format_price(data_get($vat, 'data1.amount')) }}
                                                                            @php $balance -= data_get($vat, 'data1.amount'); @endphp
                                                                        @elseif(data_get($vat, 'data2.type') == 'expense')
                                                                            {{ format_price(data_get($vat, 'data2.amount')) }}
                                                                            @php $balance -= data_get($vat, 'data2.amount'); @endphp
                                                                        @else
                                                                            <span>0.00</span>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                            <tr>
                                                                <td colspan="2">{{ __('total') }}</td>
                                                                <td>{{ format_price($data['income']) }}</td>
                                                                <td class="text-danger text-end">{{ format_price($data['expense']) }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="3"><strong><h6 class="text-bold">{{ __('remaining_balance') }}</h6>
                                                                    </strong></td>
                                                                <td class="text-end ml-2"><strong><h6 class="text-bold text-right">{{ format_price($data['grand_total']) }}</h6>
                                                                    </strong></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="card-inner p-2">
                                                <div class="-md g-3">
                                                    <div class="g">
                                                        {!! $summery_vats->appends(Request::except('page'))->links() !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    @endif

    @if (isset($mer_deli_transactions))
        <div class="container-fluid">
            <div class="row gx-20">
                <div class="col-lg-12">
                    <div class="header-top mt-2">
                        <h3 class="section-title">{{ __('lists') }}</h3>
                            <div>
                                <p>{{ __('you_have_total') }} {{ $mer_deli_transactions->total() }}
                                        {{ __('statements') }}.</p>
                            </div>
                    </div>

                    <section class="oftions">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="bg-white redious-border p-20 p-sm-30">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="default-list-table table-responsive">
                                                    <table class="table">
                                                        <thead>
                                                            <tr>
                                                                <th>#</th>
                                                                <th>{{ __('date') }}</th>
                                                                <th>{{ __('details') }}/{{ __('parcel_no') }}</th>
                                                                <th class="text-end">{{ __('amount') }}</th>
                                                            </tr>
                                                        </thead>
                                                        @php $balance = 0; @endphp
                                                        @foreach ($mer_deli_transactions as $key => $transaction)
                                                            <tr id="row_{{ $transaction->id }}">
                                                                <td>
                                                                    <span>{{ $key + 1 }}</span>
                                                                </td>
                                                                <td>
                                                                    {{ $transaction->date != '' ? date('M d, Y', strtotime($transaction->date)) : '' }}
                                                                </td>
                                                                <td>
                                                                    <span>{{ __($transaction->details) }}</span>
                                                                    @if (!blank($transaction->parcel))
                                                                        <a
                                                                            href="{{ route('admin.parcel.detail', @$transaction->parcel->id) }}">
                                                                            <div>
                                                                                <div>
                                                                                    <div>{{ __('id') }}:#{{ @$transaction->parcel->parcel_no }}
                                                                                    </div>
                                                                                </div>
                                                                                <div>
                                                                                    <div>{{ __('invno') }}:{{ @$transaction->parcel->customer_invoice_no }}
                                                                                    </div>
                                                                                </div>

                                                                            </div>
                                                                        </a>
                                                                    @endif
                                                                </td>
                                                                @if ($transaction->type == 'income')
                                                                    <td class="text-end">
                                                                        {{ format_price($transaction->amount) }}
                                                                    </td>
                                                                @else
                                                                    <td class="text-danger text-end">
                                                                        {{ format_price($transaction->amount) }}
                                                                    </td>
                                                                @endif

                                                            </tr>
                                                        @endforeach
                                                        <tr>
                                                            <td colspan="3"><strong><h6 class="text-bold">{{ __('remaining_balance') }}</h6></strong>
                                                            </td>
                                                            <td class="text-end"><strong><h6 class="text-bold text-right">{{ format_price($data['grand_total']) }}</h6></strong>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="card-inner p-2">
                                                <div class="-md g-3">
                                                    <div class="g">
                                                        {!! $mer_deli_transactions->appends(Request::except('page'))->links() !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    @endif

    @if (isset($summery_mer_deli_transactions))
        <div class="container-fluid">
            <div class="row gx-20">
                <div class="col-lg-12">
                    <div class="header-top mt-2">
                        <h3 class="section-title">{{ __('lists') }}</h3>
                        <div>
                            <p>{{ __('you_have_total') }} {{ $summery_mer_deli_transactions->total() }} {{ __('statements') }}.</p>
                        </div>
                    </div>
                    <section class="oftions">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="bg-white redious-border p-20 p-sm-30">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="default-list-table table-responsive">
                                                    <table class="table">
                                                        <tr>
                                                            <th>#</th>
                                                            <th>{{ __('date') }}</th>
                                                            <th>{{ __('credit') }}</th>
                                                            <th class="text-end">{{ __('debit') }}</th>
                                                        </tr>
                                                        @php
                                                            $balance = 0;
                                                            $i = 1;
                                                        @endphp
                                                        @foreach ($main_datas as $key => $transaction)
                                                            <tr>
                                                                <td>
                                                                    <span>{{ $i++ }}</span>
                                                                </td>
                                                                <td>
                                                                    {{ $key != '' ? date('M d, Y', strtotime($key)) : '' }}
                                                                </td>
                                                                <td>
                                                                    @if (data_get($transaction, 'data1.type') == 'income')
                                                                        {{ format_price(data_get($transaction, 'data1.amount')) }}
                                                                        @php $balance += data_get($transaction, 'data1.amount'); @endphp
                                                                    @elseif(data_get($transaction, 'data2.type') == 'income')
                                                                        {{ format_price(data_get($transaction, 'data2.amount')) }}
                                                                        @php $balance += data_get($transaction, 'data2.amount'); @endphp
                                                                    @else
                                                                        <span>0.00</span>
                                                                    @endif
                                                                </td>
                                                                <td class="text-danger text-end">
                                                                    @if (data_get($transaction, 'data1.type') == 'expense')
                                                                        {{ format_price(data_get($transaction, 'data1.amount')) }}
                                                                        @php $balance -= data_get($transaction, 'data1.amount'); @endphp
                                                                    @elseif(data_get($transaction, 'data2.type') == 'expense')
                                                                        {{ format_price(data_get($transaction, 'data2.amount')) }}
                                                                        @php $balance -= data_get($transaction, 'data2.amount'); @endphp
                                                                    @else
                                                                        <span>0.00</span>
                                                                    @endif
                                                                </td>
                                                        @endforeach
                                                        <tr>
                                                            <td colspan="2">{{ __('total') }}</td>
                                                            <td>{{ format_price($data['income']) }}
                                                            </td>
                                                            <td class="text-danger text-end">{{ format_price($data['expense']) }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="3">
                                                                <strong><h6 class="text-bold">{{ __('remaining_balance') }}</h6></strong>
                                                            </td>
                                                            <td class="text-end ml-2">
                                                                <strong><h6 class="text-bold text-right">{{ format_price($data['grand_total']) }}</h6></strong>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-inner p-2">
                                            <div class="-md g-3">
                                                <div class="g">
                                                    {!! $summery_mer_deli_transactions->appends(Request::except('page'))->links() !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    @endif
@endsection
@push('script')
    <script>
        $(document).ready(function() {
            $('.search-type').on('change', function() {
                if ($(this).val() == 'merchant') {
                    $('#merchant-area').removeClass('d-none');
                    $('#delivery-area').addClass('d-none');
                } else if ($(this).val() == 'delivery_man') {
                    $('#delivery-area').removeClass('d-none');
                    $('#merchant-area').addClass('d-none');
                } else {
                    $('#merchant-area').addClass('d-none');
                    $('#delivery-area').addClass('d-none');
                }
            })
        })
        $('#delivery-man-live-search').select2(
            getLiveSearch(
                $('#delivery-man-live-search').data('url'),
                'Select delivery hero'
            )
        )
    </script>
@endpush
@include('live_search.merchants')
@include('live_search.delivery-man')
