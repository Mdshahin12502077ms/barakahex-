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
                                                        <option value="">{{ __('all') }} {{ __('status') }}</option>
                                                        <option value="pending" {{ request()->get('status') == 'pending' ? 'selected' : '' }}>{{ __('pending') }}</option>
                                                        <option value="pickup" {{ request()->get('status') == 'pickup' ? 'selected' : '' }}>{{ __('pickup') }}</option>
                                                        <option value="transit" {{ request()->get('status') == 'transit' ? 'selected' : '' }}>{{ __('transit') }}</option>
                                                        <option value="delivered" {{ request()->get('status') == 'delivered' ? 'selected' : '' }}>{{ __('delivered') }}</option>
                                                        <option value="failed" {{ request()->get('status') == 'failed' ? 'selected' : '' }}>{{ __('failed') }}</option>
                                                        <option value="return" {{ request()->get('status') == 'return' ? 'selected' : '' }}>{{ __('return') }}</option>
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
        @php
            $colors = [
                __('total')     => ['bg' => 'bg-primary-light', 'text' => 'text-primary', 'icon' => 'las la-boxes'],
                __('pending')   => ['bg' => 'bg-warning-light', 'text' => 'text-warning', 'icon' => 'las la-clock'],
                __('pickup')    => ['bg' => 'bg-info-light', 'text' => 'text-info', 'icon' => 'las la-truck-loading'],
                __('transit')   => ['bg' => 'bg-secondary-light', 'text' => 'text-secondary', 'icon' => 'las la-shipping-fast'],
                __('delivered') => ['bg' => 'bg-success-light', 'text' => 'text-success', 'icon' => 'las la-check-circle'],
                __('failed')    => ['bg' => 'bg-danger-light', 'text' => 'text-danger', 'icon' => 'las la-times-circle'],
                __('return')    => ['bg' => 'bg-dark-light', 'text' => 'text-dark', 'icon' => 'las la-undo-alt'],
            ];
        @endphp

        <div class="container-fluid">
            <div class="row gx-20">
                <div class="col-lg-12">
                    <div class="header-top d-flex justify-content-between align-items-center mb-3 mt-3">
                        <h3 class="section-title">{{ __('parcel') . ' ' . __('summery_between') }}
                            {{ $date['start_date'] != '' ? date('M d, Y', strtotime($date['start_date'])) : '' }}
                            - {{ $date['end_date'] != '' ? date('M d, Y', strtotime($date['end_date'])) : '' }}
                        </h3>
                    </div>

                    <!-- 7 Main Summary Cards -->
                    <div class="row g-3 mb-4">
                        @foreach ($data as $event => $count)
                            @php
                                $cfg = $colors[$event] ?? ['bg' => 'bg-light', 'text' => 'text-primary', 'icon' => 'las la-box'];
                            @endphp
                            <div class="col-6 col-md-4 col-lg">
                                <div class="card border-0 shadow-sm rounded-3 p-3 h-100 bg-white">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <span class="text-muted text-uppercase font-12 fw-bold">{{ $event }}</span>
                                            <h3 class="mb-0 mt-2 fw-bold {{ $cfg['text'] }}">{{ number_format($count) }} <small class="font-14 text-muted">{{ __('pcs') }}</small></h3>
                                        </div>
                                        <div class="rounded-circle p-2 {{ $cfg['bg'] }} {{ $cfg['text'] }}">
                                            <i class="{{ $cfg['icon'] }} font-22"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Detailed Breakdown Table -->
                    <div class="bg-white redious-border p-20 p-sm-30 shadow-sm">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">{{ __('status') }}</th>
                                        <th class="text-end pe-3">{{ __('total_parcels') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data as $event => $count)
                                        <tr>
                                            <td class="ps-3">
                                                <span class="fw-semibold text-dark">{{ $event }}</span>
                                            </td>
                                            <td class="text-end pe-3">
                                                <span class="badge bg-light text-dark font-14 px-3 py-2 border">{{ number_format($count) }} {{ __('pcs') }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
@include('live_search.merchants')
