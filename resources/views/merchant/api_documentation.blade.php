@extends('backend.layouts.master')
@section('title', __('API Documentation'))

@section('mainContent')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="bg-white redious-border p-20 p-sm-30 mb-4">
                    <div class="header-top d-flex flex-wrap justify-content-between align-items-center mb-24 pb-16 border-bottom">
                        <div>
                            <h4 class="section-title mb-1">
                                <i class="las la-file-code text-primary me-2"></i>{{ __('API Documentation') }}
                            </h4>
                            <p class="text-muted font-13 mb-0">
                                {{ __('Barakahex Courier API integration guide & specifications for merchants.') }}
                            </p>
                        </div>
                        @if (!empty($apiDocumentation))
                            <div class="d-flex gap-2 mt-2 mt-sm-0">
                                <a href="{{ asset($apiDocumentation) }}" target="_blank" class="btn btn-md sg-btn-outline-primary">
                                    <i class="las la-external-link-alt me-1"></i> {{ __('Open in New Tab') }}
                                </a>
                                <a href="{{ asset($apiDocumentation) }}" download class="btn btn-md sg-btn-primary">
                                    <i class="las la-download me-1"></i> {{ __('Download PDF') }}
                                </a>
                            </div>
                        @endif
                    </div>

                    @if (!empty($apiDocumentation))
                        <div class="card border-0">
                            <div class="pdf-container" style="width: 100%; height: 850px; border-radius: 10px; overflow: hidden; border: 1px solid #e2e8f0; background: #f8fafc;">
                                <iframe src="{{ asset($apiDocumentation) }}#view=FitH&toolbar=1" width="100%" height="100%" style="border: none; display: block;">
                                    <div class="p-4 text-center">
                                        <p>{{ __('Your browser cannot display embedded PDF files directly.') }}</p>
                                        <a href="{{ asset($apiDocumentation) }}" target="_blank" class="btn btn-sm sg-btn-primary">
                                            <i class="las la-download me-1"></i> {{ __('Click here to download/view the PDF') }}
                                        </a>
                                    </div>
                                </iframe>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5 my-4">
                            <div class="mb-3">
                                <i class="las la-file-pdf text-muted" style="font-size: 64px; opacity: 0.4;"></i>
                            </div>
                            <h5 class="text-dark">{{ __('No Documentation File Available') }}</h5>
                            <p class="text-muted font-14 mb-0">
                                {{ __('API documentation has not been uploaded by the administration yet. Please contact support.') }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
