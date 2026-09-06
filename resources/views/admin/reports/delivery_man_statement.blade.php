@extends('backend.layouts.master')
@section('title')
    {{ __('delivery_man') . ' ' . __('payment_logs') }}
@endsection
@section('mainContent')
    <div class="container-fluid">
        <div class="row gx-20">
            <div class="col-lg-12">
                <div class="header-top d-flex justify-content-between align-items-center mb-12">
                    <h3 class="section-title">{{ __('delivery_man') . ' ' . __('payment_logs') }}</h3>
                </div>
                <form action="{{ route('admin.search.deliveryman.statement.report') }}" class="form-validate" method="GET">
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
                                                <label class="form-label">{{ __('delivery_man') }}</label>
                                                    <select id="delivery-man-live-search" name="delivery_man"
                                                        class="form-control delivery-man-live-search" data-url="{{ route('get-delivery-man-live') }}">
                                                        {{--                                                        <select class="form-select form-control" name="delivery_man"> --}}
                                                        <option value="">{{ __('select_delivery_man') }}</option>
                                                        @foreach ($delivery_men as $delivery_man)
                                                            <option value="{{ $delivery_man->id }}"
                                                                {{ request()->get('delivery_man') == $delivery_man->id ? 'selected' : '' }}>
                                                                {{ $delivery_man->user->first_name . ' ' . $delivery_man->user->last_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
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
    @if (isset($statements))
        <div class="container-fluid">
            <div class="row gx-20">
                <div class="col-lg-12">
                    <div class="header-top d-flex justify-content-between align-items-center">
                        <h3 class="section-title">{{ __('lists') }}</h3>
                        <div class="oftions-content-right">
                            <p>{{ __('you_have_total') }} {{ $statements->total() }}
                                {{ __('payment_logs') }}.</p>
                        </div>
                    </div>
                    <div class="bg-white redious-border p-20 p-sm-30 card-stretch">
                        <div class="card-inner-group">
                            <div class="card-inner p-0">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('delivery_man') }}</th>
                                            <th>{{ __('details') }}</th>
                                            <th>{{ __('source') }}</th>
                                            <th>{{ __('completed_at') }}</th>
                                            <th>{{ __('amount') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($statements as $key => $statement)
                                            <tr id="row_{{ $statement->id }}">
                                                <td>{{ $key + 1 }}</td>
                                                <td>
                                                    <div class="user-card">
                                                        <div class="user-avatar bg-primary">
                                                            @if (
                                                                !blank($statement->delivery_man->user->image) &&
                                                                    file_exists($statement->delivery_man->user->image->image_small_two))
                                                                <img src="{{ static_asset($statement->delivery_man->user->image->image_small_two) }}"
                                                                    alt="{{ $statement->delivery_man->user->first_name }}">
                                                            @else
                                                                <img src="{{ static_asset('admin/images/default/user40x40.jpg') }}"
                                                                    alt="{{ $statement->delivery_man->user->first_name }}">
                                                            @endif
                                                        </div>
                                                        <div class="user-info">
                                                            <span
                                                                class="tb-lead">{{ $statement->delivery_man->user->first_name . ' ' . $statement->delivery_man->user->last_name }}
                                                                <span class="dot dot-success d-md-none ml-1"></span></span>
                                                            <span>{{ $statement->delivery_man->user->email }}</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span>{{ __($statement->details) }}</span><br>
                                                    @if ($statement->parcel != '')
                                                        {{ __('id') }}:<span>#{{ __(@$statement->parcel->parcel_no) }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span>{{ __($statement->source) }}</span>
                                                </td>
                                                <td>
                                                    {{ $statement->created_at != '' ? date('M d, Y h:i a', strtotime($statement->created_at)) : '' }}
                                                </td>
                                                <td>
                                                    <span
                                                        class="{{ $statement->amount < 0 ? 'text-danger' : '' }}">
                                                        {{ format_price($statement->amount) }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-inner p-2">
                                <div class="-md g-3">
                                    <div class="g">
                                        {!! $statements->appends(Request::except('page'))->links() !!}
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
@include('live_search.delivery-man')
