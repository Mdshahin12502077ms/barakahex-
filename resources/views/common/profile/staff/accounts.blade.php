@extends('backend.layouts.master')
@section('title')
    {{ __('accounts') }}
@endsection
@section('mainContent')
    <section class="oftions">
        <div class="container-fluid">
            <div class="row">
                @include('common.profile.staff.staff-sidebar')
                <div class="col-xxl-9 col-lg-8 col-md-8">
                    <div class="default-tab-list default-tab-list-v2 bg-white redious-border activeItem-bd-none p-20 p-sm-30">
                        <div>
                            <h5>{{ __('accounts') }}</h4>
                        </div>
                        <table class="table table-responsive">
                            <thead>
                                <tr>
                                    <th>{{ __('account_details') }}</th>
                                    <th>{{ __('opening_balance') }}</th>
                                    <th>{{ __('current_balance') }}</th>
                                    <th>{{ __('options') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($accounts as $account)
                                    <tr>
                                        <td>
                                            @if ($account->method == 'bank')
                                                <span>{{ __('name') }}: {{ $account->account_holder_name }}</span><br>
                                                <span>{{ __('account_no') }}: {{ $account->account_no }}</span><br>
                                                <span>{{ __('account_no') }}: {{ $account->account_no }}</span><br>
                                                <span>{{ __('bank') }}: {{ __($account->bank_name) }}</span><br>
                                                <span>{{ __('branch') }}: {{ $account->bank_branch }}</span><br>
                                            @elseif($account->method == 'cash')
                                                <span>{{ __($account->method) }}</span><br>
                                            @else
                                                <span>{{ __('name') }}: {{ $account->account_holder_name }}</span><br>
                                                <span>{{ __('number') }}: {{ $account->number }}</span><br>
                                                <span>{{ __('account_type') }}: {{ __($account->type) }}</span><br>
                                            @endif
                                        </td>
                                        <td><span class="sub-text">{{ format_price($account->balance) }}</span></td>
                                        <td><span
                                                class="sub-text">{{ format_price($account->incomes()->sum('amount') + $account->fundReceives()->sum('amount') - $account->expenses()->sum('amount') - $account->fundTransfers()->sum('amount')) }}</span>
                                        </td>
                                        <td>
                                            <ul class="list-unstyled">
                                                <li>
                                                    <a class="btn btn-sm sg-btn-primary btn-tooltip"
                                                        title="{{ __('statement') }}"
                                                        href="{{ route('staff.account.statement', $account->id) }}">
                                                        <i class="icon las la-eye"></i>
                                                    </a>
                                                </li>
                                            </ul>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
