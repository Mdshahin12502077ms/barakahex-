@extends('backend.layouts.master')
@section('title', __('areas'))
@section('mainContent')
    <section class="oftions">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="header-top d-flex justify-content-between align-items-center">
                        <h3 class="section-title">{{ __('areas') }}</h3>
                        @if(hasPermission('area_create') || hasPermission('delivery_zone_create') || hasPermission('upazila_create') || hasPermission('district_create') || hasPermission('division_create') || hasPermission('country_create'))
                            <div class="oftions-content-right mb-12 d-flex gap-2">
                                <a href="#" class="d-flex align-items-center btn sg-btn-outline-primary gap-2"
                                   data-bs-toggle="modal" data-bs-target="#import_area_modal">
                                    <i class="las la-file-excel"></i>
                                    <span>{{ __('import') }} {{ __('areas') }}</span>
                                </a>
                                <a href="#" class="d-flex align-items-center btn sg-btn-primary gap-2"
                                   id="add_area_btn"
                                   data-bs-toggle="modal" data-bs-target="#area">
                                    <i class="las la-plus"></i>
                                    <span>{{ __('add') }} {{ __('area') }}</span>
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
    @include('admin.area.area')
    @include('admin.area.import')
@endsection

@push('script')
<script>
    $(document).ready(function () {
        // ১. Add Area বাটনে ক্লিক করলে ফর্ম সম্পূর্ণ রিসেট হবে
        $(document).on('click', '#add_area_btn, [data-bs-target="#area"]', function () {
            resetAreaModal();
        });

        // ২. মডাল ক্লোজ করলে ফর্ম রিসেট হবে যাতে আগের এডিট ডাটা না থাকে
        $('#area').on('hidden.bs.modal', function () {
            resetAreaModal();
        });

        // ৩. Edit বাটনে ক্লিক করলে ফিল্ডগুলো এবং সিলেক্ট ড্রপডাউন সঠিকভাবে আপডেট হবে
        $(document).on('click', '.edit_modal[data-modal="area"]', function () {
            let modal = $('#area');
            let fetch_url = $(this).attr('data-fetch_url');
            let route = $(this).attr('data-route');

            modal.find('.create_sub_title').addClass('d-none');
            modal.find('.edit_sub_title').removeClass('d-none');
            modal.find('form').attr('action', route);
            modal.find('p.error').text('');

            $.ajax({
                type: "GET",
                url: fetch_url,
                success: function (response) {
                    modal.find('input[name="name"]').val(response.name);
                    modal.find('select[name="delivery_zone_id"]').val(response.delivery_zone_id).trigger('change');
                }
            });
        });

        function resetAreaModal() {
            let modal = $('#area');
            let form = modal.find('form');
            if (form.length) {
                form[0].reset();
                form.attr('action', "{{ route('areas.store') }}");
            }
            modal.find('.create_sub_title').removeClass('d-none');
            modal.find('.edit_sub_title').addClass('d-none');
            modal.find('#areaName').val('');
            modal.find('#delivery_zone_id').val('').trigger('change');
            modal.find('p.error').text('');
        }
    });
</script>
@endpush
