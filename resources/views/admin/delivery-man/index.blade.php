
@extends('backend.layouts.master')

@section('title')
    {{__('delivery_man').' '.__('lists')}}
@endsection
<style>
    .form-select{
        border-radius: 5px;
        height:40px;
        width:100%;
    }
</style>
@section('mainContent')
    <div class="container-fluid">
        <div class="row gx-20">
            <div class="col-lg-12">
                <div class="header-top d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="section-title">{{__('delivery_man')}} {{__('lists')}}</h3>
                        <p>{{__('you_have_total')}} {{ !blank($delivery_men)? $delivery_men->total():'0' }} {{__('delivery_man')}}.</p>
                    </div>
                    <div class="oftions-content-right mb-12">
                        <a href="#" class="d-flex align-items-center btn sg-btn-primary gap-2" id="filterBTN">
                            <i class="las la-filter"></i>
                        </a>
                        @if(hasPermission('deliveryman_create'))
                            <a href="{{route('delivery.man.create')}}"
                            class="d-flex align-items-center btn sg-btn-primary gap-2">
                             <i class="las la-plus"></i>
                             <span>{{__('add_delivery_man') }}</span>
                         </a>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12" id="filterSection">
                        <div class="hidden-filter bg-white redious-border p-20 p-sm-30 mb-4">
                            <form action="{{route('delivery.man')}}" id="filterForm">
                                <div class="row ">
                                    <div class="col-sm-12 col-md-4 col-lg-3 ">
                                        <div class="col-custom">
                                            <div class="select-type-v2">
                                                <label for="instructor_ids" class="form-label">{{ __('name') }}</label>
                                                <div class="mb-3">
                                                    <input type="text" class="form-control" name="name" id="name" placeholder="{{__('enter_name')}}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-4 col-lg-3 ">
                                        <div class="col-custom">
                                            <div class="select-type-v2">
                                                <label for="instructor_ids" class="form-label">{{ __('email') }}</label>
                                                <div class="mb-3">
                                                    <input type="text" class="form-control" name="email" id="email" placeholder="{{__('enter_email')}}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-4 col-lg-3 ">
                                        <div class="col-custom">
                                            <div class="multi-select-v2">
                                                <label for="status"
                                                    class="form-label">{{ __('status') }}</label>
                                                <select class="without_search form-select form-select-sm form-control" name="status" id="status">
                                                    <option value="">{{__('any_status')}}</option>
                                                    <option value="active">{{__('active')}}</option>
                                                    <option value="inactive">{{__('inactive')}}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-4 col-lg-3 ">
                                        <div class="col-custom">
                                            <div class="select-type-v2">
                                                <label for="sort_by" class="form-label">{{ __('branch') }}</label>
                                                <select class="without_search form-select form-select-sm form-control" name="branch" id="branch">
                                                    <option value="">{{__('all').' '.__('branch')}}</option>
                                                    <option value="pending">{{__('pending')}}</option>
                                                        @if(hasPermission('read_all_delivery_man'))
                                                            @foreach($branchs as $branch)
                                                                <option value="{{ $branch->id }}">{{ $branch->name.' ('.$branch->address.')' }}</option>
                                                            @endforeach
                                                        @else
                                                            @if(!blank(\Sentinel::getUser()->branch) )
                                                                <option value="{{ \Sentinel::getUser()->branch_id }}">{{ \Sentinel::getUser()->branch->name.' ('.\Sentinel::getUser()->branch->address.')' }}</option>
                                                            @endif
                                                        @endif
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <div class="">
                                            <button type="submit" id="filter" class="btn sg-btn-primary" style="height:40px;">{{ __('filter') }}</button>
                                    </div>
                                </div>
                            </form>
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
@endsection
@push('script')
@include('common.delete-ajax')
@include('common.change-status-ajax')
@endpush
@push('script')
    <script>
        $(document).ready(function () {

            $('#filterBTN').click(function() {
                $('#filterSection').toggleClass('show');
            });
        });


        $(document).ready(function () {
            const advancedSearchMapping = (attribute) => {
                $('#dataTableBuilder').on('preXhr.dt', function (e, settings, data) {
                    data[attribute.name] = attribute.value;
                });
            }

            $(document).on('change', '#filterForm input', function () {
                advancedSearchMapping({
                    name: $(this).attr('name'),
                    value: $(this).val(),
                });
            });

            $(document).on('change', '#filterForm select', function () {
                advancedSearchMapping({
                    name: $(this).attr('name'),
                    value: $(this).val(),
                });
            });

            $(document).on('click', '#filter', function (event) {
                event.preventDefault();
                $('#dataTableBuilder').DataTable().ajax.reload();
            });
        });
        const refreshDataTable = () => {
            $('#dataTableBuilder').DataTable().ajax.reload();
        }

    </script>
@endpush

