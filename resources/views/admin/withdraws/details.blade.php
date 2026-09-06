@extends('backend.layouts.master')
@section('payments', 'active')
@section('withdraws', 'active')
@section('title')
    {{ __('payment') . ' ' . __('details') }}
@endsection
@section('mainContent')
    <div class="container-fluid">
        <div class="row gx-20">
            <div class="col-lg-12">
                <div class="header-top d-flex justify-content-between align-items-center mb-12">
                    <h3 class="section-title">{{ __('payment') . ' ' . __('details') }}</h3>
                    <div class="oftions-content-right">
                        <a href="{{ url()->previous() }}" class="d-flex align-items-center btn sg-btn-primary gap-2">
                            <i class="las la-arrow-left"></i>
                            <span>{{ __('back') }}</span>
                        </a>
                    </div>
                </div>
                <div class="bg-white redious-border p-20 p-sm-30 pt-sm-30">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card-inner card-inner-lg">
                                    <div class="nk-data data-list">
                                        <div>
                                            <h6 >{{ __('details') }}</h6>
                                        </div>
                                        <div class="data-item" data-bs-toggle="modal" data-bs-target="#profile-edit">
                                            <div class="data-col">
                                                <span class="data-label">{{ __('merchant') }}</span>
                                                <a
                                                    href="{{ route('detail.merchant.personal.info', $withdraw->merchant->id) }}">
                                                    <span
                                                        class="data-value text-success">{{ $withdraw->merchant->company }}</span>
                                                </a>
                                            </div>
                                        </div><!-- data-item -->

                                        <div class="data-item">
                                            <div class="data-col">
                                                <span class="data-label">{{ __('payment_to') }}</span>
                                                <span class="data-value">{{ $withdraw->account_details }}</span>
                                            </div>
                                        </div><!-- data-item -->

                                        <div class="data-item">
                                            <div class="data-col">
                                                <span class="data-label">{{ __('amount') }}</span>
                                                <span class="data-value">{{ $withdraw->amount }}</span>
                                            </div>
                                        </div><!-- data-item -->
                                        <div class="data-item">
                                            <div class="data-col">
                                                <span class="data-label">{{ __('status') }}</span>
                                                <span class="data-value">
                                                    @if ($withdraw->status == 'processed')
                                                        <span class="tb-status text-success">{{ __('processed') }}</span>
                                                    @elseif($withdraw->status == 'rejected')
                                                        <span class="tb-status text-danger">{{ __('rejected') }}</span>
                                                        <br>
                                                        <span
                                                            class="text-warning">{{ __('reject_reason') . ': ' }}{{ $withdraw->companyAccountReason->reject_reason != '' ? __($withdraw->companyAccountReason->reject_reason) : '' }}</span><br>
                                                        <span
                                                            class="text">{{ __('at') . ': ' }}{{ $withdraw->updated_at != '' ? date('M d, Y h:i a', strtotime($withdraw->updated_at)) : '' }}</span>
                                                    @elseif($withdraw->status == 'cancelled')
                                                        <span class="text-danger">{{ __('cancelled') . ': ' }}</span><br>
                                                        <span
                                                            class="text">{{ __('at') . ': ' }}{{ $withdraw->updated_at != '' ? date('M d, Y h:i a', strtotime($withdraw->updated_at)) : '' }}</span>
                                                    @elseif($withdraw->status == 'pending')
                                                        <span class="tb-status text-warning">{{ __('pending') }}</span>
                                                        <br>
                                                    @endif
                                                </span>
                                            </div>
                                        </div><!-- data-item -->
                                        @if ($withdraw->status == 'processed')
                                            <div class="data-item">
                                                <div class="data-col">
                                                    <span class="data-label">{{ __('payment_from') }}</span>
                                                    @if ($withdraw->companyAccount->cash_received_by != '')
                                                        <span>{{ __('name') }}:
                                                            {{ $withdraw->companyAccount->cashCollected->first_name . ' ' . $withdraw->companyAccount->cashCollected->last_name }}</span><br>
                                                    @elseif($withdraw->companyAccount->account_id != '')
                                                        @if (@$withdraw->companyAccount->account->method == 'bank')
                                                            {{ __(@$withdraw->companyAccount->account->method) }},
                                                            {{ __('account_holder') }}:
                                                            {{ $withdraw->companyAccount->account->account_holder_name . ' ' }},
                                                            {{ ' ' . __('account_no') }}:
                                                            {{ $withdraw->companyAccount->account->account_no . ' ' }},
                                                            {{ ' ' . __('bank') }}:
                                                            {{ __($withdraw->companyAccount->account->bank_name) . ' ' }},
                                                            {{ ' ' . __('branch') }}:{{ $withdraw->companyAccount->account->bank_branch }}({{ $withdraw->companyAccount->account->user->first_name . ' ' . $withdraw->companyAccount->account->user->last_name }})
                                                        @elseif(@$withdraw->companyAccount->account->method == 'cash')
                                                            {{ __('cash') }}
                                                            ({{ $withdraw->companyAccount->account->user->first_name . ' ' . $withdraw->companyAccount->account->user->last_name }})
                                                        @else
                                                            {{ __(@$withdraw->companyAccount->account->method) }},
                                                            {{ __('number') }}:
                                                            {{ @$withdraw->companyAccount->account->number . ' ' }},
                                                            {{ __('account_type') }}:
                                                            {{ __(@$withdraw->companyAccount->account->type) }}({{ $withdraw->companyAccount->account->user->first_name . ' ' . $withdraw->companyAccount->account->user->last_name }})
                                                        @endif
                                                    @endif
                                                </div>
                                            </div><!-- data-item -->
                                            <div class="data-item">
                                                <div class="data-col">
                                                    <span class="data-label">{{ __('transaction_id') }}</span>
                                                    <span
                                                        class="data-value">{{ $withdraw->companyAccountReason->transaction_id }}</span>
                                                </div>
                                            </div><!-- data-item -->
                                            <div class="data-item">
                                                <div class="data-col">
                                                    <span class="data-label">{{ __('receipt') }}</span>
                                                    @if (!blank($withdraw->companyAccount->receipt) && file_exists($withdraw->companyAccount->receipt))
                                                        <span class="data-value"><a
                                                                href="{{ static_asset($withdraw->companyAccount->receipt) }}"
                                                                target="_blank"> <i
                                                                    class="icon  las la-external-link-alt"></i>
                                                                {{ __('receipt') }}</a></span>
                                                    @else
                                                        {{ __('not_available') }}
                                                    @endif
                                                </div>
                                            </div><!-- data-item -->
                                        @endif
                                        <div class="data-item">
                                            <div class="data-col">
                                                <span class="data-label">{{ __('details') }}</span>
                                                <span class="data-value">{{ $withdraw->account->details }}</span>
                                            </div>
                                        </div><!-- data-item -->
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
