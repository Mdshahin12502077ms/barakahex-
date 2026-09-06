@extends('backend.layouts.master')
@section('title', __('bags'))
@section('mainContent')
    <section class="oftions">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="header-top d-flex justify-content-between align-items-center">
                        <h3 class="section-title">{{ __('bags') }}</h3>
                        @if(hasPermission('bag_create'))
                            <div class="oftions-content-right mb-12 d-flex gap-2">
                                <a href="#" class="d-flex align-items-center btn sg-btn-primary gap-2"
                                   id="add_bag_btn"
                                   data-bs-toggle="modal" data-bs-target="#bag">
                                    <i class="las la-plus"></i>
                                    <span>{{ __('add') }} {{ __('bag') }}</span>
                                </a>
                            </div>
                        @endif
                    </div>
                    <div class="bg-white redious-border p-20 p-sm-30 pt-sm-30">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="default-list-table table-responsive yajra-dataTable">
                                    {{ $dataTable->table() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @include('common.delete-script')
    @include('admin.bag.create')
@endsection

@push('script')
    <script>
        $(document).ready(function () {
            // Reset modal on close
            $('#bag').on('hidden.bs.modal', function () {
                $('#bag form')[0].reset();
                $('#bag form').find('.is-invalid').removeClass('is-invalid');
                $('#bag form').find('.invalid-feedback').remove();
                $('#bag form').find('input[name="_method"]').remove();
                $('#bag form').attr('action', '{{ route('bag.store') }}');
                $('#bag .modal-title').text('{{ __('add_bag') }}');
                
                if ($.fn.select2) {
                    $('#bag form select').trigger('change');
                }
            });

            // Edit button logic
            $(document).on('click', '.edit_modal', function (e) {
                e.preventDefault();
                let url = $(this).attr('data-fetch_url');
                
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function (response) {
                        $('#bag .modal-content').html(response);
                        $('#bag').modal('show');
                        
                        if ($.fn.select2) {
                            $('#bag .modal-content select').select2({
                                dropdownParent: $('#bag')
                            });
                        }
                    }
                });
            });
        });
    </script>
@endpush
