@extends('backend.layouts.master')

@section('title')
    {{ __('account') }} {{ __('lists') }}
@endsection
@section('mainContent')
    <div class="container-fluid">
        <div class="row gx-20">
            <div class="col-lg-12">
                <div class="header-top d-flex justify-content-between align-items-center mb-12">
                    <h3 class="section-title">{{ __('account_info') }}</h3>
                    <div class="oftions-content-right">
                        @if (hasPermission('account_create'))
                            <a href="{{ route('admin.account') }}"
                                class="d-flex align-items-center btn sg-btn-primary gap-2"><i
                                    class="icon las la-arrow-left"></i><span>{{ __('back') }}</span></a>
                        @endif
                    </div>
                </div>
                    <div class="bg-white redious-border p-20 p-md-30">
                        <div class="card-inner-group">
                            <div class="card-inner p-0">
                                <table class="table">
                                    <tr>
                                        @if ($account->method == 'bank')
                                            <td><span
                                                   ><strong>{{ __('account_name') }}</strong></span></td>
                                            <td><span
                                                   >{{ $account->account_holder_name }}</span></td>
                                            <td><span
                                                   ><strong>{{ __('account_no') }}</strong></span></td>
                                            <td><span>{{ $account->account_no }}</span>
                                            </td>
                                        @elseif($account->method == 'cash')
                                            <td><span
                                                   ><strong>{{ __('name') }}</strong></span></td>
                                            <td><span
                                                   >{{ $account->user->first_name . ' ' . $account->user->last_name }}</span>
                                            </td>
                                            <td><span
                                                   ><strong>{{ __('email') }}</strong></span></td>
                                            <td><span
                                                   >{{ $account->user->email }}</span></td>
                                        @else
                                            <td><span
                                                   ><strong>{{ __('account_name') }}</strong></span></td>
                                            <td><span
                                                   >{{ $account->account_holder_name }}</span></td>
                                            <td><span
                                                   ><strong>{{ __('mobile_no') }}</strong></span></td>
                                            <td><span>{{ $account->number }}</span>
                                            </td>
                                        @endif

                                    </tr>
                                    <tr>
                                        <td><span><strong>{{ __('opening_balance') }}</strong></span></td>
                                        <td><span>{{ format_price($account->balance) }}</span></td>
                                        <td><span><strong>{{ __('income') }}</strong></span></td>
                                        <td><span>{{ format_price($total_income) }}</span></td>
                                    </tr>
                                    <tr>
                                        <td><span><strong>{{ __('expense') }}</strong></span></td>
                                        <td><span>{{ format_price(abs($total_expense)) }}</span></td>
                                        <td><span><strong>{{ __('fund_received') }}</strong></span></td>
                                        <td><span>{{ format_price($total_fund_received) }}</span></td>
                                    </tr>
                                    <tr>
                                        <td><span><strong>{{ __('fund_transfered') }}</strong></span></td>
                                        <td><span>{{ format_price(abs($total_fund_transfered)) }}</span>
                                        </td>
                                        <td><span><strong>{{ __('remaining_balance') }}</strong></span></td>
                                        <td><span>{{ format_price($remaining_balance) }}</span></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                <div class="row mt-3 account-income-expense">
                    <div class="col-xxl-6">
                        <div class="header-top d-flex justify-content-between align-items-center">
                            <div class="">
                                <div class="oftions-content-right">
                                    <h3 class="section-title">{{ __('you_have_total') }}
                                        {{ !blank($total_incomes) ? $total_incomes->total() : '0' }} {{ __('income_lists') }}
                                    </h3>
                                </div>
                            </div>
                        </div>
                            <div class="bg-white redious-border p-20 p-md-30">
                                <div class="card-inner-group">

                                    <div class="card-inner p-0">
                                        <table class="table">
                                            <tr>
                                                <td>{{ __('details') }}</td>
                                                <td>{{ __('date') }}</td>
                                                <td>{{ __('amount') }}</td>
                                            </tr>
                                            @foreach ($total_incomes as $total_income)
                                                <tr>
                                                    <td>
                                                        <span>
                                                            @if ($total_income->note != '')
                                                                {{ __($total_income->note) }}<br>
                                                            @endif
                                                            @if ($total_income->details != '')
                                                                {{ __($total_income->details) }}<br>
                                                            @endif
                                                            @if ($total_income->parcel != '')
                                                                <span>{{ __('parcel_id') }} :
                                                                    {{ $total_income->parcel->parcel_no }}</span><br>
                                                            @endif
                                                            @if ($total_income->deliveryMan != '')
                                                                <span>{{ __('from_delivery_nam') }} :
                                                                    {{ $total_income->deliveryMan->user->first_name . ' ' . $total_income->deliveryMan->user->last_name }}</span>
                                                            @endif
                                                        </span>
                                                    </td>
                                                    <td>
                                                        {{ $total_income->date != '' ? date('M d, Y', strtotime($total_income->date)) : '' }}
                                                    </td>
                                                    <td>
                                                        {{ format_price($total_income->amount) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </table>
                                    </div>
                                    <div class="card-inner p-2">
                                        <div class="-md g-3">
                                            <div class="g">
                                                {!! !blank($total_incomes) ? $total_incomes->links() : '' !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>
                    <div class="col-xxl-6">
                        <div class="header-top d-flex justify-content-between align-items-center">
                                <div class="oftions-content-right">
                                    <h3 class="section-title">{{ __('you_have_total') }}
                                        {{ !blank($total_expenses) ? $total_expenses->total() : '0' }}
                                        {{ __('expense_lists') }}</h3>
                                </div>
                        </div>
                            <div class="bg-white redious-border p-20 p-md-30">
                                <div class="card-inner-group">
                                    <div class="card-inner p-0">
                                        <table class="table">
                                            <tr>
                                                <td>{{ __('details') }}</td>
                                                <td>{{ __('date') }}</td>
                                                <td>{{ __('amount') }} ({{ __('date') }})</td>
                                            </tr>
                                            @foreach ($total_expenses as $total_expense)
                                                <tr>
                                                    <td>
                                                        <span>
                                                            @if ($total_expense->title != '')
                                                                {{ $total_expense->title }}<br>
                                                            @endif
                                                            @if ($total_expense->source != '')
                                                                {{ __($total_expense->source) }}<br>
                                                            @endif
                                                        </span>
                                                    </td>
                                                    <td>
                                                        {{ $total_expense->date != '' ? date('M d, Y', strtotime($total_expense->date)) : '' }}
                                                    </td>
                                                    <td>
                                                        {{ format_price(abs($total_expense->amount)) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </table>
                                    </div>
                                    <div class="card-inner p-2">
                                        <div class="-md g-3">
                                            <div class="g">
                                                {!! !blank($total_expenses) ? $total_expenses->links() : '' !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
