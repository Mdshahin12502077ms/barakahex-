@extends('backend.layouts.master')
@section('title')
    {{ __('batch_payment_invoice') }}
@endsection
@section('payments', 'active')
@section('bulk-withdraws', 'active')
@section('mainContent')
    <div class="container-fluid">
        <div class="row gx-20">
            <div class="col-lg-12">
                <div class="header-top d-flex justify-content-between align-items-center">
                    <h3 class="section-title">{{ __('batch_payment_invoice') }}</h3>
                    <div class="oftions-content-right">
                        <a href="{{ url()->previous() }}"
                            class="d-md-inline-flex align-items-center btn sg-btn-primary gap-2"><i
                                class="icon las la-arrow-left"></i><span>{{ __('back') }}</span></a>
                    </div>
                </div>
                <div class="bg-white redious-border p-20 p-sm-30 pt-sm-30">
                    <div class="card-inner-group">
                        <div class="card-inner">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="assign-delivery border-bottom">
                                            <div class="row mb-2">
                                                <div class="col-6">
                                                    <div class="right-content d-block">
                                                        <img src="{{ getFileLink('original_image',setting('admin_logo')) }}" alt="logo"
                                                            class="img-fluid image">
                                                        <div class="parcel-details d-block mt-2">
                                                            {{ setting('address') }} <br>
                                                            {{ setting('phone') }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-4">

                                                </div>
                                                <div class="col-2">
                                                    <div class="invoice d-block">
                                                        <center>
                                                            <h4>{{ __('INVOICE') }}</h4>
                                                            <p>{{ __('id') }}# {{ $withdraw->batch_no }}</p>
                                                            {{ __('date') . ': ' . date('d.m.Y') }}
                                                        </center>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{--                                        <div class="row mt-4"> --}}
                                {{--                                            <div class="col-6"> --}}
                                {{--                                                <table class="table table-bordered"> --}}
                                {{--                                                    <tbody> --}}
                                {{--                                                    <tr> --}}
                                {{--                                                        <th scope="row" colspan="2"><span class="center"> {{ __('withdraw_details') }} </span></th> --}}
                                {{--                                                    </tr> --}}
                                {{--                                                    @php --}}
                                {{--                                                        $account_details = json_decode($withdraw->account_details); --}}
                                {{--                                                    @endphp --}}
                                {{--                                                    @if ($withdraw->withdraw_to == 'bank') --}}
                                {{--                                                        <tr> --}}
                                {{--                                                            <th scope="row">{{ __('payment_method') }}</th> --}}
                                {{--                                                            <td>{{ __('bank') }}</td> --}}
                                {{--                                                        </tr> --}}
                                {{--                                                        <tr> --}}
                                {{--                                                            <th scope="row">{{ __('bank_name') }}</th> --}}
                                {{--                                                            <td>{{ @$account_details[0] }}</td> --}}
                                {{--                                                        </tr> --}}
                                {{--                                                        <tr> --}}
                                {{--                                                            <th scope="row">{{ __('branch') }}</th> --}}
                                {{--                                                            <td>{{ @$account_details[1] }}</td> --}}
                                {{--                                                        </tr> --}}
                                {{--                                                        <tr> --}}
                                {{--                                                            <th scope="row">{{ __('account_holder') }}</th> --}}
                                {{--                                                            <td>{{ @$account_details[2] }}</td> --}}
                                {{--                                                        </tr> --}}
                                {{--                                                        <tr> --}}
                                {{--                                                            <th scope="row">{{ __('account_no') }}</th> --}}
                                {{--                                                            <td>{{ @$account_details[3] }}</td> --}}
                                {{--                                                        </tr> --}}
                                {{--                                                        @if (@$account_details[4] != '') --}}
                                {{--                                                        <tr> --}}
                                {{--                                                            <th scope="row">{{ __('routing_no') }}</th> --}}
                                {{--                                                            <td>{{ @$account_details[4] }}</td> --}}
                                {{--                                                        </tr> --}}
                                {{--                                                        @endif --}}
                                {{--                                                    @else --}}
                                {{--                                                        <tr> --}}
                                {{--                                                            <th scope="row">{{ __('payment_method') }}</th> --}}
                                {{--                                                            <td>{{ __(@$withdraw->withdraw_to) }}</td> --}}
                                {{--                                                        </tr> --}}
                                {{--                                                        @if (@$withdraw->withdraw_to == 'bKash' || @$withdraw->withdraw_to == 'nogod' || @$withdraw->withdraw_to == 'rocket') --}}
                                {{--                                                            <tr> --}}
                                {{--                                                                <th scope="row">{{ __('account_type') }}</th> --}}
                                {{--                                                                <td>{{ __(@$account_details[2]) }}</td> --}}
                                {{--                                                            </tr> --}}
                                {{--                                                            <tr> --}}
                                {{--                                                                <th scope="row">{{ __('account_number') }}</th> --}}
                                {{--                                                                <td>{{ @$account_details[1] }}</td> --}}
                                {{--                                                            </tr> --}}

                                {{--                                                        @endif --}}
                                {{--                                                    @endif --}}
                                {{--                                                    </tbody> --}}
                                {{--                                                </table> --}}
                                {{--                                                @if ($withdraw->note != '' or $withdraw->note != null) --}}
                                {{--                                                    <div class="note mt-2"> --}}
                                {{--                                                        <textarea class="form-control">{{ __('note') }}: {{ $withdraw->note }}</textarea> --}}
                                {{--                                                    </div> --}}
                                {{--                                                @endif --}}
                                {{--                                            </div> --}}
                                {{--                                        </div> --}}
                            </div>
                            @if (!blank($withdraw->withdraws))
                                <div class="col-12 mt-5">
                                    <center>
                                        <h5 class="card-title mb-3">{{ __('cleared_payment_requests') }}</h5>
                                        <hr width="50%">
                                    </center>

                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">{{ __('invoice_no') }}</th>
                                                <th scope="col">{{ __('payment_to') }}</th>
                                                <th scope="col">{{ __('amount') }}</th>
                                                @if ($withdraw->status == 'pending' && hasPermission('add_to_bulk_withdraw'))
                                                    <th scope="col">{{ __('options') }}</th>
                                                @endif
                                            </tr>

                                            @foreach ($withdraw->withdraws as $key => $request)
                                                <tr id="row_{{ $request->id }}">
                                                    <td>
                                                        {{ $key + 1 }}
                                                    </td>
                                                    <td>{{ $request->withdraw_id }}</td>
                                                    <td>
                                                        @php
                                                            $account_details = json_decode($request->account_details);
                                                        @endphp
                                                        <span>{{ __('merchant') . ': ' . $request->merchant->company }}</span>
                                                        <br>
                                                        @if (@$request->payments->paymentAccount->type == 'cash')
                                                            <tr>
                                                                <th scope="row">{{ __('account_holder_name') }}</th>
                                                                <td>{{ __($withdraw->account->account_holder_name) }}</td>
                                                            </tr>
                                                        @elseif (@$request->payments->paymentAccount->type == 'bank')
                                                            <span>{{ __('payment_method') . ': ' . __($request->payments->paymentAccount->name) }}
                                                            <span>{{ __('bank_name') . ': ' . @$account_details[0] }} </span>
                                                            <br>
                                                            <span>{{ __('branch') . ': ' . @$account_details[1] }}</span> <br>
                                                            <span>{{ __('account_holder') . ': ' . @$account_details[2] }}</span>
                                                            <br>
                                                            <span>{{ __('account_no') . ': ' . @$account_details[3] }}</span>
                                                            @if (@$account_details[4] != '')
                                                                <br>
                                                                <span>{{ __('routing_no') . ': ' . @$account_details[4] }}
                                                            @endif
                                                            </span>
                                                        @else
                                                            <span>{{ __('payment_method') . ': ' . __($request->payments->paymentAccount->name) }}
                                                            </span>
                                                            <br><span>{{ __('account_type') . ': ' . __(@$account_details[0]) }}
                                                            </span> <br>
                                                            <span>{{ __('account_number') . ': ' . @$account_details[2] }}
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{ format_price($request->amount) }}
                                                    </td>.
                                                    @if ($withdraw->status == 'pending' && hasPermission('add_to_bulk_withdraw'))
                                                        <td>
                                                            <button type="button"
                                                                onclick="delete_row('remove-from-batch/', {{ $request->id }})"
                                                                class="btn btn-danger"><i class="la latrash"></i></button>
                                                        </td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                            <tr>
                                                <th scope="row" colspan="3">{{ __('total_amount') }}</th>
                                                <td>{{ format_price($withdraw->withdraws->sum('amount')) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                            <div class="col-12 mt-5">
                                <center>
                                    <h5 class="card-title mb-3">{{ __('processed_from_account_details') }}</h5>
                                    <p>{{ __('account_info_from_which_this_transaction_processed') }}</p>
                                    <hr width="50%">
                                </center>

                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <th scope="row">{{ __('request_created_by') }}</th>
                                            <td>{{ __($withdraw->user->first_name . ' ' . $withdraw->user->last_name . ' ') }}({{ __($withdraw->user->user_type) }})
                                            </td>
                                        </tr>

                                        @isset($withdraw->account)
                                            <tr>
                                                <th scope="row">{{ __('payment_method') }}</th>
                                                <td>{{ __($withdraw->account->method) }}</td>
                                            </tr>
                                            @if ($withdraw->account->method != 'cash')
                                                <tr>
                                                    <th scope="row">{{ __('account_holder_name') }}</th>
                                                    <td>{{ __($withdraw->account->account_holder_name) }}</td>
                                                </tr>
                                            @endif
                                            @if ($withdraw->account->method == 'bank')
                                                <tr>
                                                    <th scope="row">{{ __('bank_name') }}</th>
                                                    <td>{{ __($withdraw->account->bank_name) }}</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">{{ __('branch') }}</th>
                                                    <td>{{ __($withdraw->account->bank_branch) }}</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">{{ __('account_no') }}</th>
                                                    <td>{{ __($withdraw->account->account_no) }}</td>
                                                </tr>
                                            @elseif($withdraw->account->method == 'cash')
                                                <tr>
                                                    <th scope="row">{{ __('account_holder_name') }}</th>
                                                    <td>{{ __($withdraw->account->user->first_name . ' ' . $withdraw->account->user->last_name) }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">{{ __('email') }}</th>
                                                    <td>{{ __($withdraw->account->user->email) }}</td>
                                                </tr>
                                            @else
                                                <tr>
                                                    <th scope="row">{{ __('account_type') }}</th>
                                                    <td>{{ __($withdraw->account->type) }}</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">{{ __('account_number') }}</th>
                                                    <td>{{ $withdraw->account->number }}</td>
                                                </tr>
                                            @endif
                                        @endisset
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@include('common.delete-ajax')
