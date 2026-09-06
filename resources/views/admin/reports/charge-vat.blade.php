@extends('backend.layouts.master')
@section('title')
    {{ __('income') . '/' . __('expense') }}
@endsection
@section('mainContent')
    <div class="container-fluid">
        <div class="row gx-20">
            <div class="col-lg-12">
                <div class="header-top d-flex justify-content-between align-items-center mb-12">
                    <h3 class="section-title">{{ __('income') . '/' . __('expense') }}</h3>
                </div>
                <form action="{{ route('admin.search.income.expense') }}" class="form-validate" method="POST">
                    @csrf
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
                                                        autocomplete="off" required placeholder="{{ __('start_date') }}"
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
                                                <label class="form-label">{{ __('report_type') }} <span
                                                        class="text-danger">*</span></label>
                                                    <select class="form-select form-control" name="report_type" required>
                                                        <option value="">{{ __('select_report_type') }}</option>
                                                        <option value="statement"
                                                            {{ request()->get('report_type') == 'statement' ? 'selected' : '' }}>
                                                            {{ __('statement') }}</option>
                                                        <option value="summery"
                                                            {{ request()->get('report_type') == 'summery' ? 'selected' : '' }}>
                                                            {{ __('summery') }}</option>
                                                    </select>
                                                @if ($errors->has('type'))
                                                    <div class="invalid-feedback help-block">
                                                        <p>{{ $errors->first('type') }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-3 col-lg-3">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('select_type') }}</label>
                                                    <select class="form-select form-control search-type" name="type">
                                                        <option value="">{{ __('select_type') }}</option>
                                                        @if (hasPermission('income_report_read'))
                                                            <option value="income"
                                                                {{ request()->get('type') == 'income' ? 'selected' : '' }}>
                                                                {{ __('income') }}</option>
                                                        @endif
                                                        @if (hasPermission('expense_report_read'))
                                                            <option value="expense"
                                                                {{ request()->get('type') == 'expense' ? 'selected' : '' }}>
                                                                {{ __('expense') }}</option>
                                                        @endif
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

    @if (isset($accounts))
        <div class="container-fluid">
            <div class="row gx-20">
                <div class="col-lg-12">
                    <div class="header-top d-flex justify-content-between align-items-center">
                        <h3 class="section-title">{{ __('lists') }}</h3>
                        <div class="oftions-content-right">
                            <p>{{ __('you_have_total') }} {{ $accounts->total() }} {{ __($type) }}.</p>
                        </div>
                    </div>
                    <div class="bg-white redious-border p-20 p-sm-30 card-stretch">
                        <div class="card-inner-group">
                            <div class="card-inner p-0">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('date') }}</th>
                                            <th>{{ __('source') }} / {{ __('details') }}</th>
                                            <th>{{ __('credit') }}</th>
                                            <th>{{ __('debit') }}</th>
                                            <th>{{ __('balance') }}</th>
                                        </tr>
                                    </thead>
                                    @php $balance = 0; @endphp
                                    <tbody>
                                        @foreach ($accounts as $key => $company_account)
                                            <tr id="row_{{ $company_account->id }}">
                                                <td>
                                                    <span>{{ $key + 1 }}</span>
                                                </td>
                                                <td>
                                                    {{ $company_account->date != '' ? date('M d, Y', strtotime($company_account->date)) : '' }}
                                                </td>

                                                <td>
                                                    {{ __($company_account->source) }} <br>
                                                    {{ __($company_account->details) }}
                                                </td>

                                                <td>
                                                    @if ($company_account->type == 'income')
                                                        {{ format_price($company_account->amount) }}
                                                        @php $balance += $company_account->amount; @endphp
                                                    @else
                                                        <span>-.--</span>
                                                    @endif

                                                </td>

                                                <td class="text-danger">

                                                    @if ($company_account->type == 'expense')
                                                        {{ format_price($company_account->amount) }}
                                                        @php $balance -= $company_account->amount; @endphp
                                                    @else
                                                        <span>-.--</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ format_price($balance) }}
                                                </td>

                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-inner p-2">
                                <div class="-md g-3">
                                    <div class="g">
                                        {!! $accounts->appends(Request::except('page', '_token'))->links() !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    @if (isset($summery_accounts))
        <div class="container-fluid">
            <div class="row gx-20">
                <div class="col-lg-12">
                    <div class="header-top d-flex justify-content-between align-items-center">
                        <h3 class="section-title">{{ __('lists') }}</h3>
                        <div class="oftions-content-right">
                            <p>{{ __('you_have_total') }} {{ $summery_accounts->total() }}
                                {{ __($type) }}.</p>
                        </div>
                    </div>
                    <div class="bg-white redious-border p-20 p-sm-30 card-stretch">
                        <div class="card-inner-group">
                            <div class="card-inner p-0">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('date') }}</th>
                                            <th>{{ __('credit') }}</th>
                                            <th>{{ __('debit') }}</th>
                                            <th>{{ __('balance') }}</th>
                                        </tr>
                                    </thead>
                                    @php
                                        $balance = 0;
                                        $i = 1;
                                    @endphp
                                    <tbody>
                                        @foreach ($main_datas as $key => $main_datas)
                                            @php
                                            @endphp
                                            <tr>
                                                <td>
                                                    <span>{{ $i++ }}</span>
                                                </td>
                                                <td>
                                                    {{ $key != '' ? date('M d, Y', strtotime($key)) : '' }}
                                                </td>
                                                <td>
                                                    @if (data_get($main_datas, 'data1.type') == 'income')
                                                        {{ format_price(data_get($main_datas, 'data1.amount')) }}
                                                        @php $balance += data_get($main_datas, 'data1.amount'); @endphp
                                                    @elseif(data_get($main_datas, 'data2.type') == 'income')
                                                        {{ format_price(data_get($main_datas, 'data2.amount')) }}
                                                        @php $balance += data_get($main_datas, 'data2.amount'); @endphp
                                                    @else
                                                        <span>-.--</span>
                                                    @endif
                                                </td>
                                                <td class="nk-tb-col text-danger">
                                                    @if (data_get($main_datas, 'data1.type') == 'expense')
                                                        {{ format_price(data_get($main_datas, 'data1.amount')) }}
                                                        @php $balance -= data_get($main_datas, 'data1.amount'); @endphp
                                                    @elseif(data_get($main_datas, 'data2.type') == 'expense')
                                                        {{ format_price(data_get($main_datas, 'data2.amount')) }}
                                                        @php $balance -= data_get($main_datas, 'data2.amount'); @endphp
                                                    @else
                                                        <span>-.--</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ format_price($balance) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-inner p-2">
                                <div class="-md g-3">
                                    <div class="g">
                                        {!! $summery_accounts->appends(Request::except('page', '_token'))->links() !!}
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
