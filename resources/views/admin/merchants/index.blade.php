@extends('backend.layouts.master')

@section('title')
{{__('merchant').' '.__('lists')}}
@endsection

@section('mainContent')
<div class="container-fluid">
    <div class="row gx-20">
        <div class="col-lg-12">
            <div class="header-top d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="section-title">{{__('merchant')}} {{__('lists')}}</h3>
                    <p class="mb-1">{{__('you_have_total')}} {{ $merchants->total() }} {{__('merchants')}}.</p>
                </div>
                <div class="oftions-content-right mb-12">
                    <a href="#" class="d-flex align-items-center btn sg-btn-primary gap-2" id="filterBTN">
                        <i class="las la-filter"></i>
                    </a>
                    @if(hasPermission('merchant_create'))
                    <a href="{{ route('merchant.create') }}"
                        class="d-flex align-items-center btn sg-btn-primary gap-2">
                        <i class="las la-plus"></i>
                        <span>{{__('add_merchant') }}</span>
                    </a>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12" id="filterSection">
                    <div class="hidden-filter bg-white redious-border p-20 p-sm-30 mb-4">
                        <form action="{{route('merchant')}}" id="filterForm">
                            <div class="row ">
                                <div class="col-sm-12 col-md-4 col-lg-3 ">
                                    <div class="col-custom">
                                        <div class="select-type-v2">
                                            <label for="instructor_ids" class="form-label">{{ __('company_name') }}</label>
                                            <div class="mb-3">
                                                <input type="text" class="form-control" name="company_name" value="{{request()->get('company_name')}}" placeholder="{{__('enter_company_name')}}">
                                            </div>
                                            <div class="invalid-feedback help-block">
                                                <p class="error">{{ $errors->first('instructor_ids') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-4 col-lg-3 ">
                                    <div class="col-custom">
                                        <div class="multi-select-v2">
                                            <label for="status"
                                                class="form-label">{{ __('status') }}</label>
                                            <select class="without_search" name="status" id="status">
                                                <option value="">{{__('any_status')}}</option>
                                                <option value="active">{{__('active')}}</option>
                                                <option value="inactive">{{__('inactive')}}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-4 col-lg-3 ">
                                    <div class="col-custom">
                                        <div class="multi-select-v2">
                                            <label for="approval_status form-control"
                                                class="form-label">{{ __('approval_status') }}</label>
                                            <select class="without_search" name="approval_status" id="approval_status">
                                                <option value="">{{__('any_approval_status')}}</option>
                                                <option value="1">{{__('approved')}}</option>
                                                <option value="0">{{__('pending')}}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-4 col-lg-3 ">
                                    <div class="col-custom">
                                        <div class="select-type-v2">
                                            <label for="sort_by" class="form-label">{{ __('shorted') }}</label>
                                            <select class="without_search" name="sort_by" id="sort_by">
                                                <option value="">{{__('sort_by')}}</option>
                                                <option value="rank">{{__('rank')}}</option>
                                                <option value="oldest_on_top">{{__('oldest_on_top')}}</option>
                                                <option value="newest_on_top">{{__('newest_on_top')}}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-4 col-lg-3 ">
                                    <div class="col-custom">
                                        <div class="select-type-v2">
                                            <label for="sort_by" class="form-label">{{ __('branch') }}</label>
                                            <select class="without_search" name="branch" id="branch">
                                                <option value="">{{__('all').' '.__('branch')}}</option>
                                                <option value="pending">{{__('pending')}}</option>
                                                @if(hasPermission('read_all_merchant'))
                                                @foreach($branchs as $branch)
                                                <option value="{{ $branch->id }}" {{request()->get('branch')==$branch->id ? 'selected':''}}>{{ $branch->name.' ('.$branch->address.')' }}</option>
                                                @endforeach
                                                @else
                                                @if(!blank(\Sentinel::getUser()->branch) )
                                                <option value="{{ \Sentinel::getUser()->branch_id }}" {{request()->get('branch')==\Sentinel::getUser()->branch_id ? 'selected':''}}>{{ \Sentinel::getUser()->branch->name.' ('.\Sentinel::getUser()->branch->address.')' }}</option>
                                                @endif
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-4 col-lg-3 mt-1 pt-4 ">
                                    <div class="">
                                        <button type="submit" id="filter" class=" btn sg-btn-primary" style="height:40px;">{{ __('filter') }}</button>
                                    </div>
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
@include('common.delete-ajax')
@include('common.change-status-ajax')
@push('script')
<script>
    $(document).ready(function() {
        searchCategory($('#select_category'));
        searchOrganization($('#ins_by_org'));

        $('#filterBTN').click(function() {
            $('#filterSection').toggleClass('show');
        });
    });

    $(document).ready(function() {
        const advancedSearchMapping = (attribute) => {

            $('#dataTableBuilder').on('preXhr.dt', function(e, settings, data) {
                data[attribute.name] = attribute.value;
            });
        }

        $(document).on('change', '#filterForm input', function() {
            advancedSearchMapping({
                name: $(this).attr('name'),
                value: $(this).val(),
            });
        });

        $(document).on('change', '#filterForm select', function() {
            advancedSearchMapping({
                name: $(this).attr('name'),
                value: $(this).val(),
            });
        });

        $(document).on('click', '#filter', function(event) {
            event.preventDefault();
            $('#dataTableBuilder').DataTable().ajax.reload();
        });
    });
    const refreshDataTable = () => {
        $('#dataTableBuilder').DataTable().ajax.reload();
    }
</script>
@endpush