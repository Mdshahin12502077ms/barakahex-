@extends('backend.layouts.master')
@section('title')
    {{ __('account') }}
@endsection
@section('mainContent')
    <div class="container-fluid">
        <div class="row gx-20">
            <div class="col-lg-12">
                <div class="header-top d-flex justify-content-between align-items-center mb-12">
                    <h3 class="section-title">{{ __('account') }}</h3>
                </div>
                <form action="{{ route('admin.search.account.report') }}" class="form-validate" method="GET">
                    <div class="bg-white redious-border p-20 p-sm-30">
                        <div class="card-inner">
                            <div class="row g-gs">
                                <div class="col-12 col-md-3 col-lg-3">
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('start_date') }} <span
                                                class="text-danger">*</span></label>
                                        <div class="form-control-wrap focused">
                                            <input type="text" class="form-control date-picker" name="start_date"
                                                value="{{ old('start_date') }}" autocomplete="off" required
                                                placeholder="{{ __('start_date') }}"
                                                value="{{ request()->get('start_date') }}">
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
                                                value="{{ old('end_date') }}" autocomplete="off" required
                                                placeholder="{{ __('end_date') }}"
                                                value="{{ request()->get('end_date') }}">
                                        </div>
                                        @if ($errors->has('end_date'))
                                            <div class="invalid-feedback help-block">
                                                <p>{{ $errors->first('end_date') }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-12 col-md-3 col-lg-3" id="user-select-area">
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('user') }}</label>
                                            <select id="user" name="user" class="form-control user-live-search change-user">
                                            </select>
                                        @if ($errors->has('user'))
                                            <div class="invalid-feedback help-block">
                                                <p>{{ $errors->first('user') }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row g-gs">
                                <div class="col-12 col-md-3 col-lg-3">
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('account') }}</label>
                                            <select class="form-select form-control account-change" name="account">
                                                <option value="">{{ __('select_account') }}</option>
                                            </select>
                                        @if ($errors->has('report_type'))
                                            <div class="invalid-feedback help-block">
                                                <p>{{ $errors->first('report_type') }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-12 col-md-3 col-lg-3 d-none">
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('select_type') }}</label>
                                            <select class="form-select form-control statement-type" name="type"
                                                required=false>
                                                <option value="">{{ __('select_type') }}</option>
                                                <option value="income"
                                                    {{ request()->get('type') == 'income' ? 'selected' : '' }}>
                                                    {{ __('income') }}</option>
                                                <option value="expense"
                                                    {{ request()->get('type') == 'expense' ? 'selected' : '' }}>
                                                    {{ __('expense') }}</option>
                                                <option value="fund_received"
                                                    {{ request()->get('type') == 'fund_received' ? 'selected' : '' }}>
                                                    {{ __('fund_received') }}</option>
                                                <option value="fund_transfered"
                                                    {{ request()->get('type') == 'fund_transfered' ? 'selected' : '' }}>
                                                    {{ __('fund_transfered') }}
                                                </option>
                                            </select>
                                        @if ($errors->has('type'))
                                            <div class="invalid-feedback help-block">
                                                <p>{{ $errors->first('type') }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-sm-12 text-right">
                                    <div class="mb-3">
                                        <button type="submit" class="btn sg-btn-primary d-md-inline-flex">{{ __('search') }}</button>
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
                    <div class="header-top d-flex justify-content-between align-items-center">
                        <h3 class="section-title">{{ __('lists') }}</h3>
                    </div>
                    <div class="bg-white redious-border p-20 p-sm-30 card-stretch">
                        <div class="card-inner-group">
                            @if ($type == 'date-wise')
                                <div class="card-inner p-0">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>{{ __('date') }}</th>
                                                <th>{{ __('account') }}</th>
                                                <th>{{ __('description') }}</th>
                                                <th>{{ __('debit') }}/{{ __('income') }}</th>
                                                <th>{{ __('credit') }}/{{ __('expense') }}</th>
                                                <th>{{ __('balance') }}</th>
                                            </tr>
                                        </thead>
                                        @php
                                            $balance = 0;
                                            $total_income = 0;
                                            $total_expense = 0;
                                        @endphp
                                        @foreach ($data as $key => $value)
                                            @if ($value['table'] == 'company_accounts')
                                                <tr>
                                                    <td>
                                                        {{ $value['date'] != '' ? date('M d, Y', strtotime($value['date'])) : '' }}
                                                    </td>
                                                    <td>
                                                        @if ($value['data']->account->method == 'bank')
                                                            {{ __('name') }}:
                                                            {{ $value['data']->account->account_holder_name }}<br>
                                                            {{ __('account_no') }}:
                                                            {{ $value['data']->account->account_no }}<br>
                                                            {{ __('bank') }}:
                                                            {{ __($value['data']->account->bank_name) }}<br>
                                                            {{ __('branch') }}:{{ $value['data']->account->bank_branch }}<br>
                                                        @elseif($value['data']->account->method == 'cash')
                                                            {{ __('name') }}:
                                                            {{ $value['data']->account->user->first_name . ' ' . $value['data']->account->user->last_name }}({{ __($value['data']->account->method) }})<br>
                                                            {{ __('email') }}:
                                                            {{ $value['data']->account->user->email }}<br>
                                                        @else
                                                            {{ __('name') }}:
                                                            {{ $value['data']->account->account_holder_name }}<br>
                                                            {{ __('number') }}:
                                                            {{ $value['data']->account->number }}<br>
                                                            {{ __('account_type') }}:
                                                            {{ __($value['data']->account->type) }}<br>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($value['data']->type == 'income')
                                                            {{ __($value['data']->details) }}
                                                            {{ __($value['data']->note) }}
                                                        @else
                                                            {{ __($value['data']->details) }}
                                                            {{ __($value['data']->title) }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($value['data']->type == 'income')
                                                            {{ format_price($value['data']->amount) }}
                                                            @php
                                                                $balance += $value['data']->amount;
                                                                $total_income += $value['data']->amount;
                                                            @endphp
                                                        @else
                                                            -.--
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($value['data']->type == 'expense')
                                                            {{ format_price(abs($value['data']->amount)) }}
                                                            @php
                                                                $balance -= abs($value['data']->amount);
                                                                $total_expense += abs($value['data']->amount);
                                                            @endphp
                                                        @else
                                                            -.--
                                                        @endif
                                                    </td>
                                                    <td>{{ format_price($balance) }}</td>
                                                </tr>
                                            @else
                                                <tr>
                                                    <td>
                                                        {{ $value['date'] != '' ? date('M d, Y', strtotime($value['date'])) : '' }}
                                                    </td>
                                                    <td>
                                                        @if ($value['data']->fromAccount->method == 'bank')
                                                            {{ __('name') }}:
                                                            {{ $value['data']->fromAccount->account_holder_name }}<br>
                                                            {{ __('account_no') }}:
                                                            {{ $value['data']->fromAccount->account_no }}<br>
                                                            {{ __('bank') }}:
                                                            {{ __($value['data']->fromAccount->bank_name) }}<br>
                                                            {{ __('branch') }}:{{ $value['data']->fromAccount->bank_branch }}<br>
                                                        @elseif($value['data']->fromAccount->method == 'cash')
                                                            {{ __('name') }}:
                                                            {{ $value['data']->fromAccount->user->first_name . ' ' . $value['data']->fromAccount->user->last_name }}({{ __($value['data']->toAccount->method) }})<br>
                                                            {{ __('email') }}:
                                                            {{ $value['data']->fromAccount->user->email }}<br>
                                                        @else
                                                            {{ __('name') }}:
                                                            {{ $value['data']->fromAccount->account_holder_name }}<br>
                                                            {{ __('number') }}:
                                                            {{ $value['data']->account->fromAccount }}<br>
                                                            {{ __('account_type') }}:
                                                            {{ __($value['data']->fromAccount->type) }}<br>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        Fund Transfered
                                                    </td>
                                                    <td>
                                                        -.--
                                                    </td>
                                                    <td>
                                                        {{ format_price($value['data']->amount) }}
                                                        @php
                                                            $balance -= $value['data']->amount;
                                                            $total_expense += abs($value['data']->amount);
                                                        @endphp
                                                    </td>
                                                    <td>
                                                        {{ format_price($balance) }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        {{ $value['date'] != '' ? date('M d, Y', strtotime($value['date'])) : '' }}
                                                    </td>
                                                    <td>
                                                        @if ($value['data']->toAccount->method == 'bank')
                                                            {{ __('name') }}:
                                                            {{ $value['data']->toAccount->account_holder_name }}<br>
                                                            {{ __('account_no') }}:
                                                            {{ $value['data']->toAccount->account_no }}<br>
                                                            {{ __('bank') }}:
                                                            {{ __($value['data']->toAccount->bank_name) }}<br>
                                                            {{ __('branch') }}:{{ $value['data']->toAccount->bank_branch }}<br>
                                                        @elseif($value['data']->toAccount->method == 'cash')
                                                            {{ __('name') }}:
                                                            {{ $value['data']->toAccount->user->first_name . ' ' . $value['data']->toAccount->user->last_name }}({{ __($value['data']->toAccount->method) }})<br>
                                                            {{ __('email') }}:
                                                            {{ $value['data']->toAccount->user->email }}<br>
                                                        @else
                                                            {{ __('name') }}:
                                                            {{ $value['data']->toAccount->account_holder_name }}<br>
                                                            {{ __('number') }}:
                                                            {{ $value['data']->toAccount->number }}<br>
                                                            {{ __('account_type') }}:
                                                            {{ __($value['data']->toAccount->type) }}<br>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span>Fund Received</span>
                                                    </td>

                                                    <td>
                                                        {{ format_price($value['data']->amount) }}
                                                        @php
                                                            $balance += $value['data']->amount;
                                                            $total_income += $value['data']->amount;
                                                        @endphp
                                                    </td>
                                                    <td>
                                                        -.--
                                                    </td>
                                                    <td>
                                                        {{ format_price($balance) }}
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                        <tr>
                                            <td colspan="3"><span
                                                   ><strong>{{ __('grand_total') }}</strong></strong></span>
                                            </td>
                                            <td><span
                                                   ><strong>{{ format_price($total_income) }}</strong></span>
                                            </td>
                                            <td><span
                                                   ><strong>{{ format_price($total_expense) }}</strong></span>
                                            </td>
                                            <td><span
                                                   ><strong>{{ format_price($balance) }}</strong></span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            @elseif($type == 'date-user-wise')
                                <div class="card-inner p-0">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>{{ __('date') }}</th>
                                                <th>{{ __('account') }}</th>
                                                <th>{{ __('description') }}</th>
                                                <th>{{ __('debit') }}/{{ __('income') }}</th>
                                                <th>{{ __('credit') }}/{{ __('expense') }}</th>
                                                <th>{{ __('balance') }}</th>
                                            </tr>
                                        </thead>
                                        @php
                                            $balance = 0;
                                            $total_income = 0;
                                            $total_expense = 0;
                                        @endphp
                                        <tbody>
                                            @foreach ($data as $key => $value)
                                                @if ($value['table'] == 'company_accounts')
                                                    <tr>
                                                        <td>
                                                            {{ $value['date'] != '' ? date('M d, Y', strtotime($value['date'])) : '' }}
                                                        </td>
                                                        <td>
                                                            @if ($value['data']->account->method == 'bank')
                                                                {{ __('name') }}:
                                                                {{ $value['data']->account->account_holder_name }}<br>
                                                                {{ __('account_no') }}:
                                                                {{ $value['data']->account->account_no }}<br>
                                                                {{ __('bank') }}:
                                                                {{ __($value['data']->account->bank_name) }}<br>
                                                                {{ __('branch') }}:{{ $value['data']->account->bank_branch }}<br>
                                                            @elseif($value['data']->account->method == 'cash')
                                                                {{ __('name') }}:
                                                                {{ $value['data']->account->user->first_name . ' ' . $value['data']->account->user->last_name }}({{ __($value['data']->account->method) }})<br>
                                                                {{ __('email') }}:
                                                                {{ $value['data']->account->user->email }}<br>
                                                            @else
                                                                {{ __('name') }}:
                                                                {{ $value['data']->account->account_holder_name }}<br>
                                                                {{ __('number') }}:
                                                                {{ $value['data']->account->number }}<br>
                                                                {{ __('account_type') }}:
                                                                {{ __($value['data']->account->type) }}<br>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($value['data']->type == 'income')
                                                                {{ __($value['data']->details) }}
                                                                {{ __($value['data']->note) }}
                                                            @else
                                                                {{ __($value['data']->details) }}
                                                                {{ __($value['data']->title) }}
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($value['data']->type == 'income')
                                                                {{ format_price($value['data']->amount) }}
                                                                @php
                                                                    $balance += $value['data']->amount;
                                                                    $total_income += $value['data']->amount;
                                                                @endphp
                                                            @else
                                                                -.--
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($value['data']->type == 'expense')
                                                                {{ format_price(abs($value['data']->amount)) }}
                                                                @php
                                                                    $balance -= abs($value['data']->amount);
                                                                    $total_expense += abs($value['data']->amount);
                                                                @endphp
                                                            @else
                                                                -.--
                                                            @endif
                                                        </td>
                                                        <td>
                                                            {{ format_price($balance) }}
                                                        </td>
                                                    </tr>
                                                @elseif($value['table'] == 'fund_transfers')
                                                    <tr>
                                                        <td>
                                                            {{ $value['date'] != '' ? date('M d, Y', strtotime($value['date'])) : '' }}
                                                        </td>
                                                        <td>
                                                            @if ($value['data']->fromAccount->method == 'bank')
                                                                {{ __('name') }}:
                                                                {{ $value['data']->fromAccount->account_holder_name }}<br>
                                                                {{ __('account_no') }}:
                                                                {{ $value['data']->fromAccount->account_no }}<br>
                                                                {{ __('bank') }}:
                                                                {{ __($value['data']->fromAccount->bank_name) }}<br>
                                                                {{ __('branch') }}:{{ $value['data']->fromAccount->bank_branch }}<br>
                                                            @elseif($value['data']->fromAccount->method == 'cash')
                                                                {{ __('name') }}:
                                                                {{ $value['data']->fromAccount->user->first_name . ' ' . $value['data']->fromAccount->user->last_name }}({{ __($value['data']->toAccount->method) }})<br>
                                                                {{ __('email') }}:
                                                                {{ $value['data']->fromAccount->user->email }}<br>
                                                            @else
                                                                {{ __('name') }}:
                                                                {{ $value['data']->fromAccount->account_holder_name }}<br>
                                                                {{ __('number') }}:
                                                                {{ $value['data']->account->fromAccount }}<br>
                                                                {{ __('account_type') }}:
                                                                {{ __($value['data']->fromAccount->type) }}<br>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <span>Fund Transfered</span>
                                                        </td>
                                                        <td>
                                                            -.--
                                                        </td>
                                                        <td>
                                                            {{ format_price($value['data']->amount) }}
                                                            @php
                                                                $balance -= $value['data']->amount;
                                                                $total_expense += abs($value['data']->amount);
                                                            @endphp
                                                        </td>
                                                        <td>
                                                            {{ format_price($balance) }}
                                                        </td>
                                                    </tr>
                                                @else
                                                    <tr>
                                                        <td>
                                                            {{ $value['date'] != '' ? date('M d, Y', strtotime($value['date'])) : '' }}
                                                        </td>
                                                        <td>
                                                            @if ($value['data']->toAccount->method == 'bank')
                                                                {{ __('name') }}:
                                                                {{ $value['data']->toAccount->account_holder_name }}<br>
                                                                {{ __('account_no') }}:
                                                                {{ $value['data']->toAccount->account_no }}<br>
                                                                {{ __('bank') }}:
                                                                {{ __($value['data']->toAccount->bank_name) }}<br>
                                                                {{ __('branch') }}:{{ $value['data']->toAccount->bank_branch }}<br>
                                                            @elseif($value['data']->toAccount->method == 'cash')
                                                                {{ __('name') }}:
                                                                {{ $value['data']->toAccount->user->first_name . ' ' . $value['data']->toAccount->user->last_name }}({{ __($value['data']->toAccount->method) }})<br>
                                                                {{ __('email') }}:
                                                                {{ $value['data']->toAccount->user->email }}<br>
                                                            @else
                                                                {{ __('name') }}:
                                                                {{ $value['data']->toAccount->account_holder_name }}<br>
                                                                {{ __('number') }}:
                                                                {{ $value['data']->toAccount->number }}<br>
                                                                {{ __('account_type') }}:
                                                                {{ __($value['data']->toAccount->type) }}<br>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <span>Fund Received</span>
                                                        </td>
                                                        <td>
                                                            {{ format_price($value['data']->amount) }}
                                                            @php
                                                                $balance += $value['data']->amount;
                                                                $total_income += $value['data']->amount;
                                                            @endphp
                                                        </td>
                                                        <td>
                                                            -.--
                                                        </td>
                                                        <td>
                                                            {{ format_price($balance) }}
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="3"><span><strong>{{ __('grand_total') }}</strong></strong></span>
                                                </td>
                                                <td><span><strong>{{ format_price($total_income) }}</strong></span>
                                                </td>
                                                <td><span><strong>{{ format_price($total_expense) }}</strong></span>
                                                </td>
                                                <td><span><strong>{{ format_price($balance) }}</strong></span>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

@endsection
@push('script')
    <script>
        $(document).ready(function() {
            $('.search-type').on('change', function() {
                if ($(this).val() == 'cash') {
                    $('#user-area').removeClass('d-none');
                    $('#user-select-area').removeClass('d-none');
                } else {
                    $('#user-area').addClass('d-none');
                }
            })
        });


        $(document).ready(function() {
            $('.change-user').on('change', function() {

                var token = "{{ csrf_token() }}";
                var url = "{{ url('') }}" + '/admin/get-accounts';

                var formData = {
                    id: $(this).val()
                }

                $.ajax({
                        type: 'GET',
                        dataType: 'json',
                        data: formData,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: url,
                    })
                    .done(function(response) {
                        var option = '';
                        $(response).each(function(index, account) {
                            if (account.method == 'bank') {
                                option += "<option value='" + account.id + "'>" + account
                                    .account_holder_name + ', ' + account.account_no +
                                    "</option>";
                            } else if (account.method == 'cash') {
                                option += "<option value='" + account.id + "'>" + account.user
                                    .first_name + ' ' + account.user.last_name + ', ' + account
                                    .user.email + "</option>";
                            } else {
                                option += "<option value='" + account.id + "'>" + account
                                    .account_holder_name + ', ' + account.number + "</option>";
                            }

                        });

                        $('.account-change').find('option').not(':first').remove();
                        $('.account-change').append(option);

                    })
                    .fail(function(error) {
                        Swal.fire('{{ __('opps') }}...',
                            '{{ __('something_went_wrong_with_ajax') }}', 'error');
                    })

            });
        });
    </script>
@endpush
@include('live_search.user')
