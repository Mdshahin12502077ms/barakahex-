@extends('backend.layouts.master')
@section('title', __('cron_job_setting'))
@section('mainContent')
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col col-lg-8 col-md-9">
                <div class="bg-white redious-border pt-30 p-20 p-sm-30">
                    <div class="section-top">
                        <h6>{{ __('cron_job_setting') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <div class="mb-4">
                                <p>{{ __('cron_setup_instruction_line1') }}</p>
                                <p>{{ __('cron_setup_instruction_line2') }}</p>
                            </div>
                        </div>
                        <label for="cron_command" class="form-label">{{ __('cron_command') }}</label>
                        <div class="input-group">
                            <input type="text"
                                   value="* * * * * cd /D:/xampp/htdocs/PickFast-Laravel && php artisan schedule:run >> /dev/null 2>&1"
                                   readonly
                                   class="form-control"
                                   placeholder="{{ __('cron_command') }}"
                                   aria-label="{{ __('cron_command') }}"
                                   aria-describedby="cron_command">
                            <span class="input-group-text copy-text" id="cron_command"><i class="la la-copy"></i></span>
                        </div>
                        <div class="text-center">
                            <a href="{{ route('cron.run.manually') }}" class="btn btn-sm sg-btn-primary gap-2 mt-20 mb-20">
                                <span>{{ __('run_cron_manually') }}</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script>
        $(document).ready(function() {
            $('.copy-text').click(function() {
                var inputField = $(this).closest('.input-group').find('input');
                inputField.select();
                document.execCommand("copy");
                toastr.success("{{ __('copied') }}");
            });
        });
    </script>
@endpush
