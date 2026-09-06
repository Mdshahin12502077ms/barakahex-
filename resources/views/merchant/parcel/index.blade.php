@extends('backend.layouts.master')
@section('title')
    {{ __('parcel') . ' ' . __('lists') }}
@endsection
@section('style')
    <style>
        .text-fliter-btn {
            color: #8599b1 !important;
        }
    </style>
@endsection
@php
    $pn = isset($_GET['pn']) ? $_GET['pn'] : null;
@endphp
@section('mainContent')
    <div class="container-fluid">
        <div class="row gx-20">
            <div class="col-lg-12">
                <div class="header-top d-flex justify-content-between align-items-center">
                    <div>
                        <div>
                            <h3 class="section-title">{{ (isset($slug) && $slug == 'returned-to-merchant') || request('status') == 'returned-to-merchant' ? __('returned_parcels') : ((isset($slug) && $slug == 'pending-return') || request('status') == 'pending-return' ? __('pending_returns') : __('lists')) }}</h3>
                        </div>
                        <div>
                            <p>{{ __('you_have_total') }} {{ $parcels->total() }} {{ __('parcel') }}.</p>
                        </div>
                    </div>
                    <div class="oftions-content-right mb-12">
                        <a href="#" class="d-flex align-items-center btn sg-btn-primary gap-2" id="filterBTN">
                            <i class="las la-filter"></i>
                        </a>
                        @if((!isset($slug) || ($slug != 'returned-to-merchant' && $slug != 'pending-return')) && !request('status'))
                            @if (@settingHelper('preferences')->where('title', 'create_parcel')->first()->merchant)
                                <a href="{{ Sentinel::getUser()->user_type == 'merchant' ? route('merchant.import.csv') : route('merchant.staff.import.csv') }}"
                                    class="d-flex align-items-center btn sg-btn-primary gap-2"><i
                                        class="icon la la-plus"></i><span>{{ __('import') }}</span></a>
                                <a href="{{ Sentinel::getUser()->user_type == 'merchant' ? route('merchant.parcel.create') : route('merchant.staff.parcel.create') }}"
                                    class="d-flex align-items-center btn sg-btn-primary gap-2"><i
                                        class="icon la la-plus"></i><span>{{ __('create_parcel') }}</span></a>
                            @else
                                <a class="d-flex align-items-center btn sg-btn-primary gap-2"><i
                                        class="icon la la-plus"></i><span>{{ __('create') . ' ' . __('parcel') . ' (' . __('service_unavailable') . ')' }}</span></a>
                            @endif
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12" id="filterSection">
                        <div class="hidden-filter bg-white redious-border p-20 p-sm-30 mb-4">
                            {{-- <form action="#" id="filterForm"> --}}
                                <div class="row gx-6 gy-3">
                                    <div class="col-3">
                                        <label class="form-label" for="phone_number">{{ __('phone_number') }}</label>
                                        <input type="text" class="form-control filterable" id="phone_number"
                                            name="phone_number" placeholder="{{__('enter') . ' ' . __('phone_number')}}">
                                    </div>
                                    <div class="col-3">
                                        <label class="form-label" for="customer_name">{{ __('customer_name') }}</label>
                                        <input type="text" class="form-control filterable" id="customer_name"
                                            name="customer_name" placeholder="{{__('enter') . '  ' . __('customer_name')}}">
                                    </div>
                                    <div class="col-3">
                                        <label class="form-label"
                                            for="customer_invoice_no">{{ __('customer_invoice_no') }}</label>
                                        <input type="text" class="form-control filterable" id="customer_invoice_no"
                                            name="customer_invoice_no"
                                            placeholder="{{__('enter') . ' ' . __('invoice_no')}}">
                                    </div>

                                    <div class="col-3">
                                        <label class="form-label" for="created_date">{{ __('created_date') }}</label>
                                        <input type="text" name="created_date" id="created_date"
                                            class="form-control date-range filterable"
                                            placeholder="YYYY-MM-DD to YYYY-MM-DD">
                                    </div>
                                    <!-- <div class="col-3">
                                                <label class="form-label" for="pickup_date">{{ __('pickup_date') }}</label>
                                                <input type="text" name="pickup_date" id="pickup_date"
                                                    class="form-control date-picker filterable" placeholder="YYYY-MM-DD"
                                                    id="pickup_date">
                                            </div>
                                            <div class="col-3">
                                                <label class="form-label" for="delivery_date">{{ __('delivery_date') }}</label>
                                                <input type="text" name="delivery_date" id="delivery_date"
                                                    class="form-control date-picker filterable" placeholder="YYYY-MM-DD"
                                                    id="delivery_date">
                                            </div>
                                            <div class="col-3">
                                                <label class="form-label" for="delivered_date">{{ __('delivered_date') }}</label>
                                                <input type="text" name="delivered_date" id="delivered_date"
                                                    class="form-control date-picker filterable" placeholder="YYYY-MM-DD"
                                                    id="delivered_date">
                                            </div> -->

                                    <div class="col-3">
                                        <label class="form-label" for="status">{{ __('status') }}</label>
                                        <select class="without_search form-select form-control form-select-sm filterable"
                                            id="status" name="status">
                                            <option value="">{{__('any_status')}}</option>
                                            @foreach(\Config::get('parcel.parcel_status') as $parcel_status)
                                                <option value="{{ $parcel_status }}" {{ (request('status') == $parcel_status || (isset($slug) && $slug == $parcel_status)) ? 'selected' : '' }}>
                                                    {{ $parcel_status == 'received' ? __('received_by_warehouse') : __($parcel_status) }}
                                                </option>
                                            @endforeach
                                            <option value="pending-return" {{ (request('status') == 'pending-return' || (isset($slug) && $slug == 'pending-return')) ? 'selected' : '' }}>{{ __('pending-return') }}</option>
                                        </select>
                                    </div>
                                    <!-- <div class="col-3">
                                                <label class="form-label" for="weight">{{ __('weight') }}</label>
                                                <select class="without_search form-select form-select-sm filterable" name="weight"
                                                    id="weight">
                                                    <option value="">
                                                        {{ __('any_weight') }}
                                                    </option>
                                                    @foreach ($charges as $charge)
                                                        <option value="{{ $charge->weight }}">
                                                            {{ $charge->weight }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div> -->
                                    <!-- <div class="col-3">
                                                <label class="form-label" for="parcel_type">{{ __('parcel_type') }}</label>
                                                <select class="without_search form-select form-select-sm filterable"
                                                    name="parcel_type" id="parcel_type">
                                                    <option value="">
                                                        {{ __('any_type') }}
                                                    </option>
                                                    <option value="same_day">
                                                        {{ __('same_day') }}
                                                    </option>
                                                    <option value="next_day">
                                                        {{ __('next_day') }}
                                                    </option>
                                                    <option value="sub_city">
                                                        {{ __('sub_city') }}
                                                    </option>
                                                    <option value="sub_urban_area">
                                                        {{ __('sub_urban_area') }}
                                                    </option>
                                                </select>
                                            </div> -->
                                    <div class="col-3">
                                        <label class="form-label" for="location">{{ __('location') }}</label>
                                        <select class="without_search form-select form-select-sm filterable" name="location"
                                            id="location">
                                            <option value="">
                                                {{ __('any_location') }}
                                            </option>
                                            @foreach ($cod_charges as $cod_charge)
                                                <option value="{{ $cod_charge->location }}">
                                                    {{ __($cod_charge->location) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-12 text-right">
                                        <div class="d-flex justify-content-end gap-2">
                                            <div class="mb-3">
                                                <button type="button" id="download"
                                                    class="btn sg-btn-primary">{{__('download')}}</button>
                                            </div>
                                            <div class="mb-3">
                                                <button type="submit" id="filter"
                                                    class="btn sg-btn-primary">{{__('filter')}}</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{--
                            </form> --}}
                        </div>
                    </div>
                </div>
                <section class="oftions">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="bg-white redious-border p-20 p-sm-30 pt-sm-30">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="default-list-table yajra-dataTable">
                                                {{ $dataTable->table(['class' => 'table'], true) }}
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
    <div class="modal fade" tabindex="-1" id="re-request-parcel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{__('re_request')}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form
                        action="{{ Sentinel::getUser()->user_type == 'merchant' ? route('merchant.parcel.re-request') : route('merchant.staff.parcel.re-request') }}"
                        method="POST" class="form-validate is-alter">
                        @csrf
                        <input type="hidden" name="id" value="" id="re-request-parcel-id">
                        <div class="mb-3">
                            <label class="form-label" for="area">{{ __('note') }} </label>
                            <textarea name="note" class="form-control">{{ old('note') }}</textarea>
                        </div>
                        <div class="text-right">
                            <button type="submit" class="btn sg-btn-primary">{{__('submit')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" id="parcel-cancel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{__('cancel_parcel')}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form
                        action="{{ Sentinel::getUser()->user_type == 'merchant' ? route('merchant.parcel-cancel') : route('merchant.staff.parcel-cancel') }}"
                        method="POST" class="form-validate is-alter">
                        @csrf
                        <input type="hidden" name="id" value="" id="cancel-parcel-id">
                        <div class="mb-3">
                            <label class="form-label" for="area">{{ __('cancel_note') }} <span
                                    class="text-danger">*</span></label>
                            <textarea name="cancel_note" class="form-control" required>{{ old('cancel_note') }}</textarea>
                        </div>
                        <div class="text-right">
                            <button type="submit" class="btn sg-btn-primary">{{__('submit')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" id="parcel-delete">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{__('delete_parcel')}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form
                        action="{{ Sentinel::getUser()->user_type == 'merchant' ? route('merchant.parcel-delete') : route('merchant.staff.parcel-delete') }}"
                        method="POST" class="form-validate is-alter">
                        @csrf
                        <input type="hidden" name="id" value="" id="delete-parcel-id">
                        <div class="mb-3">
                            <label class="form-label" for="area">{{ __('delete_note') }}</label>
                            <textarea name="cancel_note" class="form-control">{{ old('delete_note') }}</textarea>
                        </div>
                        <div class="text-right">
                            <button type="submit" class="btn sg-btn-primary resubmit">{{__('submit')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    @include('common.delete-ajax')
    @include('common.change-status-ajax')
    @include('merchant.parcel.change-parcel-status')
    <script>
        $('#filterBTN').click(function () {
            $('#filterSection').toggleClass('show');
        });

        $('#dataTableBuilder').on('preXhr.dt', function (e, settings, data) {
            $('.filterable').each(function () {
                data[$(this).attr('name')] = $(this).val();
            });
        });

        $(document).on('click', '#reset', () => {
            $('.filterable').val('').trigger('change');
            $('#dataTableBuilder').DataTable().ajax.reload();
        });

        $(document).on('click', '#filter', () => {
            $('#checkAll').prop('checked', false).trigger('change');
            $('#dataTableBuilder').DataTable().ajax.reload();
        });

        let download_url = "";
        @if (Sentinel::getUser() && Sentinel::getUser()->user_type == 'merchant_staff')
            download_url = "{{ route('merchant-staff.parcel.download') }}";
        @elseif (Sentinel::getUser() && Sentinel::getUser()->user_type == 'merchant')
            download_url = "{{ route('merchant.parcel.download') }}";
        @endif
        $(document).on('click', '#download', function () {
            const phone_number = $('#phone_number').val();
            const customer_name = $('#customer_name').val();
            const customer_invoice_no = $('#customer_invoice_no').val();
            const created_date = $('#created_date').val();
            const pickup_date = $('#pickup_date').val();
            const delivery_date = $('#delivery_date').val();
            const delivered_date = $('#delivered_date').val();
            const status = $('#status').val();
            const weight = $('#weight').val();
            const parcel_type = $('#parcel_type').val();
            const location = $('#location').val();
            const dataset = {
                customer_name: customer_name,
                phone_number: phone_number,
                customer_invoice_no: customer_invoice_no,
                created_date: created_date,
                pickup_date: pickup_date,
                delivery_date: delivery_date,
                delivered_date: delivered_date,
                status: status,
                weight: weight,
                parcel_type: parcel_type,
                location: location,
            };
            axios.post(download_url, dataset, {
                responseType: 'blob'
            })
                .then(response => {
                    const blob = new Blob([response.data], {
                        type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                    });
                    const timestamp = Date.now();
                    const filename = `parcel_list_${timestamp}.xlsx`;

                    const link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.setAttribute('download', filename);
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                })
                .catch(error => {
                    console.error('Error downloading file:', error);
                    alert('Failed to download the report. Please try again.');
                });
        });

    </script>

@endpush