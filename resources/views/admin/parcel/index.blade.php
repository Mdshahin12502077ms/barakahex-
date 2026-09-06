@extends('backend.layouts.master')
@section('parcel', 'active')
@section('title')
    {{$title}}
@endsection
@section('mainContent')
    <div class="container-fluid">
        <div class="row gx-20">
            <div class="col-lg-12">
                <div class="header-top d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="section-title">{{$title}} {{__('lists')}}</h3>
                        <p class="mb-1">{{__('you_have_total')}} {{ $countData }} {{__('parcels')}}.</p>
                    </div>
                    <div class="oftions-content-right mb-12 filterOPT">
                        <div class="d-none d-flex gap-2" id="actionDropdown">

                            <div class="form-check btn btn-sm sg-btn-primary ps-2">
                                <input class="form-check-input ms-1" type="checkbox" id="select_all"
                                    onclick="selectAll(event)">
                                <label class="form-check-label ps-2" for="select_all">
                                    {{ __('select_all') }}
                                </label>
                            </div>

                            <div class="dropdown">
                                <button class="btn btn-sm sg-btn-primary" type="button" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    <i class="las la-angle-down" style="font-size: 12px;"></i> {{ __('bulk_action') }}
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="javascript:void(0)" class="dropdown-item" id="bulkExportBtn"
                                            onclick="selectedParcelExport()">
                                            {{ __('bulk_export') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0)" class="dropdown-item" id="batchPrintBtn"
                                            onclick="selectedParcelBatchPrint()">
                                            {{ __('bulk_batch_print') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0)" class="dropdown-item" id="received-by-pickupman"
                                            onclick="selectedParcelReceivedByPickupman()">
                                            {{ __('bulk_received_by_pickupman') }}
                                        </a>
                                    </li>

                                    <li>
                                        <a href="javascript:void(0)" class="dropdown-item" id="receive-parcel"
                                            onclick="selectedParcelReceive()">
                                            {{ __('bulk_receive_warehouse') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0)" class="dropdown-item" id="assign-delivery-man"
                                            onclick="selectedParcelShip()">
                                            {{ __('bulk_assign_deliveryman') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0)" class="dropdown-item" id="parcel-delivered"
                                            onclick="selectedBulkDelivered()">
                                            {{ __('bulk_delivered') }}
                                        </a>
                                    </li>

                                    <li>
                                        <a href="javascript:void(0)" class="dropdown-item" id="return-to-warehouse"
                                            onclick="selectedBulkReturnToWarehouse()">
                                            {{ __('bulk_return_to_warehouse') }}
                                        </a>
                                    </li>

                                    <li>
                                        <a href="javascript:void(0)" class="dropdown-item" id="return-assign-to-merchent"
                                            onclick="selectedBulkReturnAssignToMerchant()">
                                            {{ __('bulk_return_assign_to_merchant') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0)" class="dropdown-item" id="return-to-merchent"
                                            onclick="selectedBulkReturnToMerchant()">
                                            {{ __('bulk_return_to_merchant') }}
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <a href="#" class="d-flex align-items-center btn btn-sm sg-btn-primary" id="filterBTN">
                            <i class="las la-filter" style="font-size: 12px;"></i>
                        </a>
                        @if(hasPermission('parcel_create'))
                            @if(@settingHelper('preferences')->where('title', 'create_parcel')->first()->staff)
                                <a href="{{route('import.csv')}}"
                                    class="d-flex align-items-center btn btn-sm sg-btn-primary gap-2"><i
                                        class="icon las la-plus"></i><span>{{__('import')}}</span></a>
                            @endif
                        @endif
                        @if(hasPermission('parcel_transfer_to_branch'))
                            <a href="{{route('bulk.transfer')}}"
                                class="d-flex align-items-center btn btn-sm sg-btn-primary gap-2"><i
                                    class="icon las la-plus"></i><span>{{__('branch_transfer')}}</span></a>
                        @endif
                        @if(hasPermission('parcel_transfer_receive_to_branch'))
                            <a href="{{route('bulk.transfer.receive')}}"
                                class="d-flex btn-sm align-items-center btn sg-btn-primary gap-2"><i
                                    class="icon las la-plus"></i><span>{{__('transfer_receive')}}</span></a>
                        @endif
                        @if(hasPermission('parcel_pickup_assigned'))
                            <a href="{{route('bulk.pickup.assign')}}"
                                class="d-flex align-items-center btn btn-sm sg-btn-primary gap-2"><i
                                    class="icon las la-plus"></i><span>{{__('assign_pickup')}}</span></a>
                        @endif
                        @if(hasPermission('parcel_delivery_assigned'))
                            <a href="{{route('bulk.assigning')}}"
                                class="d-flex align-items-center btn btn-sm sg-btn-primary gap-2"><i
                                    class="icon las la-plus"></i><span>{{__('assign_delivery')}}</span></a>
                        @endif
                        @if(hasPermission('parcel_create'))
                            @if(@settingHelper('preferences')->where('title', 'create_parcel')->first()->staff)
                                <a href="{{route('parcel.create')}}"
                                    class="d-flex align-items-center btn btn-sm sg-btn-primary gap-2"><i
                                        class="icon las la-plus"></i><span>{{__('create') . ' ' . __('parcel')}}</span></a>
                            @else
                                <button class="d-flex align-items-center btn btn-sm sg-btn-primary gap-2"><i
                                        class="icon las la-plus"></i><span>{{__('create') . ' ' . __('parcel') . ' (' . __('service_unavailable') . ')'}}</span></button>
                            @endif
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12" id="filterSection">
                        <div class="hidden-filter bg-white redious-border p-20 p-sm-30 mb-4">
                            <div class="row gx-6 gy-3">
                                <div class="col-3">
                                    <label class="form-label" for="merchant_id">{{ __('merchant') }}</label>
                                    <select id="merchant_id" name="merchant_id"
                                        class="form-control select-merchant merchant merchant-live-search filterable"
                                        data-url="{{ route('get-merchant-live') }}">
                                        <option value="">{{ __('select_merchant') }}</option>
                                    </select>
                                </div>
                                <div class="col-3">
                                    <label class="form-label" for="phone_number">{{ __('phone_number') }}</label>
                                    <input type="text" class="form-control filterable" id="phone_number" name="phone_number"
                                        placeholder="{{__('enter') . ' ' . __('customer') . ' ' . __('phone_number')}}">
                                </div>
                                <div class="col-3">
                                    <label class="form-label"
                                        for="customer_invoice_no">{{ __('customer_invoice_no') }}</label>
                                    <input type="text" class="form-control filterable" id="customer_invoice_no"
                                        name="customer_invoice_no" placeholder="{{__('enter') . ' ' . __('invoice_no')}}">
                                </div>
                                <div class="col-3">
                                    <label class="form-label" for="created_at">{{ __('created_at') }}</label>
                                    <input type="text" name="created_at" id="created_at"
                                        class="form-control date-range filterable" placeholder="YYYY-MM-DD to YYYY-MM-DD"
                                        id="created_from">
                                </div>
                                <div class="col-3">
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
                                </div>
                                <div class="col-3">
                                    <label class="form-label" for="returned_date">{{ __('returned_date') }}</label>
                                    <input type="text" name="returned_date" id="returned_date"
                                        class="form-control date-picker filterable" placeholder="YYYY-MM-DD"
                                        id="returned_date">
                                </div>
                                <div class="col-3">
                                    <label class="form-label" for="pickup_man_id">{{ __('pickup_man') }}</label>
                                    <select name="pickup_man_id" id="pickup_man_id" class="form-control filterable"
                                        data-url="{{ route('get-delivery-man-live') }}">
                                        <option value="">{{__('pickup_man')}}</option>
                                    </select>
                                </div>
                                <div class="col-3">
                                    <label class="form-label" for="delivery_man_id">{{ __('delivery_man') }}</label>
                                    <select name="delivery_man_id" id="delivery_man_id" class="form-control  filterable"
                                        data-url="{{ route('get-delivery-man-live') }}">
                                        <option value="">{{__('delivery_man')}}</option>
                                    </select>
                                </div>
                                <div class="col-3">
                                    <label class="form-label" for="third_party">{{ __('third_party') }}</label>
                                    <select name="third_party_id" id="third_party"
                                        class="without_search form-select form-control form-select-sm filterable">
                                        <option value="">{{__('third_party')}}</option>
                                        @if (!empty($third_parties) && count($third_parties) > 0)
                                            @foreach($third_parties as $third_party)
                                                <option value="{{ $third_party->id }}">
                                                    {{ $third_party->name . ' (' . $third_party->address . ')' }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="col-3">
                                    <label class="form-label" for="status">{{ __('status') }}</label>
                                    <select class="without_search form-select form-control form-select-sm filterable"
                                        id="status" name="status">
                                        <option value="">{{__('any_status')}}</option>
                                        @foreach(\Config::get('parcel.parcel_status') as $parcel_status)
                                            <option value="{{ $parcel_status }}">
                                                {{ $parcel_status == 'received' ? __('received_by_warehouse') : __($parcel_status) }}
                                            </option>
                                        @endforeach
                                        <option value="pending-return">{{ __('pending-return') }}</option>
                                    </select>
                                </div>
                                <div class="col-3">
                                    <label class="" for="weight">{{ __('weight') }}</label>
                                    <select class="without_search form-select form-control form-select-sm filterable"
                                        id="any_weight" name="weight">
                                        <option value="">{{__('any_weight')}}</option>
                                        @if (!empty($charges) && count($charges) > 0)
                                            @foreach($charges as $charge)
                                                <option value="{{ $charge->weight }}">{{ $charge->weight }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="col-3">
                                    <label class="form-label" for="parcel_type">{{ __('parcel_type') }}</label>
                                    <select class="without_search form-select form-control form-select-sm filterable"
                                        id="parcel_type" name="parcel_type">
                                        <option value="">{{__('any_type')}}</option>
                                        <option value="same_day">{{ __('same_day') }}</option>
                                        <!-- <option value="next_day">{{ __('next_day') }}</option> -->
                                        <option value="sub_city">{{ __('sub_city') }}</option>
                                        <option value="sub_urban_area">{{ __('sub_urban_area') }}</option>
                                    </select>
                                </div>
                                <div class="col-3">
                                    <label class="form-label" for="location">{{ __('location') }}</label>
                                    <select class="without_search form-select form-control form-select-sm filterable"
                                        id="location" name="location">
                                        <option value="">{{__('any_location')}}</option>
                                        @if (!empty($cod_charges) && count($cod_charges) > 0)
                                            @foreach($cod_charges as $cod_charge)
                                                <option value="{{ $cod_charge->location }}">{{ __($cod_charge->location) }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                                <div class="col-3">
                                    <label class="form-label" for="branch_id">{{ __('branch') }}</label>
                                    <select class="without_search form-select form-control form-select-sm filterable"
                                        id="branch_id" name="branch_id">
                                        <option value="">{{__('all') . ' ' . __('branch')}}</option>
                                        @if(hasPermission('read_all_parcel'))
                                            @if (!empty($branchs) && count($branchs) > 0)
                                                @foreach($branchs as $branch)
                                                    <option value="{{ $branch->id }}">
                                                        {{ $branch->name . ' (' . $branch->address . ')' }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        @else
                                            @if(!blank(\Sentinel::getUser()->branch))
                                                <option value="{{ \Sentinel::getUser()->branch_id }}">
                                                    {{ \Sentinel::getUser()->branch->name . ' (' . \Sentinel::getUser()->branch->address . ')' }}
                                                </option>
                                            @endif
                                        @endif
                                    </select>
                                </div>

                                <div class="col-3">
                                    <label class="form-label" for="pickup_branch_id">{{ __('pickup_branch') }}</label>
                                    <select class="without_search form-select form-control form-select-sm filterable"
                                        id="pickup_branch_id" name="pickup_branch_id">
                                        <option value="">{{__('all') . ' ' . __('pickup_branch')}}</option>
                                        @if(hasPermission('read_all_parcel'))
                                            @if (!empty($branchs) && count($branchs) > 0)
                                                @foreach($branchs as $branch)
                                                    <option value="{{ $branch->id }}">
                                                        {{ $branch->name . ' (' . $branch->address . ')' }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        @else
                                            @if(!blank(\Sentinel::getUser()->branch))
                                                <option value="{{ \Sentinel::getUser()->branch_id }}">
                                                    {{ \Sentinel::getUser()->branch->name . ' (' . \Sentinel::getUser()->branch->address . ')' }}
                                                </option>
                                            @endif
                                        @endif
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
                                            <div class="default-list-table table-responsive yajra-dataTable ">
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
    @include('admin.parcel.modal.assign-delivery')
    @include('admin.parcel.modal.assign-pickup')
    @include('admin.parcel.modal.delivery-reverse')
    @include('admin.parcel.modal.parcel-cancel')
    @include('admin.parcel.modal.parcel-delete')
    @include('admin.parcel.modal.parcel-delivered-partially')
    @include('admin.parcel.modal.parcel-delivered')
    @include('admin.parcel.modal.parcel-receive-by-pickupman')
    @include('admin.parcel.modal.parcel-receive')
    @include('admin.parcel.modal.parcel-reverse-from-cancel')
    @include('admin.parcel.modal.parcel-transfer-receive-to-branch')
    @include('admin.parcel.modal.parcel-transfer-to-branch')
    @include('admin.parcel.modal.re-schedule-delivery')
    @include('admin.parcel.modal.re-schedule-pickup')
    @include('admin.parcel.modal.return-assign-tomerchant')
    @include('admin.parcel.modal.return-delivery')
    @include('admin.parcel.modal.returned-to-merchant')
@endsection
@include('live_search.merchants')
@push('script')
    @include('common.delete-ajax')
    @include('common.change-status-ajax')
    @include('admin.parcel.change-parcel-status')
    @include('admin.parcel.reverse-script')
    <script>
        const download_url = "{{ route('admin.parcel.download') }}";
    </script>
    <script src="{{ static_asset('admin/js/parcel/index.js') }}"></script>
@endpush