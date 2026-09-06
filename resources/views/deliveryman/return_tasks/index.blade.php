@extends('backend.layouts.master')

@section('title')
    {{ __('return_tasks') }}
@endsection

@section('mainContent')
<div class="container-fluid">
    {{-- Header Section --}}
    <div class="d-flex justify-content-between align-items-center mb-20 flex-wrap gap-2">
        <div>
            <h4 class="section-title mb-1">{{ __('return_tasks') }}</h4>
            <p class="text-muted small mb-0">{{ __('manage_and_handover_assigned_return_parcels_to_merchants') }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('deliveryman.dashboard') }}" class="btn btn-sm sg-btn-outline-primary">
                <i class="las la-arrow-left"></i> {{ __('back_to_dashboard') }}
            </a>
        </div>
    </div>

    {{-- DataTable Card --}}
    <div class="bg-white redious-border p-20 p-sm-30">
        <div class="row">
            <div class="col-lg-12">
                <div class="default-list-table yajra-dataTable">
                    {{ $dataTable->table(['class' => 'table table-bordered table-striped align-middle mb-0 w-100'], true) }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    $(document).on('submit', '.confirm-form', function(e) {
        e.preventDefault();
        var form = this;
        var title = $(form).data('title') || "{{ __('are_you_sure') }}";
        var text = $(form).data('text') || "{{ __('you_won_t_be_able_to_revert_this') }}";
        var confirmBtnText = $(form).data('confirm-btn') || "{{ __('yes_i_m') }}";
        var cancelBtnText = $(form).data('cancel-btn') || "{{ __('cancel') }}";

        Swal.fire({
            title: title,
            text: text,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: confirmBtnText,
            cancelButtonText: cancelBtnText
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
</script>
@endpush
