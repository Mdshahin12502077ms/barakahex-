@extends('backend.layouts.master')

@section('title')
    {{ __('account') }} {{ __('lists') }}
@endsection
@section('mainContent')
    <div class="container-fluid">
        <div class="row gx-20">
            <div class="col-lg-12">
                <div class="header-top d-flex justify-content-between align-items-center mb-12">
                    <h3 class="section-title">{{ __('you_have_total') }} {{ !blank($accounts) ? $accounts->total() : '0' }}
                        {{ __('statement') }}</h3>
                    <div class="oftions-content-right">
                        @if (hasPermission('account_create'))
                            <a href="{{ route('admin.account') }}"
                                class="d-flex align-items-center btn sg-btn-primary gap-2"><i
                                    class="icon las la-arrow-left"></i><span>{{ __('back') }}</span></a>
                        @endif
                    </div>
                </div>
                <section class="oftions">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="bg-white redious-border p-20 p-sm-30 pt-sm-30">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="text-nowrap table-responsive">
                                                <div class="card-inner p-0">
                                                    <table class="table">
                                                        <thead>
                                                            <th>#</th>
                                                            <th>{{ __('date') }}</th>
                                                            <th>{{ __('source') }} / {{ __('details') }}</th>
                                                            <th class="text-end">{{ __('amount') }}</th>
                                                        </thead>
                                                        @php $balance = 0; @endphp
                                                        <tbody>
                                                            @foreach ($accounts as $key => $account)
                                                                <tr id="row_{{ $account->id }}">
                                                                    <td>
                                                                        <span>{{ $key + 1 }}</span>
                                                                    </td>
                                                                    <td>
                                                                        {{ $account->date != '' ? date('M d, Y', strtotime($account->date)) : '' }}<br>
                                                                        {{ $account->updated_at != '' ? date('M d, Y h:i a', strtotime($account->updated_at)) : '' }}
                                                                    </td>
                                                                    <td>
                                                                        {{ __($account->source) }}
                                                                        @if (@$account->companyAccount->source == 'delivery_charge_receive_from_merchant')
                                                                            {{ __('from') }}<br>
                                                                            <span>{{ __('merchant_name') }}:
                                                                                {{ $account->companyAccount->merchant->user->first_name . ' ' . $account->companyAccount->merchant->user->last_name . ' (' . $account->companyAccount->merchant->company . ')' }}</span><br>
                                                                            <span>{{ __('phone_number') }}:
                                                                                {{ $account->companyAccount->merchant->phone_number }}</span>
                                                                        @endif
                                                                        @if (@$account->companyAccount->source == 'cash_receive_from_delivery_man')
                                                                            <span>{{ __('delivery_man') }}:
                                                                                {{ $account->companyAccount->deliveryMan->user->first_name . ' ' . $account->companyAccount->deliveryMan->user->last_name }}</span><br>
                                                                            <span>{{ __('phone_number') }}:
                                                                                {{ $account->companyAccount->deliveryMan->phone_number }}</span>
                                                                        @endif

                                                                        @if ($account->source == 'withdraw' && @$account->companyAccount->withdraw)
                                                                            <a
                                                                                href="{{ route('admin.withdraw.invoice', $account->companyAccount->withdraw->id) }}">
                                                                                {{ __('id') . '#' . __($account->companyAccount->withdraw->withdraw_id) }}
                                                                            </a>
                                                                        @endif
                                                                        <br>
                                                                        {{ __($account->details) }} <br>
                                                                        {{ $account->deliveryMan }}
                                                                        @if ($account->fundTransfer != '')
                                                                            @if ($account->fundTransfer->toAccount->method == 'bank')
                                                                                <span>{{ __('name') }}:
                                                                                    {{ $account->fundTransfer->toAccount->account_holder_name }}</span><br>
                                                                                <span>{{ __('account_no') }}:
                                                                                    {{ $account->fundTransfer->toAccount->account_no }}</span><br>
                                                                                <span>{{ __('bank') }}:
                                                                                    {{ __($account->fundTransfer->toAccount->bank_name) }}</span><br>
                                                                                <span>{{ __('branch') }}:{{ $account->fundTransfer->toAccount->bank_branch }}</span><br>
                                                                            @elseif($account->fundTransfer->toAccount->method == 'cash')
                                                                                <span>{{ __('name') }}:
                                                                                    {{ $account->fundTransfer->toAccount->user->first_name . ' ' . $account->fundTransfer->toAccount->user->last_name }}</span><br>
                                                                                <span>{{ __('email') }}:
                                                                                    {{ $account->fundTransfer->toAccount->user->email }}</span><br>
                                                                            @else
                                                                                <span>{{ __('name') }}:
                                                                                    {{ $account->fundTransfer->toAccount->account_holder_name }}</span><br>
                                                                                <span>{{ __('number') }}:
                                                                                    {{ $account->fundTransfer->toAccount->number }}</span><br>
                                                                                <span>{{ __('account_type') }}:
                                                                                    {{ __($account->fundTransfer->toAccount->type) }}</span><br>
                                                                            @endif
                                                                        @endif
                                                                    </td>
                                                                    @if ($account->type == 'income')
                                                                        <td class="text-end">
                                                                            {{ format_price($account->amount) }}
                                                                        </td>
                                                                    @else
                                                                        <td class="text-end text-danger">
                                                                            {{ format_price($account->amount) }}
                                                                        </td>
                                                                    @endif
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <td colspan="3" class="text-end"><span
                                                                    ><strong>{{ __('remaining_balance') }}</strong></span>
                                                                </td>
                                                                <td class="text-end"><span
                                                                    ><strong>{{ format_price($grand_total) }}</strong></span>
                                                                </td>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                                <div class="d-flex  justify-content-end">
                                                    {!! $accounts->appends(Request::except('page'))->links() !!}
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
@endsection
