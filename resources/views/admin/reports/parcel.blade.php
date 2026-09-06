@extends('backend.layouts.master')

@section('title')
    {{ __('parcels_summary') }}
@endsection

@section('mainContent')
    <div class="container-fluid">
        <div class="row gx-20">
            <div class="col-lg-12">
                <div class="header-top d-flex justify-content-between align-items-center mb-12">
                    <h3 class="section-title">{{ __('parcels_summary') }}</h3>
                </div>
                <form action="{{ route('admin.search.parcels') }}" class="form-validate" method="GET">
                    <div class=" bg-white redious-border p-20 p-sm-30">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card-inner">
                                    <div class="row g-gs">
                                        <div class="col-12 col-md-6 col-lg-3">
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
                                        <div class="col-12 col-md-6 col-lg-3">
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
                                        <div class="col-12 col-md-6 col-lg-3">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('select_status') }} </label>
                                                    <select class="without_search form-select form-control search-type" name="status">
                                                        <option value="">{{ __('select_type') }}</option>
                                                        <option value="pending"
                                                            {{ request()->get('status') == 'pending' ? 'selected' : '' }}>
                                                            {{ __('pending') }}</option>
                                                        <option value="pickup-assigned"
                                                            {{ request()->get('status') == 'pickup-assigned' ? 'selected' : '' }}>
                                                            {{ __('pickup-assigned') }}
                                                        </option>
                                                        <option value="re-schedule-pickup"
                                                            {{ request()->get('status') == 're-schedule-pickup' ? 'selected' : '' }}>
                                                            {{ __('re-schedule-pickup') }}
                                                        </option>
                                                        <option value="received-by-pickup-man"
                                                            {{ request()->get('status') == 'received-by-pickup-man' ? 'selected' : '' }}>
                                                            {{ __('received-by-pickup-man') }}</option>
                                                        <option value="received"
                                                            {{ request()->get('status') == 'received' ? 'selected' : '' }}>
                                                            {{ __('received_by_warehouse') }}</option>
                                                        <option value="delivery-assigned"
                                                            {{ request()->get('status') == 'delivery-assigned' ? 'selected' : '' }}>
                                                            {{ __('delivery-assigned') }}
                                                        </option>
                                                        <option value="re-schedule-delivery"
                                                            {{ request()->get('status') == 're-schedule-delivery' ? 'selected' : '' }}>
                                                            {{ __('re-schedule-delivery') }}</option>
                                                        <option value="returned-to-warehouse"
                                                            {{ request()->get('status') == 'returned-to-warehouse' ? 'selected' : '' }}>
                                                            {{ __('returned-to-warehouse') }}
                                                        </option>
                                                        <option value="return-assigned-to-merchant"
                                                            {{ request()->get('status') == 'return-assigned-to-merchant' ? 'selected' : '' }}>
                                                            {{ __('return-assigned-to-merchant') }}</option>
                                                        <option value="returned-to-merchant"
                                                            {{ request()->get('status') == 'returned-to-merchant' ? 'selected' : '' }}>
                                                            {{ __('returned-to-merchant') }}</option>
                                                        <option value="delivered"
                                                            {{ request()->get('status') == 'delivered' ? 'selected' : '' }}>
                                                            {{ __('delivered') }}</option>
                                                        <option value="partially-delivered"
                                                            {{ request()->get('status') == 'partially-delivered' ? 'selected' : '' }}>
                                                            {{ __('partially-delivered') }}
                                                        </option>
                                                        <option value="cancelled"
                                                            {{ request()->get('status') == 'cancelled' ? 'selected' : '' }}>
                                                            {{ __('cancelled') }}</option>
                                                        <option value="deleted"
                                                            {{ request()->get('status') == 'deleted' ? 'selected' : '' }}>
                                                            {{ __('deleted') }}</option>
                                                        <option value="re-request"
                                                            {{ request()->get('status') == 're-request' ? 'selected' : '' }}>
                                                            {{ __('re-request') }}</option>
                                                    </select>
                                                @if ($errors->has('status'))
                                                    <div class="invalid-feedback help-block">
                                                        <p>{{ $errors->first('status') }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6 col-lg-3">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('merchant') }}</label>
                                                    <select id="merchant-live-search" name="merchant"
                                                        class="without_search form-control merchant-live-search">
                                                        <option value="">{{ __('select_merchant') }}</option>
                                                    </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-sm-12 d-flex justify-content-end">
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

    @if (isset($data))
        <div class="container-fluid">
            <div class="row gx-20">
                <div class="col-lg-12">
                    <div class="header-top d-flex justify-content-between align-items-center mb-3 mt-3">
                        <h3 class="section-title">{{ __('parcel') . ' ' . __('summery_between') }}
                            {{ $date['start_date'] != '' ? date('M d, Y', strtotime($date['start_date'])) : '' }}
                            - {{ $date['end_date'] != '' ? date('M d, Y', strtotime($date['end_date'])) : '' }}
                        </h3>
                    </div>
                    <div class="bg-white redious-border p-20 p-sm-30">
                        <div class="card-inner-group">
                            <div class="card-inner p-0">
                                <table class="table">
                                    @foreach ($data as $event => $count)
                                        <tr>
                                            <td class="text-bold">
                                                <h6>{{ $event }}</h6>
                                            </td>
                                            <td class="text-bold">
                                                <h6>{{ $count }}{{ __('pcs') }}</h6>
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
@include('live_search.merchants')
