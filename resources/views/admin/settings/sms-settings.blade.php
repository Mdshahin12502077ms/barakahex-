@extends('backend.layouts.master')
@section('title')
    {{ __('sms_setting') }}
@endsection

@section('mainContent')
    <section class="oftions">
        <div class="container-fluid">
            <div class="row">
                <div class="d-flex justify-content-center align-items-center">
                    <div class="col-xxl-9 col-lg-12">
                        <div class="header-top d-flex justify-content-between align-items-center mb-12">
                            <div>
                                <h3 class="section-title">{{__('sms_provider')}} {{__('lists')}}</h3>
                            </div>
                        </div>
                        <div class="bg-white redious-border p-20 p-sm-30">
                            <div class="row align-items-center g-20">
                                @include('admin.system_setting.payment_gateways.twilio')
                                @include('admin.system_setting.payment_gateways.fast_2sms')
                                @include('admin.system_setting.payment_gateways.reve_systems')
                                @include('admin.system_setting.payment_gateways.mimo')
                                @include('admin.system_setting.payment_gateways.nexmo')
                                @include('admin.system_setting.payment_gateways.ssl_wireless')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('script')
<script>
    $(document).ready(function () {
        $(document).on('click', '.sms-status-change', function () {
            var token = "{{ csrf_token() }}";
            var value = $(this).val();
            var url = $(this).data('url');
            var name = $(this).attr('name');
            var status = $(this).is(':checked') ? value : '';

            var formData = {
                name: name,
                value: status
            }
            $.ajax({
                type: 'GET',
                dataType: 'json',
                data: {
                    data: formData,
                    _token: token
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                success: function (response) {
                    if (response.error) {
                        toastr["error"](response.message);
                    } else {
                        toastr["success"](response.message);
                        location.reload();
                    }
                },
                error: function (response) {
                    // Handle HTTP errors here
                    toastr["error"](response.responseJSON.message || 'An error occurred');
                }
            });
        });
    });

</script>

@endpush


