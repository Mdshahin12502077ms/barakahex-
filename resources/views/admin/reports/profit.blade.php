@extends('backend.layouts.master')
@section('title')
    {{ __('profit') }}
@endsection
@section('mainContent')
    <div class="container-fluid">
        <div class="row gx-20">
            <div class="col-lg-12">
                <div class="header-top d-flex justify-content-between align-items-center mb-12">
                    <h3 class="section-title">{{ __('profit') }}</h3>
                </div>
                <form action="{{ route('admin.profit') }}" class="form-validate" method="POST">
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
                                                    <input type="text" class="form-control date-picker" name="start_date"
                                                        autocomplete="off" required placeholder="{{ __('start_date') }}"
                                                        value="{{ request()->get('start_date') }}">
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
                                                    <input type="text" class="form-control date-picker" name="end_date"
                                                        autocomplete="off" required placeholder="{{ __('end_date') }}"
                                                        value="{{ request()->get('end_date') }}">
                                                @if ($errors->has('end_date'))
                                                    <div class="invalid-feedback help-block">
                                                        <p>{{ $errors->first('end_date') }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-4">
                                        <div class="col-sm-12 text-right">
                                            <div class="mb-3">
                                                <button type="submit" class="btn sg-btn-primary resubmit">{{ __('search') }}</button>
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

    @if (isset($profits))
        <div class="container-fluid">
            <div class="row gx-20">
                <div class="col-lg-12">
                    <div class="header-top d-flex justify-content-between align-items-center">
                        <h3 class="section-title">{{ __('profit_info') }}</h3>
                    </div>
                    <div class="bg-white redious-border p-20 p-sm-30 card-stretch">
                        <div class="card-inner-group">
                            <div class="card-inner p-0">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>{{ __('title') }}</th>
                                            <th>{{ __('amount') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{ __('total_charge') }}</td>
                                            <td>{{ format_price(abs($profits['total_charge_vat'])) }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ __('total_delivery_charge') }}</td>
                                            <td> - {{ format_price($profits['total_delivery_charge']) }}<</td>
                                        </tr>
                                        <tr>
                                            <td>{{ __('total_vat') }}</td>
                                            <td> - {{ format_price($profits['total_vat']) }}</td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td><span
                                                   ><strong>{{ __('total_profit') }}</strong></span>
                                            </td>
                                            <td><span
                                                   ><strong>{{ format_price($profits['total_profit']) }}</strong></span>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
