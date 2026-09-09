@extends('backend.layouts.master')

@section('title')
    {{ __('import') }}
@endsection

@section('mainContent')

    @php
        $previewRoute = Sentinel::getUser()->user_type == 'merchant'
            ? route('merchant.import.preview')
            : (Sentinel::getUser()->user_type == 'merchant_staff' ? route('merchant.staff.import.preview') : route('import.preview'));

        $confirmRoute = Sentinel::getUser()->user_type == 'merchant'
            ? route('merchant.import.confirm')
            : (Sentinel::getUser()->user_type == 'merchant_staff' ? route('merchant.staff.import.confirm') : route('import.confirm'));
    @endphp

    <style>
        input#choose_file {
            padding-left: 11px;
            height: 38px;
        }
        .preview-table-container {
            max-height: 520px;
            overflow-y: auto;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
        }
        .preview-table-container thead th {
            position: sticky;
            top: 0;
            background: #f8fafc;
            z-index: 5;
            white-space: nowrap;
            font-size: 13px;
        }
        .preview-table-container table {
            font-size: 13px;
            margin-bottom: 0;
        }
        .preview-table-container .form-control-sm,
        .preview-table-container .form-select-sm {
            font-size: 12px;
            padding: 4px 8px;
            min-width: 110px;
        }
        .preview-table-container input.customer-address {
            min-width: 180px;
        }
        .preview-table-container input.customer-name {
            min-width: 130px;
        }
        .row-invalid {
            background-color: #fff5f5 !important;
        }
    </style>

    <div class="container-fluid">
        <div class="row gx-20">
            <div class="col-lg-12">
                <div class="header-top d-flex justify-content-between align-items-center mb-12">
                    <h3 class="section-title">{{ __('import') }}</h3>
                    <div class="oftions-content-right">
                        <a href="{{ route('parcel') }}" class="d-flex align-items-center btn sg-btn-primary gap-2">
                            <i class="las la-arrow-left"></i>
                            <span>{{ __('back') }}</span>
                        </a>
                    </div>
                </div>

                <!-- Upload Form Card -->
                <form id="parcel-import-form" enctype="multipart/form-data">
                    @csrf
                    <div class="bg-white redious-border p-20 p-sm-30 pt-sm-30">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card-inner">
                                    <div class="row g-gs">
                                        <div class="col-md-6">
                                            @if (Sentinel::getUser()->user_type != 'merchant' && Sentinel::getUser()->user_type != 'merchant_staff')
                                                <div class="mb-3">
                                                    <label class="form-label" for="selectMerchant">{{ __('merchants') }}
                                                        <span class="text-danger">*</span></label>
                                                    <select
                                                        class="with_search form-select form-control @error('merchant') is-invalid @enderror"
                                                        id="selectMerchant" name="merchant" required>
                                                        <option value="">{{ __('select_merchant') }} </option>
                                                        @foreach ($merchants as $item)
                                                            <option
                                                                value="{{$item->merchant->id}}"> {{$item->first_name.' '.$item->last_name}} </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @endif
                                            <div class="mb-3">
                                                <label class="form-label" for="shop">{{ __('Shop') }}
                                                    <span class="text-danger">*</span></label>
                                                <select
                                                    class="with_search form-select form-control shop @error('shop') is-invalid @enderror"
                                                    id="shop" name="shop" required>
                                                    <option value="">{{ __('select_shop') }} </option>
                                                    @if (Sentinel::getUser()->user_type == 'merchant')
                                                        @foreach (App\Models\Shop::where('merchant_id', Sentinel::getUser()->merchant->id)->get() as $item)
                                                            <option value="{{$item->id}}" {{ $item->default == 1 ? 'selected' : '' }}> {{$item->shop_name}}</option>
                                                        @endforeach
                                                    @endif
                                                    @if (Sentinel::getUser()->user_type == 'merchant_staff')
                                                        @foreach (App\Models\Shop::where('merchant_id', Sentinel::getUser()->merchant_id)->get() as $item)
                                                            <option value="{{$item->id}}" {{ $item->default == 1 ? 'selected' : '' }}> {{$item->shop_name}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="choose_file"
                                                       class="form-label">{{ __('choose_file') }} <span class="text-danger">*</span></label>
                                                <input class="form-control" name="file" type="file" id="choose_file"
                                                       accept=".xlsx, .csv" required>
                                            </div>

                                            <div class="col-md-12 text-left mt-4">
                                                <div class="mb-3 d-flex gap-2">
                                                    <button type="submit" id="btnPreviewFile"
                                                            class="btn sg-btn-primary">
                                                        <i class="las la-file-excel me-1"></i> {{ __('Preview & Review Data') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h5>{{ __('n_b') }}</h5>
                                            <p>{{ __('please_check_this_before_importing_your_file') }}</p>
                                            <ul class="list list-sm list-success">
                                                <li>{{ __('uploaded_file_must_be_xlsx_or_csv') }}</li>
                                                <li>{{ __('the_file_must_contain_price_selling_price_customer_name_customer_invoice_no_customer_phone_number_customer_address') }}</li>
                                                <li>{{ __('price_and_selling_price_must_be_numeric_example') }}</li>
                                                <li>{{ __('fragile_parcel_type_note_weight_pickup_shop_phone_number_pickup_address_pickup_branch') }}</li>
                                                <li>{{ __('if_parcel_type_not_provided_by_default_it_will_be_set_for_next_day') }}</li>
                                                <li>{{ __('if_weight_not_provided_by_default_weight_will_be_1') }}</li>
                                                @if (hasPermission('parcel_create') || hasPermission('manage_parcel') || Sentinel::getUser()->user_type == 'merchant')
                                                    <a class="import-sample-btn mt-2 d-inline-block"
                                                       href="{{ Sentinel::getUser()->user_type == 'merchant' ? route('merchant.export') : (Sentinel::getUser()->user_type == 'merchant_staff' ? route('merchant.staff.export') : route('export')) }}">
                                                        <span><i class="icon las la-file-download"></i></span>
                                                        <span>{{ __('parcel_import_sample') . ' ' . __('download') }}</span>
                                                    </a>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Interactive Preview Section -->
                <div id="preview-container" class="bg-white redious-border p-20 p-sm-30 pt-sm-30 mt-4 d-none">
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                        <div>
                            <h4 class="mb-1 fw-bold text-dark"><i class="las la-table text-primary me-1"></i> {{ __('Import Data Preview') }}</h4>
                            <div class="text-muted small">
                                <span>{{ __('Total Parcels') }}: <strong class="text-primary font-14" id="stat-total">0</strong></span> |
                                <span>{{ __('Valid') }}: <strong class="text-success" id="stat-valid">0</strong></span> |
                                <span>{{ __('Need Attention') }}: <strong class="text-danger" id="stat-invalid">0</strong></span>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-danger" id="btnResetImport">
                                <i class="las la-undo me-1"></i> {{ __('Reset') }}
                            </button>
                            <button type="button" class="btn sg-btn-primary" id="btnSaveParcels">
                                <i class="las la-save me-1"></i> {{ __('Save All Parcels') }} (<span id="btn-save-count">0</span>)
                            </button>
                        </div>
                    </div>

                    <div class="alert alert-info py-2 small mb-3">
                        <i class="las la-info-circle me-1"></i>
                        {{ __('You can edit any field directly inside the table before saving, or click the red delete button to remove a row.') }}
                    </div>

                    <div class="preview-table-container">
                        <table class="table table-bordered table-hover align-middle mb-0" id="previewTable">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 45px;">#</th>
                                    <th>{{ __('Customer Name') }} <span class="text-danger">*</span></th>
                                    <th>{{ __('Phone') }} <span class="text-danger">*</span></th>
                                    <th>{{ __('Address') }} <span class="text-danger">*</span></th>
                                    <th>{{ __('Invoice No') }}</th>
                                    <th>{{ __('COD (Price)') }} <span class="text-danger">*</span></th>
                                    <th>{{ __('Weight (KG)') }}</th>
                                    <th>{{ __('Parcel Type') }}</th>
                                    <th>{{ __('Note') }}</th>
                                    <th class="text-center" style="width: 60px;">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Rendered dynamically via JavaScript -->
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <span class="text-muted small">{{ __('Review all rows before confirming.') }}</span>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-danger" id="btnResetImportBottom">
                                <i class="las la-undo me-1"></i> {{ __('Reset') }}
                            </button>
                            <button type="button" class="btn sg-btn-primary" id="btnSaveParcelsBottom">
                                <i class="las la-save me-1"></i> {{ __('Save All Parcels') }} (<span class="btn-save-count-bottom">0</span>)
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function () {
            // Dynamic shop fetch when merchant is selected (Admin only)
            $(document).on('change', '#selectMerchant', function () {
                const merchantId = $(this).val();
                const shopSelect = $('#shop');
                shopSelect.html('<option value="">Loading...</option>');

                if (merchantId) {
                    $.ajax({
                        url: "{{ route('get.shops.by.merchant') }}",
                        type: "GET",
                        data: { merchant_id: merchantId },
                        success: function (response) {
                            let options = '<option value="">{{ __('select_shop') }}</option>';
                            if (Array.isArray(response)) {
                                response.forEach(shop => {
                                    options += `<option value="${shop.id}">${shop.shop_name}</option>`;
                                });
                            } else {
                                options += '<option disabled>No valid shop data</option>';
                            }
                            shopSelect.html(options);
                        },
                        error: function () {
                            shopSelect.html('<option value="">{{ __('no_shops_found') }}</option>');
                        }
                    });
                } else {
                    shopSelect.html('<option value="">{{ __('select_shop') }}</option>');
                }
            });

            // Form Submit -> Trigger AJAX Preview
            $('#parcel-import-form').on('submit', function (e) {
                e.preventDefault();

                const shopVal = $('#shop').val();
                if (!shopVal) {
                    alert('{{ __('Please select a shop first.') }}');
                    return;
                }

                const fileInput = document.getElementById('choose_file');
                if (!fileInput.files || !fileInput.files[0]) {
                    alert('{{ __('Please choose an Excel or CSV file.') }}');
                    return;
                }

                const formData = new FormData(this);
                const btn = $('#btnPreviewFile');
                const originalHtml = btn.html();

                btn.prop('disabled', true).html('<i class="las la-spinner la-spin me-1"></i> {{ __('Reading file...') }}');

                $.ajax({
                    url: "{{ $previewRoute }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (res) {
                        btn.prop('disabled', false).html(originalHtml);

                        if (res.status && res.parcels && res.parcels.length > 0) {
                            renderPreviewTable(res.parcels);
                            $('#preview-container').removeClass('d-none');
                            $('html, body').animate({
                                scrollTop: $('#preview-container').offset().top - 30
                            }, 400);
                        } else {
                            alert(res.message || '{{ __('No data found in the uploaded file.') }}');
                        }
                    },
                    error: function (xhr) {
                        btn.prop('disabled', false).html(originalHtml);
                        let msg = '{{ __('Failed to parse the file. Please check file format.') }}';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        alert(msg);
                    }
                });
            });

            // Render Table Rows
            function renderPreviewTable(parcels) {
                const tbody = $('#previewTable tbody');
                tbody.empty();

                parcels.forEach((item, i) => {
                    const hasError = !item.is_valid;
                    const errorBadge = hasError ? `<div class="text-danger small mt-1 font-11"><i class="las la-exclamation-triangle"></i> ${item.errors.join(', ')}</div>` : '';

                    const rowHtml = `
                        <tr class="${hasError ? 'row-invalid' : ''}" data-row="${i}">
                            <td class="text-center row-index fw-semibold">${i + 1}</td>
                            <td>
                                <input type="text" class="form-control form-control-sm customer-name" value="${escapeHtml(item.customer_name)}" required>
                            </td>
                            <td>
                                <input type="text" class="form-control form-control-sm customer-phone ${item.errors.some(e => e.includes('Phone')) ? 'is-invalid border-danger' : ''}" value="${escapeHtml(item.customer_phone_number)}" required>
                                ${errorBadge}
                            </td>
                            <td>
                                <input type="text" class="form-control form-control-sm customer-address" value="${escapeHtml(item.customer_address)}" required>
                            </td>
                            <td>
                                <input type="text" class="form-control form-control-sm customer-invoice" value="${escapeHtml(item.customer_invoice_no)}">
                            </td>
                            <td>
                                <input type="number" step="any" class="form-control form-control-sm price text-end" value="${item.price}" required>
                            </td>
                            <td>
                                <input type="number" step="any" class="form-control form-control-sm weight text-end" value="${item.weight}">
                            </td>
                            <td>
                                <select class="form-select form-select-sm parcel-type">
                                    <option value="same_day" ${item.parcel_type === 'same_day' || item.parcel_type === 'inside_city' ? 'selected' : ''}>{{ __('same_day') }} ({{ __('inside_city') }})</option>
                                    <option value="next_day" ${item.parcel_type === 'next_day' ? 'selected' : ''}>{{ __('next_day') }}</option>
                                    <option value="sub_city" ${item.parcel_type === 'sub_city' ? 'selected' : ''}>{{ __('sub_city') }}</option>
                                    <option value="sub_urban_area" ${item.parcel_type === 'sub_urban_area' || item.parcel_type === 'outside_city' ? 'selected' : ''}>{{ __('outside_city') }}</option>
                                    <option value="frozen" ${item.parcel_type === 'frozen' ? 'selected' : ''}>{{ __('frozen') }}</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" class="form-control form-control-sm note" value="${escapeHtml(item.note)}">
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-danger delete-row" title="{{ __('Delete Row') }}">
                                    <i class="las la-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                    tbody.append(rowHtml);
                });

                updateStats();
            }

            // Update row numbers and badges
            function updateStats() {
                const totalRows = $('#previewTable tbody tr').length;
                let invalidCount = 0;

                $('#previewTable tbody tr').each(function (idx) {
                    $(this).find('.row-index').text(idx + 1);
                    const phone = $(this).find('.customer-phone').val().trim();
                    const name = $(this).find('.customer-name').val().trim();
                    const address = $(this).find('.customer-address').val().trim();

                    if (!phone || phone.length !== 11 || !name || !address) {
                        invalidCount++;
                        $(this).addClass('row-invalid');
                    } else {
                        $(this).removeClass('row-invalid');
                    }
                });

                $('#stat-total').text(totalRows);
                $('#stat-valid').text(totalRows - invalidCount);
                $('#stat-invalid').text(invalidCount);
                $('#btn-save-count').text(totalRows);
                $('.btn-save-count-bottom').text(totalRows);

                if (totalRows === 0) {
                    $('#previewTable tbody').html('<tr><td colspan="10" class="text-center text-muted py-4">{{ __('No rows available. Click Reset to re-upload.') }}</td></tr>');
                    $('#btnSaveParcels, #btnSaveParcelsBottom').prop('disabled', true);
                } else {
                    $('#btnSaveParcels, #btnSaveParcelsBottom').prop('disabled', false);
                }
            }

            // Delete individual row
            $(document).on('click', '.delete-row', function () {
                $(this).closest('tr').remove();
                updateStats();
            });

            // Live validation on phone change
            $(document).on('input', '.customer-phone, .customer-name, .customer-address', function () {
                updateStats();
            });

            // Reset Buttons: Clears table and file input
            $('#btnResetImport, #btnResetImportBottom').on('click', function () {
                if (confirm('{{ __('Are you sure you want to reset? All previewed data will be cleared.') }}')) {
                    $('#choose_file').val('');
                    $('#previewTable tbody').empty();
                    $('#preview-container').addClass('d-none');
                    $('html, body').animate({
                        scrollTop: $('#parcel-import-form').offset().top - 30
                    }, 300);
                }
            });

            // Save / Confirm All Parcels
            $('#btnSaveParcels, #btnSaveParcelsBottom').on('click', function () {
                const rows = $('#previewTable tbody tr');
                if (rows.length === 0 || rows.find('.customer-name').length === 0) {
                    alert('{{ __('No parcel data to save.') }}');
                    return;
                }

                const parcels = [];
                let hasValidationError = false;

                rows.each(function (index) {
                    const name = $(this).find('.customer-name').val().trim();
                    const phone = $(this).find('.customer-phone').val().trim();
                    const address = $(this).find('.customer-address').val().trim();
                    const price = $(this).find('.price').val();
                    const weight = $(this).find('.weight').val();
                    const invoice = $(this).find('.customer-invoice').val().trim();
                    const parcelType = $(this).find('.parcel-type').val();
                    const note = $(this).find('.note').val().trim();

                    if (!name || !phone || !address) {
                        hasValidationError = true;
                        $(this).addClass('row-invalid');
                    }

                    parcels.push({
                        customer_name: name,
                        customer_phone_number: phone,
                        customer_address: address,
                        customer_invoice_no: invoice,
                        price: price,
                        weight: weight,
                        parcel_type: parcelType,
                        note: note
                    });
                });

                if (hasValidationError) {
                    if (!confirm('{{ __('Some rows have missing customer name, phone number, or address. Are you sure you want to proceed?') }}')) {
                        return;
                    }
                }

                const saveBtn = $('#btnSaveParcels, #btnSaveParcelsBottom');
                saveBtn.prop('disabled', true).html('<i class="las la-spinner la-spin me-1"></i> {{ __('Saving...') }}');

                $.ajax({
                    url: "{{ $confirmRoute }}",
                    type: "POST",
                    data: {
                        _token: '{{ csrf_token() }}',
                        merchant: $('#selectMerchant').val(),
                        shop: $('#shop').val(),
                        parcels: parcels
                    },
                    success: function (response) {
                        if (response.status) {
                            alert(response.message || '{{ __('Parcels imported successfully!') }}');
                            window.location.href = response.redirect;
                        } else {
                            saveBtn.prop('disabled', false).html('<i class="las la-save me-1"></i> {{ __('Save All Parcels') }}');
                            alert(response.message || '{{ __('Failed to save parcels.') }}');
                        }
                    },
                    error: function (xhr) {
                        saveBtn.prop('disabled', false).html('<i class="las la-save me-1"></i> {{ __('Save All Parcels') }}');
                        let err = '{{ __('Something went wrong while saving parcels.') }}';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            err = xhr.responseJSON.message;
                        }
                        alert(err);
                    }
                });
            });

            // Helper to escape HTML characters
            function escapeHtml(text) {
                if (!text) return '';
                return String(text)
                    .replace(/&/g, "&amp;")
                    .replace(/</g, "&lt;")
                    .replace(/>/g, "&gt;")
                    .replace(/"/g, "&quot;")
                    .replace(/'/g, "&#039;");
            }
        });
    </script>
@endpush

