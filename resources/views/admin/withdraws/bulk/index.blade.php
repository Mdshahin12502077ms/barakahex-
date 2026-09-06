@extends('backend.layouts.master')
@section('title')
    {{ __('bulk_payouts') . ' ' . __('lists') }}
@endsection
@section('payments', 'active')
@section('bulk-withdraws', 'active')
@section('mainContent')
    <div class="container-fluid">
        <div class="row gx-20">
            <div class="col-lg-12">
                <div class="header-top d-flex justify-content-between align-items-center mb-12">
                    <div>
                        <h3 class="section-title">{{ __('lists') }}</h3>
                        <p class="mb-1">{{__('you_have_total')}} {{ $withdraws->total() }} {{ __('payouts') }}.</p>
                    </div>
                    <div class="oftions-content-right">
                        @if (hasPermission('bulk_withdraw_create'))
                            <a href="{{ route('admin.withdraws.bulk.create') }}"
                                class="d-flex align-items-center btn sg-btn-primary gap-2"><i
                                    class="las la-plus"></i><span>{{ __('add') }}</span></a>
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
                                            <div class="default-list-table table-responsive yajra-dataTable">
                                                {{ $dataTable->table(['class' => 'dt-responsive table'], true) }}
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
</div>
    {{--    modal --}}
    {{--    process payment modal --}}
    <div class="modal fade" tabindex="-1" id="withdraw-process">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('process_payment') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.bulk.process-payment') }}" method="POST" class="form-validate is-alter"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id" value="" id="withdraw-process-id">
                        <div class="mb-3">
                            <label class="form-label">{{ __('account') }} <span class="text-danger">*</span></label>
                                <select class="without_search form-select form-control account-change" name="account" required>
                                    <option value="">{{ __('select_account') }}</option>
                                    @foreach ($accounts as $account)
                                        <option value="{{ $account->id }}"
                                            {{ old('account') == $account->id ? 'selected' : '' }}>
                                            ({{ __($account->method) }})
                                            @if ($account->method == 'bank')
                                                {{ $account->account_holder_name . ', ' . $account->account_no . ', ' . __($account->bank_name) . ',' . $account->bank_branch }}.
                                            @elseif($account->method == 'cash')
                                                {{ $account->user->first_name . ' ' . $account->user->last_name }}
                                            @else
                                                {{ $account->account_holder_name . ', ' . $account->number . ', ' . __($account->type) }}
                                            @endif
                                        </option>
                                    @endforeach

                                </select>
                            <div class="text-success">
                                <p>{{ __('current_payable_balance') }}: <span
                                        id="current-balance" class="currency" data-default-currency="{{ setting('default_currency') }}">{{ format_price(0) }}</span></p>
                                <input type="hidden" name="payable_balance" id="payable_balance">
                            </div>
                            @if ($errors->has('account'))
                                <div class="invalid-feedback help-block">
                                    <p>{{ $errors->first('account') }}</p>
                                </div>
                            @endif
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="receipt">{{ __('receipt') }}</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="receipt" name="receipt">
                                    <label class="custom-file-label" for="receipt">{{ __('choose_file') }}</label>
                                </div>
                            @if ($errors->has('receipt'))
                                <div class="invalid-feedback help-block">
                                    <p>{{ $errors->first('receipt') }}</p>
                                </div>
                            @endif
                        </div>
                        <div class="text-right">
                            <button type="submit" class="btn sg-btn-primary resubmit">{{ __('process') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endsection

    @push('script')
        @include('admin.withdraws.status-ajax')
        <script type="text/javascript">
            var currency = document.getElementsByClassName('currency')[0].dataset.defaultCurrency;
            $(document).ready(function() {
                $('.account-change').on('change', function() {

                    var token = "{{ csrf_token() }}";
                    var url = "{{ url('') }}" + '/admin/get-balance-info';

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
                            if (response < 0) {
                                $("#current-balance").parent().removeClass("text-success");
                                $("#current-balance").parent().addClass("text-danger");
                            } else {
                                $("#current-balance").parent().removeClass("text-danger");
                                $("#current-balance").parent().addClass("text-success");
                            }

                            $("#current-balance").html(currency+ response);
                            $("#payable_balance").val(response);

                        })
                        .fail(function(error) {
                            Swal.fire('{{ __('opps') }}...',
                                '{{ __('something_went_wrong_with_ajax') }}', 'error');
                        })

                });
            });
        </script>
    @endpush
    @include('common.delete-ajax')
