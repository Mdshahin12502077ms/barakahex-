@extends('backend.layouts.master')
@section('title', __('district'))
@section('mainContent')
    <section class="oftions">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="header-top d-flex justify-content-between align-items-center">
                        <h3 class="section-title">{{ __('districts') }}</h3>
                        @if(hasPermission('district_create') || hasPermission('division_create') || hasPermission('country_create'))
                            <div class="oftions-content-right mb-12">
                                <a href="#" class="d-flex align-items-center btn sg-btn-primary gap-2"
                                   id="add_district_btn"
                                   data-bs-toggle="modal" data-bs-target="#district">
                                    <i class="las la-plus"></i>
                                    <span>{{ __('add') }} {{ __('district') }}</span>
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
    @include('admin.district.district')
@endsection

@push('script')
<script>
    $(document).ready(function () {
        // ১. Add District বাটনে ক্লিক করলে ফর্ম সম্পূর্ণ রিসেট হবে
        $(document).on('click', '#add_district_btn, [data-bs-target="#district"]', function () {
            resetDistrictModal();
        });

        // ২. মডাল ক্লোজ করলে ফর্ম রিসেট হবে যাতে আগের এডিট ডাটা না থাকে
        $('#district').on('hidden.bs.modal', function () {
            resetDistrictModal();
        });

        // ৩. Edit বাটনে ক্লিক করলে ফিল্ডগুলো এবং সিলেক্ট ড্রপডাউন সঠিকভাবে আপডেট হবে
        $(document).on('click', '.edit_modal[data-modal="district"]', function () {
            let modal = $('#district');
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
                    modal.find('select[name="division_id"]').val(response.division_id).trigger('change');
                }
            });
        });

        function resetDistrictModal() {
            let modal = $('#district');
            let form = modal.find('form');
            if (form.length) {
                form[0].reset();
                form.attr('action', "{{ route('districts.store') }}");
            }
            modal.find('.create_sub_title').removeClass('d-none');
            modal.find('.edit_sub_title').addClass('d-none');
            modal.find('#districtName').val('');
            modal.find('#division_id').val('').trigger('change');
            modal.find('p.error').text('');
        }
    });
</script>
@endpush
