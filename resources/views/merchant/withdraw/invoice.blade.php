@extends('backend.layouts.master')
@section('title')
    {{ __('payment_invoice') }}
@endsection
@section('mainContent')
<section class="oftions">
    <div class="container-fluid">
      <div class="row">
        <div class="col-lg-12">
                <div class="header-top d-flex justify-content-between align-items-center">
                    <h3 class="section-title">{{ __('payment_invoice') }}</h3>
                    <div class="oftions-content-right mb-12">
                        <a href="{{ url()->previous() }}" class="d-flex align-items-center btn sg-btn-primary gap-2"><i
                                class="icon las la-arrow-left"></i><span>{{ __('back') }}</span></a>
                    </div>
                </div>
                <section class="oftions">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="bg-white redious-border p-20 p-sm-30 pt-sm-30">
                                <div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="assign-delivery border-bottom">
                                                <div class="row mb-2">
                                                    <div class="col-4">
                                                        <div class="right-content d-block">
                                                            <img src="{{ setting('dark_logo') && @is_file_exists(setting('dark_logo')['original_image']) ? get_media(setting('dark_logo')['original_image']) : getFileLink('80X80', []) }}" alt="logo"
                                                                class="img-fluid image">
                                                            <div class="parcel-details d-block mt-2">
                                                                {{ setting('address') }} <br>
                                                                {{ setting('phone') }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="invoice d-block">
                                                            <center>
                                                                <h4>{{ __('INVOICE') }}</h4>
                                                                <p>{{ __('id') }}# {{ $withdraw->withdraw_id }}</p>
                                                                {{ __('date') . ': ' . date('d.m.Y') }}
                                                            </center>
                                                        </div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="d-flex justify-content-between">
                                                            <div class="parcel-details d-block">
                                                                <h3 class="page-title">{{ __('merchant') }}:</h3>
                                                                <h6>{{ $withdraw->merchant->company }}</h6>
                                                                {{ $withdraw->merchant->address }}<br>
                                                                {{ $withdraw->merchant->phone_number }}
                                                            </div>
                                                            <div class="card-title">
                                                                <a href="{{ Sentinel::getUser()->user_type == 'merchant' ? route('merchant.invoice.print', $withdraw->id) : route('merchant.staff.invoice.print', $withdraw->id) }}"
                                                                    target="_blank" class="btn btn-icon btn-warning text-white"> <i
                                                                        class="icon las la-print"></i> </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-4">
                                        <div class="col-6">
                                            <table class="table table-bordered">
                                                <tbody>
                                                    <tr>
                                                        <th scope="row" colspan="2">
                                                            <span class="center">{{ __('payment_details') }} </span>
                                                        </th>
                                                    </tr>
                                                    @php
                                                        $total_price = 0;
                                                        $total_cod_charge = 0;
                                                        $total_vat = 0;
                                                        $total_charge = 0;
                                                        $total_fragile_charge = 0;
                                                        $total_packaging_charge = 0;
                                                        $total_delivery_charge_with_vat = 0;
                                                        $total_delivery_charge_without_vat = 0;
                                                        if (!blank($withdraw->parcels)):
                                                            foreach ($withdraw->parcels as $key => $parcel):
                                                                $total_price += $parcel->price;
                                                                $total_cod_charge += floor(($parcel->price / 100) * $parcel->cod_charge);
                                                                $total_charge += $parcel->charge;

                                                                $total_fragile_charge += $parcel->fragile_charge;
                                                                $total_packaging_charge += $parcel->packaging_charge;
                                                            endforeach;

                                                            $total_delivery_charge_with_vat = $withdraw->parcels->sum('total_delivery_charge');

                                                            $total_delivery_charge_without_vat = $total_cod_charge + $total_fragile_charge + $total_packaging_charge + $total_charge;
                                                            $total_vat = $total_delivery_charge_with_vat - $total_delivery_charge_without_vat;
                                                        endif;
                                                    @endphp
                                                    @if (!blank($withdraw->parcels))
                                                        <tr>
                                                            <th scope="row">{{ __('total_cash_collection') }}</th>
                                                            <td>{{ format_price($total_price) }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">{{ __('delivery_charge') }}</th>
                                                            <td> {{ format_price($total_charge) }}</td>
                                                        </tr>
                                                        @if ($total_cod_charge > 0)
                                                            <tr>
                                                                <th scope="row">{{ __('cod_charge') }}</th>
                                                                <td>{{ format_price(floor($total_cod_charge)) }}</td>
                                                            </tr>
                                                        @endif
                                                        @if ($total_fragile_charge > 0)
                                                            <tr>
                                                                <th scope="row">{{ __('fragile_liquid_charge') }}</th>
                                                                <td>{{ format_price($total_fragile_charge) }}</td>
                                                            </tr>
                                                        @endif
                                                        @if ($total_packaging_charge > 0)
                                                            <tr>
                                                                <th scope="row">{{ __('packaging_charge') }}</th>
                                                                <td>{{ format_price($total_packaging_charge) }}</td>
                                                            </tr>
                                                        @endif
                                                        @if ($total_vat > 0)
                                                            <tr>
                                                                <th scope="row">{{ __('vat') }}</th>
                                                                <td>{{ format_price(floor($total_vat)) }}</td>
                                                            </tr>
                                                        @endif
                                                        <tr>
                                                            <th scope="row">{{ __('total_charge') }}</th>
                                                            <td>{{ format_price($total_delivery_charge_with_vat) }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">{{ __('total_payable') }}</th>
                                                            <td>{{ format_price($withdraw->parcels->sum('payable')) }}
                                                            </td>
                                                        </tr>
                                                    @endif
                                                    @if (!blank($withdraw->merchantAccounts))
                                                        <tr>
                                                            <th scope="row">{{ __('previous_balance') }}
                                                                @php
                                                                    $income = $withdraw->merchantAccounts->where('type', 'income')->sum('amount');
                                                                    $expense = $withdraw->merchantAccounts->where('type', 'expense')->sum('amount');
                                                                @endphp
                                                            <td>{{ format_price($income - $expense) }}</td>
                                                        </tr>
                                                    @endif
                                                    @if (!blank($withdraw->paidReverseParcels))
                                                        <tr>
                                                            <th scope="row">
                                                                {{ __('paid_parcels_reverse_adjustment_amount') }}</th>
                                                            <td>{{ format_price($withdraw->paidReverseParcels->sum('amount')) }}
                                                            </td>
                                                        </tr>
                                                    @endif
                                                    <tr>
                                                        <th scope="row">{{ __('withdraw_amount') }}</th>
                                                        <td>{{ format_price($withdraw->amount) }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="col-6">
                                            <table class="table table-bordered">
                                                <tbody>
                                                    <tr>
                                                        <th scope="row" colspan="2"><span class="center">
                                                                {{ __('withdraw_details') }} </span></th>
                                                    </tr>
                                                    @php
                                                        $account_details = json_decode($withdraw->account_details);
                                                    @endphp
                                                    @if (@$withdraw->merchantPaymentAccount->type == \App\Enums\PaymentMethodType::BANK->value)
                                                        <tr>
                                                            <th scope="row">{{ __('payment_method') }}</th>
                                                            <td>{{ __('bank') }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">{{ __('bank_name') }}</th>
                                                            <td>{{ @$account_details[0] }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">{{ __('branch') }}</th>
                                                            <td>{{ @$account_details[1] }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">{{ __('account_holder') }}</th>
                                                            <td>{{ @$account_details[2] }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">{{ __('account_no') }}</th>
                                                            <td>{{ @$account_details[3] }}</td>
                                                        </tr>
                                                        @if (@$account_details[4] != '')
                                                            <tr>
                                                                <th scope="row">{{ __('routing_no') }}</th>
                                                                <td>{{ @$account_details[4] }}</td>
                                                            </tr>
                                                        @endif
                                                    @elseif (@$withdraw->merchantPaymentAccount->type == \App\Enums\PaymentMethodType::MFS->value)
                                                        <tr>
                                                            <th scope="row">{{ __('payment_method') }}</th>
                                                            <td>{{ __(@$account_details[0]) }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">{{ __('account_type') }}</th>
                                                            <td>{{ __(@$account_details[2]) }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">{{ __('account_number') }}</th>
                                                            <td>{{ @$account_details[1] }}</td>
                                                        </tr>
                                                    @else
                                                    <tr>
                                                        <th scope="row">{{ __('payment_method') }}</th>
                                                        <td>{{ $withdraw->payment_method_type }}</td>
                                                    </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                            @if ($withdraw->note != '' or $withdraw->note != null)
                                                <div class="note mt-2">
                                                    <textarea class="form-control">{{ __('note') }}: {{ $withdraw->note }}</textarea>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @if (!blank($withdraw->parcels))
                                    <div class="col-12 mt-5">
                                        <center>
                                            <h5 class="card-title mb-3">{{ __('cleared_payment_parcels') }}</h5>
                                            <p>{{ __('payment_invoice_notice') }}</p>
                                            <hr width="50%">
                                        </center>

                                        <table class="table table-bordered">
                                            <tbody>
                                                <tr>
                                                    <th scope="col">#</th>
                                                    <th scope="col">{{ __('parcel_id') }}</th>
                                                    <th scope="col">{{ __('invoice_no') }}</th>
                                                    <th scope="col">{{ __('customer_name') }}</th>
                                                    <th scope="col">{{ __('cod') }}</th>
                                                    <th scope="col">{{ __('delivery_charge') }}</th>
                                                    <th scope="col">{{ __('cod_charge') }}</th>
                                                    <th scope="col">{{ __('fragile_liquid_charge') }}</th>
                                                    <th scope="col">{{ __('packaging_charge') }}</th>
                                                    <th scope="col">{{ __('vat') }}</th>
                                                    <th scope="col">{{ __('total_charge') }}</th>
                                                    <th scope="col">{{ __('payable') }}</th>
                                                </tr>
                                                @foreach ($withdraw->parcels as $key => $parcel)
                                                    @php
                                                        $total_vat = ($parcel->total_delivery_charge / 100) * $parcel->vat;
                                                        $cod_charge = ($parcel->price / 100) * $parcel->cod_charge;
                                                    @endphp
                                                    <tr>
                                                        <th scope="row">{{ $key + 1 }}</th>
                                                        <td>{{ $parcel->parcel_no }}</td>
                                                        <td>{{ $parcel->customer_invoice_no }}</td>
                                                        <td>{{ $parcel->customer_name }}</td>
                                                        <td>{{ format_price($parcel->price) }}</td>
                                                        <td>{{ format_price($parcel->charge) }}</td>
                                                        <td>{{ format_price(floor($cod_charge)) }}</td>
                                                        <td>{{ format_price($parcel->fragile_charge) }}</td>
                                                        <td>{{ format_price($parcel->packaging_charge) }}</td>
                                                        <td>{{ format_price(floor($total_vat)) }}</td>
                                                        <td>{{ format_price($parcel->total_delivery_charge) }}</td>
                                                        <td>{{ format_price($parcel->payable) }}</td>
                                                    </tr>
                                                @endforeach
                                                <tr>
                                                    <th scope="row" colspan="11">{{ __('total_payable') }}</th>
                                                    <td>{{ format_price($withdraw->parcels->sum('payable')) }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                                @if (!blank($withdraw->paidReverseParcels))
                                    <div class="col-12 mt-5">
                                        <center>
                                            <h5 class="card-title mb-3">{{ __('delivery_reversed_parcels') }}</h5>
                                            <p>{{ __('paid_parcels_invoice_notice') }}</p>
                                            <hr width="50%">
                                        </center>

                                        <table class="table table-bordered">
                                            <tbody>
                                                <tr>
                                                    <th scope="col">#</th>
                                                    <th scope="col">{{ __('parcel_id') }}</th>
                                                    <th scope="col">{{ __('paid_amount') }}</th>
                                                </tr>
                                                @foreach ($withdraw->paidReverseParcels as $key => $withdrawParcel)
                                                    <tr>
                                                        <th scope="row">{{ $key + 1 }}</th>
                                                        <td>{{ $withdrawParcel->parcel->parcel_no }}</td>
                                                        <td>{{ format_price($withdrawParcel->amount) }}</td>
                                                    </tr>
                                                @endforeach
                                                <tr>
                                                    <th scope="row" colspan="2">{{ __('total_paid') }}</th>
                                                    <td>{{ format_price($withdraw->paidReverseParcels->sum('amount')) }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                                @if (!blank($withdraw->merchantAccounts))
                                    <div class="col-12 mt-5">
                                        <center>
                                            <h5 class="card-title mb-3">{{ __('previous_balance_calculations') }}</h5>
                                            <p>{{ __('previous_balance_calculations_notice') }}</p>
                                            <hr width="50%">
                                        </center>

                                        <table class="table table-bordered">
                                            <tbody>
                                                <tr>
                                                    <th scope="col">#</th>
                                                    <th scope="col">{{ __('source') }}</th>
                                                    <th scope="col">{{ __('details') }}</th>
                                                    <th scope="col">{{ __('date') }}</th>
                                                    <th scope="col">{{ __('credit') . '/' . __('debit') }}</th>
                                                    <th scope="col">{{ __('amount') }}</th>
                                                </tr>
                                                @foreach ($withdraw->merchantAccounts as $key => $merchant_account)
                                                    <tr>
                                                        <th scope="row">{{ $key + 1 }}</th>
                                                        <td>{{ __($merchant_account->source) }}</td>
                                                        <td>{{ @$merchant_account->parcel->parcel_no }}
                                                            <br>{{ __($merchant_account->details) }}
                                                        </td>
                                                        <td>{{ $merchant_account->created_at != '' ? date('M d, Y h:i a', strtotime($merchant_account->created_at)) : '' }}
                                                        </td>
                                                        <td>{{ $merchant_account->type == 'income' ? __('credit') : __('debit') }}
                                                        </td>
                                                        <td
                                                            {{ $merchant_account->type == 'income' ? '' : 'class=text-danger' }}>
                                                            {{ format_price($merchant_account->amount) }}</td>
                                                    </tr>
                                                @endforeach
                                                <tr>
                                                    <th scope="row" colspan="5">{{ __('previous_balance') }}</th>
                                                    <td>{{ format_price($income - $expense) }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                            </div>
                        </div>
                    </div>
                </section>
        </div>
      </div>
    </div>
</section>

@endsection
