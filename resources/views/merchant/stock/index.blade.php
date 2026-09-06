@extends('backend.layouts.master')
@section('title')
    {{ __('stock') . ' ' . __('list') }}
@endsection
@section('mainContent')
    <div class="container-fluid">
        <div class="row gx-20">
            <div class="col-lg-12">
                <div class="header-top d-flex justify-content-between align-items-center">
                    <h3 class="section-title">
                        {{ menuActivation(['merchant/stock/stock_in'], __('stock_list')) }}
                        {{ menuActivation(['merchant/stock/history'], __('stock_history')) }}</h3>
                    <div class="oftions-content-right pb-12">
                        <a href="#" data-bs-toggle="modal" data-bs-target="#add_stock"
                           class="d-flex align-items-center btn sg-btn-primary gap-2"><i
                                class="icon la la-plus"></i><span>{{ __('add_stock') }}</span></a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="bg-white redious-border p-20 p-sm-30 pt-sm-30">
                            <div class="default-list-table table-responsive yajra-dataTable">
                                {{ $dataTable->table(['class' => 'dt-responsive table'], true) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" id="add_stock">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('add_stock') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form
                        action="{{ route('merchant.stock.store') }}"
                        method="POST" class="form" id="add-shop-form">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label" for="product">{{ __('product_name') }} <span
                                    class="text-danger">*</span></label>
                            <select class="with_search form-control" name="product">
                                <option value="">{{ __('select_product') }}</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}">
                                        {{ $product->name }}</option>
                                @endforeach
                            </select>
                            <div class="nk-block-des text-danger">
                                <p class="product_error error"></p>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="warehouse">{{ __('warehouse') }} <span
                                    class="text-danger">*</span></label>
                            <select class="with_search form-control" name="warehouse">
                                <option value="">{{ __('select_product') }}</option>
                                @foreach ($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}">
                                        {{ $warehouse->name }}</option>
                                @endforeach
                            </select>
                            <div class="nk-block-des text-danger">
                                <p class="warehouse_error error"></p>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="quantity">{{ __('quantity') }} <span
                                    class="text-danger">*</span></label>
                            <input type="number" name="quantity" class="form-control" value="{{ old('quantity') }}"
                                   id="quantity">
                            <div class="nk-block-des text-danger">
                                <p class="quantity_error error"></p>
                            </div>
                        </div>
                        <div class="col-md-12  input_file_div">
                            <div class="mb-3 mt-2">
                                <label class="form-label mb-1">{{ __('file') }}</label>
                                <input class="form-control sp_file_input file_picker" type="file" id="profilePhoto"
                                       name="image" accept="image/*">
                            </div>
                            <div class="selected-files d-flex flex-wrap gap-20">
                                <div class="selected-files-item">
                                    <img class="selected-img"
                                         src="{{ getFileLink('80X80', []) }}"
                                         alt="favicon">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3 text-right mt-3">
                            <button type="submit" class="btn sg-btn-primary resubmit">{{ __('submit') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@include('merchant.delete-ajax')

