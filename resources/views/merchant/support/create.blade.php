@extends('backend.layouts.master')

@section('title')
    {{ __('Create Support Ticket') }}
@endsection

@section('mainContent')
    <div class="container-fluid">
        <div class="row gx-20">
            <div class="col-lg-12">
                <div class="header-top d-flex justify-content-between align-items-center mb-12">
                    <h3 class="section-title">{{ __('Create Support Ticket') }}</h3>
                    <div class="oftions-content-right">
                        <a href="{{ route('merchant.support-tickets.index') }}" class="d-flex align-items-center btn sg-btn-primary gap-2">
                            <i class="las la-arrow-left"></i>
                            <span>{{ __('Back to Tickets') }}</span>
                        </a>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-8 col-md-10 mx-auto">
                        <div class="card bg-white redious-border p-20 p-sm-30">
                            <form action="{{ Route::has('merchant.support-tickets.store') ? route('merchant.support-tickets.store') : '#' }}" method="POST" enctype="multipart/form-data" id="supportTicketForm">
                                @csrf

                                <!-- Row 1: Ticket Type & Priority -->
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold" for="ticket_type">
                                            {{ __('Ticket Type / Issue Category') }} <span class="text-danger">*</span>
                                        </label>
                                        <select name="ticket_type" id="ticket_type" class="form-select @error('ticket_type') is-invalid @enderror" required>
                                            <option value="">-- {{ __('Select Issue Type') }} --</option>
                                            <option value="parcel_issue" {{ old('ticket_type') == 'parcel_issue' ? 'selected' : '' }}>{{ __('Parcel Issue') }}</option>
                                            <option value="delivery_issue" {{ old('ticket_type') == 'delivery_issue' ? 'selected' : '' }}>{{ __('Delivery Issue / Delay') }}</option>
                                            <option value="payment_issue" {{ old('ticket_type') == 'payment_issue' ? 'selected' : '' }}>{{ __('Payment / COD Issue') }}</option>
                                            <option value="return_issue" {{ old('ticket_type') == 'return_issue' ? 'selected' : '' }}>{{ __('Return Item Issue') }}</option>
                                            <option value="merchant_ticket" {{ old('ticket_type') == 'merchant_ticket' ? 'selected' : '' }}>{{ __('General Merchant Query') }}</option>
                                            <option value="customer_ticket" {{ old('ticket_type') == 'customer_ticket' ? 'selected' : '' }}>{{ __('Customer Complaint') }}</option>
                                            <option value="technical_issue" {{ old('ticket_type') == 'technical_issue' ? 'selected' : '' }}>{{ __('Technical / Website Issue') }}</option>
                                        </select>
                                        @error('ticket_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold" for="priority">
                                            {{ __('Priority Level') }} <span class="text-danger">*</span>
                                        </label>
                                        <select name="priority" id="priority" class="form-select @error('priority') is-invalid @enderror" required>
                                            <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>🟢 {{ __('Low (General Query)') }}</option>
                                            <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>🔵 {{ __('Medium (Standard)') }}</option>
                                            <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>🟠 {{ __('High (Needs Quick Action)') }}</option>
                                            <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>🔴 {{ __('Urgent (Critical Problem)') }}</option>
                                        </select>
                                        @error('priority')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Row 2: Tracking / Parcel Reference -->
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label fw-bold" for="tracking_number">
                                            {{ __('Parcel Tracking ID / Order No') }} <small class="text-muted">({{ __('Optional if not parcel related') }})</small>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="las la-barcode"></i></span>
                                            <input type="text" name="tracking_number" id="tracking_number" 
                                                   class="form-control @error('tracking_number') is-invalid @enderror" 
                                                   value="{{ old('tracking_number', request('tracking_number')) }}" 
                                                   placeholder="{{ __('Enter Tracking ID (e.g. BARAKA-12345)') }}">
                                        </div>
                                        @error('tracking_number')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Row 3: Subject -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold" for="subject">
                                        {{ __('Subject') }} <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="subject" id="subject" 
                                           class="form-control @error('subject') is-invalid @enderror" 
                                           value="{{ old('subject') }}" 
                                           placeholder="{{ __('Briefly state the issue (e.g., Payment not received for order #1234)') }}" required>
                                    @error('subject')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Row 4: Description -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold" for="description">
                                        {{ __('Detailed Description') }} <span class="text-danger">*</span>
                                    </label>
                                    <textarea name="description" id="description" rows="5" 
                                              class="form-control @error('description') is-invalid @enderror" 
                                              placeholder="{{ __('Please describe your issue in detail. Include date, customer phone or any relevant info...') }}" required>{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Row 5: Attachments -->
                                <div class="mb-4">
                                    <label class="form-label fw-bold" for="attachments">
                                        {{ __('Attachments') }} <small class="text-muted">({{ __('Optional: Screenshot, Invoice, Receipt') }})</small>
                                    </label>
                                    <input type="file" name="attachments[]" id="attachments" 
                                           class="form-control @error('attachments') is-invalid @enderror" 
                                           multiple accept="image/*,.pdf,.doc,.docx">
                                    <small class="text-muted mt-1 d-block">
                                        <i class="las la-info-circle"></i> {{ __('Supported formats: JPG, PNG, PDF. Max file size: 5MB') }}
                                    </small>
                                    @error('attachments')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Submit Button -->
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('merchant.support-tickets.index') }}" class="btn btn-secondary px-4">
                                        {{ __('Cancel') }}
                                    </a>
                                    <button type="submit" class="btn sg-btn-primary px-4 d-flex align-items-center gap-2">
                                        <i class="las la-paper-plane"></i>
                                        <span>{{ __('Submit Ticket') }}</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
