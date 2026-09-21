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
<!-- Return OTP Modal -->
<div class="modal fade" id="returnOtpModal" tabindex="-1" aria-labelledby="returnOtpModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="returnOtpModalLabel">{{ __('return_otp_verification') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <i class="las la-shield-alt text-primary" style="font-size: 3rem;"></i>
                    <h6 class="mt-2">{{ __('verify_return_parcel') }} <span id="modal-return-parcel-no" class="text-danger fw-bold"></span></h6>
                    <p class="text-muted small">{{ __('merchant_must_provide_otp_to_confirm_return') }}</p>
                </div>
                
                <div id="return-otp-expired-warning" class="alert alert-danger d-none" role="alert">
                    <i class="las la-exclamation-circle"></i> {{ __('otp_expired_warning') }} <span id="return-otp-expired-time"></span>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <form action="" method="POST" id="resendReturnOtpForm" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-primary" id="btnResendReturnOtp">
                            <i class="las la-paper-plane"></i> {{ __('send_resend_otp') }}
                        </button>
                    </form>
                    <span class="badge bg-warning text-dark" id="return-otp-attempts-badge" style="display:none;"></span>
                </div>

                <form action="" method="POST" id="verifyReturnOtpForm">
                    @csrf
                    <div class="form-group mb-3">
                        <label for="return_otp" class="form-label fw-bold">{{ __('enter_otp') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-lg text-center fw-bold" id="return_otp" name="otp" required placeholder="----" maxlength="4" style="letter-spacing: 10px; font-size: 1.5rem;">
                    </div>
                    <button type="submit" class="btn btn-success btn-lg w-100"><i class="las la-check-circle"></i> {{ __('verify_and_return') }}</button>
                </form>
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

    $(document).on('click', '.return-otp-btn', function() {
        var id = $(this).data('id');
        var parcelNo = $(this).data('parcel-no');
        var attempts = $(this).data('otp-attempts');
        var expired = $(this).data('otp-expired');
        var expiredAt = $(this).data('otp-expired-at');
        
        $('#modal-return-parcel-no').text(parcelNo);
        
        // Update form actions
        var resendUrl = "{{ url('delivery-man/return-task-resend-otp') }}/" + id;
        var verifyUrl = "{{ url('delivery-man/return-task-verify-otp') }}/" + id;
        
        $('#resendReturnOtpForm').attr('action', resendUrl);
        $('#verifyReturnOtpForm').attr('action', verifyUrl);
        $('#return_otp').val('');
        
        // Handle expiration warning
        if(expired == '1'){
            $('#return-otp-expired-warning').removeClass('d-none');
            $('#return-otp-expired-time').text(expiredAt);
        } else {
            $('#return-otp-expired-warning').addClass('d-none');
        }

        // Handle attempts badge
        if(attempts > 0){
            var remaining = 3 - parseInt(attempts);
            $('#return-otp-attempts-badge').text("Wrong Attempts: " + attempts + " (Remaining: " + remaining + ")").show();
            if(remaining <= 0) {
                $('#return-otp-attempts-badge').removeClass('bg-warning').addClass('bg-danger text-white');
                $('#verifyReturnOtpForm button[type="submit"]').prop('disabled', true);
            } else {
                $('#return-otp-attempts-badge').removeClass('bg-danger text-white').addClass('bg-warning text-dark');
                $('#verifyReturnOtpForm button[type="submit"]').prop('disabled', false);
            }
        } else {
            $('#return-otp-attempts-badge').hide();
            $('#verifyReturnOtpForm button[type="submit"]').prop('disabled', false);
        }

        var myModal = new bootstrap.Modal(document.getElementById('returnOtpModal'));
        myModal.show();
    });
</script>
@endpush
