@extends('backend.layouts.master')
@section('title')
    {{ __('payment_logs') }}
@endsection
@section('mainContent')
    <section class="oftions">
        <div class="container-fluid">
            <div class="row">
                @include('admin.delivery-man.details.sidebar')
                <div class="col-xxl-9 col-lg-8 col-md-8">
                    <div
                        class="default-tab-list default-tab-list-v2 bg-white redious-border activeItem-bd-none p-20 p-sm-30">
                        <div class="header-top d-flex justify-content-between align-items-center mb-12">
                            <h5>{{ __('payout_logs') }}</h5>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="text-nowrap table-responsive">
                                    <table class="table table-ulogs">
                                        <thead class="thead-light">
                                            <tr class="statement">
                                                <th>{{ __('details') }}</th>
                                                <th>{{ __('source') }}</th>
                                                <th>{{ __('completed_at') }}</th>
                                                <th>{{ __('amount') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($statements as $statement)
                                                <tr class="statement">
                                                    <td>
                                                        <span>{{ __($statement->details) }}</span><br>
                                                        @if ($statement->parcel != '')
                                                            {{ __('id') }}:<span>#{{ __(@$statement->parcel->parcel_no) }}</span>
                                                        @endif
                                                    </td>
                                                    <td><span>{{ __($statement->source) }} </span></td>
                                                    <td><span>{{ $statement->created_at != '' ? date('M d, Y h:i a', strtotime($statement->created_at)) : '' }}
                                                        </span></td>
                                                    @if ($statement->type == 'income')
                                                        <td><span>{{ format_price($statement->amount) }} </span></td>
                                                    @else
                                                        <td class="text-danger">
                                                            <span>{{ format_price($statement->amount) }} </span></td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="d-flex  justify-content-end">
                                {!! $statements->appends(Request::except('page'))->links() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
