@extends('backend.layouts.master')
@section('title')
    {{ __('accounts') }}
@endsection
@section('mainContent')
    <div class="container-fluid">
        <div class="row gx-20">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-aside-wrap">
                        <div class="card-inner card-inner-lg">
                            <div class="header-top d-flex justify-content-between align-items-center mb-12">
                                <div class="oftions-content-right">
                                    <h4 class="nk-block-title">{{ __('accounts') }}</h4>
                                </div>
                            </div>
                            <div class="bg-white redious-border p-20 p-sm-30 pt-sm-30"">
                                <table class="table table-ulogs">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>{{ __('account_details') }}</th>
                                            <th>{{ __('opening_balance') }}</th>
                                            <th>{{ __('current_balance') }}</th>
                                            @if (hasPermission('account_statement'))
                                                <th>{{ __('options') }}</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($accounts as $account)
                                            <tr>
                                                <td>
                                                    @if ($account->method == 'bank')
                                                        <span>{{ __('name') }}:
                                                            {{ $account->account_holder_name }}</span><br>
                                                        <span>{{ __('account_no') }}: {{ $account->account_no }}</span><br>
                                                        <span>{{ __('account_no') }}:
                                                            {{ $account->account_no }}</span><br>
                                                        <span>{{ __('bank') }}:
                                                            {{ __($account->bank_name) }}</span><br>
                                                        <span>{{ __('branch') }}: {{ $account->bank_branch }}</span><br>
                                                    @elseif($account->method == 'cash')
                                                        <span>{{ __($account->method) }}</span><br>
                                                    @else
                                                        <span>{{ __('name') }}:
                                                            {{ $account->account_holder_name }}</span><br>
                                                        <span>{{ __('number') }}: {{ $account->number }}</span><br>
                                                        <span>{{ __('account_type') }}:
                                                            {{ __($account->type) }}</span><br>
                                                    @endif
                                                </td>
                                                <td><span>{{ format_price($account->balance) }}</span></td>
                                                <td><span>{{ format_price($account->incomes()->sum('amount') + $account->fundReceives()->sum('amount') - $account->expenses()->sum('amount') - $account->fundTransfers()->sum('amount')) }}</span>
                                                </td>
                                                <td>
                                                    <ul class="gx-1">
                                                        <li>
                                                            <div class="drodown">
                                                                <a href="#"
                                                                    class="dropdown-toggle btn btn-icon btn-trigger"
                                                                    data-toggle="dropdown"><i
                                                                        class="icon la lamore-h"></i></a>
                                                                <div class="dropdown-menu dropdown-menu-right">
                                                                    <ul class="link-list-opt no-bdr">
                                                                        @if (hasPermission('account_statement'))
                                                                            <li><a
                                                                                    href="{{ route('admin.account.statement', $account->id) }}"><i
                                                                                        class="icon las la-eye"></i> <span>
                                                                                        {{ __('statement') }}</span></a>
                                                                            </li>
                                                                        @endif
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="d-flex  justify-content-end">
                                    {!! $accounts->appends(Request::except('page'))->links() !!}
                                </div>
                            </div>
                        </div>
                        @include('admin.users.details.sidebar')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
