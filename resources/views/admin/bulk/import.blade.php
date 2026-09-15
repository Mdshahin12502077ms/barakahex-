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
            background-color: #fff1f0 !important;
            box-shadow: inset 3px 0 0 #ff4d4f;
        }
        .row-invalid input.customer-address {
            border-color: #ff4d4f !important;
            background-color: #fff2f0 !important;
        }
        .flash-highlight {
            animation: pulse-danger 1.5s ease-in-out 2;
        }
        @keyframes pulse-danger {
            0% { background-color: #ffccc7 !important; }
            50% { background-color: #fff1f0 !important; }
            100% { background-color: #ffccc7 !important; }
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
                                                            <option value="{{ $item->id }}">
                                                                {{ @$item->user ? ($item->user->first_name . ' ' . $item->user->last_name) : 'Merchant #' . $item->id }} {{ $item->company ? '(' . $item->company . ')' : '' }}
                                                            </option>
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
                                                        @php
                                                            $loggedMerchantId = Sentinel::getUser()->merchant?->id ?? Sentinel::getUser()->merchant_id ?? 0;
                                                            $shops = $loggedMerchantId ? App\Models\Shop::where('merchant_id', $loggedMerchantId)->get() : collect();
                                                        @endphp
                                                        @foreach ($shops as $item)
                                                            <option value="{{$item->id}}" {{ $item->default == 1 ? 'selected' : '' }}> {{$item->shop_name}}</option>
                                                        @endforeach
                                                    @endif
                                                    @if (Sentinel::getUser()->user_type == 'merchant_staff')
                                                        @php
                                                            $staffMerchantId = Sentinel::getUser()->merchant_id ?? Sentinel::getUser()->staffMerchant?->id ?? 0;
                                                            $shops = $staffMerchantId ? App\Models\Shop::where('merchant_id', $staffMerchantId)->get() : collect();
                                                        @endphp
                                                        @foreach ($shops as $item)
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
                                                <li><strong>{{ __('Customer Address') }}:</strong> {{ __('Must contain a valid District and Thana that exists in the database.') }}</li>
                                                <li><strong>{{ __('Columns Supported') }}:</strong> customer_name, customer_phone_number, customer_address, district, thana, total_quantity, price, selling_price, weight, delivery_area, customer_invoice_no, packaging, open_box, home_delivery, transfer_to_branch, destination_branch, note.</li>
                                                <li><strong>{{ __('Price & Selling Price') }}:</strong> {{ __('price_and_selling_price_must_be_numeric_example') }}</li>
                                                <li><strong>{{ __('Total Quantity') }}:</strong> {{ __('Default is 1 if empty or not provided.') }}</li>
                                                <li><strong>{{ __('Weight') }}:</strong> {{ __('if_weight_not_provided_by_default_weight_will_be_1') }}</li>
                                                @if (hasPermission('parcel_create') || hasPermission('manage_parcel') || Sentinel::getUser()->user_type == 'merchant')
                                                    <a class="import-sample-btn mt-2 d-inline-block"
                                                       href="{{ (Sentinel::getUser()->user_type == 'merchant' ? route('merchant.export') : (Sentinel::getUser()->user_type == 'merchant_staff' ? route('merchant.staff.export') : route('export'))) . '?v=' . time() }}">
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
                                    <th class="text-center" style="width: 40px;">#</th>
                                    <th>{{ __('Customer Name') }} <span class="text-danger">*</span></th>
                                    <th>{{ __('Phone') }} <span class="text-danger">*</span></th>
                                    <th>{{ __('Address') }} <span class="text-danger">*</span></th>
                                    <th style="min-width: 170px;">{{ __('District & Thana') }} <span class="text-danger">*</span></th>
                                    <th style="width: 75px;">{{ __('Qty') }}</th>
                                    <th style="width: 100px;">{{ __('COD') }} <span class="text-danger">*</span></th>
                                    <th style="width: 100px;">{{ __('Selling') }}</th>
                                    <th style="width: 80px;">{{ __('Weight') }}</th>
                                    <th>{{ __('delivery_area') }}</th>
                                    <th>{{ __('Invoice') }}</th>
                                    <th>{{ __('Note') }}</th>
                                    <th class="text-center" style="width: 50px;">{{ __('Action') }}</th>
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
                    
                    const locationBadge = (item.district_name && item.thana_name)
                        ? `<div class="badge bg-soft-success text-success border border-success p-1 px-2 mb-1" style="font-size: 11px; white-space: normal; text-align: left; line-height: 1.3;">
                            <i class="las la-map-marker-alt"></i> <strong>${escapeHtml(item.district_name)}</strong> &gt; ${escapeHtml(item.thana_name)}
                           </div>`
                        : `<div class="badge bg-soft-danger text-danger border border-danger p-1 px-2 mb-1" style="font-size: 11px; white-space: normal; text-align: left; line-height: 1.3;">
                            <i class="las la-exclamation-circle"></i> Invalid Address
                           </div>`;

                    const branchBadge = (item.destination_branch_name)
                        ? `<div class="badge bg-soft-info text-info border border-info p-1 px-2 mt-1" style="font-size: 10px;">
                            <i class="las la-code-branch"></i> ${escapeHtml(item.destination_branch_name)}
                           </div>`
                        : '';

                    const rowHtml = `
                        <tr class="${hasError ? 'row-invalid' : ''}" data-row="${i}">
                            <td class="text-center row-index fw-semibold">${i + 1}</td>
                            <td>
                                <input type="text" class="form-control form-control-sm customer-name" value="${escapeHtml(item.customer_name)}" required>
                            </td>
                            <td>
                                <input type="text" class="form-control form-control-sm customer-phone ${item.errors.some(e => e.includes('Phone')) ? 'is-invalid border-danger' : ''}" value="${escapeHtml(item.customer_phone_number)}" required>
                            </td>
                            <td>
                                <input type="text" class="form-control form-control-sm customer-address ${item.errors.some(e => e.includes('address') || e.includes('Address')) ? 'is-invalid border-danger' : ''}" value="${escapeHtml(item.customer_address)}" required>
                                ${errorBadge}
                            </td>
                            <td>
                                ${locationBadge}
                                ${branchBadge}
                                <input type="hidden" class="district-id" value="${item.district_id || ''}">
                                <input type="hidden" class="thana-id" value="${item.thana_id || ''}">
                                <input type="hidden" class="destination-branch-id" value="${item.destination_branch_id || ''}">
                                <input type="hidden" class="open-box" value="${item.open_box || 0}">
                                <input type="hidden" class="home-delivery" value="${item.home_delivery !== undefined ? item.home_delivery : 1}">
                            </td>
                            <td>
                                <input type="number" min="1" step="1" class="form-control form-control-sm total-quantity text-end" value="${item.total_quantity || 1}" required>
                            </td>
                            <td>
                                <input type="number" step="any" class="form-control form-control-sm price text-end" value="${item.price}" required>
                            </td>
                            <td>
                                <input type="number" step="any" class="form-control form-control-sm selling-price text-end" value="${item.selling_price || 0}">
                            </td>
                            <td>
                                <input type="number" step="any" class="form-control form-control-sm weight text-end" value="${item.weight}">
                            </td>
                            <td>
                                <select class="form-select form-select-sm parcel-type">
                                    <option value="same_day" ${item.parcel_type === 'same_day' || item.parcel_type === 'inside_city' ? 'selected' : ''}>{{ __('same_day') }}</option>
                                    <option value="sub_city" ${item.parcel_type === 'sub_city' ? 'selected' : ''}>{{ __('sub_city') }}</option>
                                    <option value="outside_city" ${item.parcel_type === 'outside_city' || item.parcel_type === 'sub_urban_area' ? 'selected' : ''}>{{ __('sub_urban_area') }}</option>
                                    <option value="next_day" ${item.parcel_type === 'next_day' ? 'selected' : ''}>{{ __('next_day') }}</option>
                                    <option value="frozen" ${item.parcel_type === 'frozen' ? 'selected' : ''}>{{ __('frozen') }}</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" class="form-control form-control-sm customer-invoice" value="${escapeHtml(item.customer_invoice_no)}">
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
                    const phone = $(this).find('.customer-phone').val() ? $(this).find('.customer-phone').val().trim() : '';
                    const name = $(this).find('.customer-name').val() ? $(this).find('.customer-name').val().trim() : '';
                    const address = $(this).find('.customer-address').val() ? $(this).find('.customer-address').val().trim() : '';
                    const distId = $(this).find('.district-id').val();
                    const thanaId = $(this).find('.thana-id').val();

                    if (!phone || phone.length !== 11 || !name || !address || !distId || !thanaId) {
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
                    $('#previewTable tbody').html('<tr><td colspan="13" class="text-center text-muted py-4">{{ __('No rows available. Click Reset to re-upload.') }}</td></tr>');
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

            // Live validation and address re-detection when editing in table
            let addressDetectTimeout = null;
            $(document).on('input change blur', '.customer-address', function () {
                const inputEl = $(this);
                const rowEl = inputEl.closest('tr');
                const addressVal = inputEl.val() ? inputEl.val().trim() : '';
                const locationCol = rowEl.find('.district-id').parent();

                clearTimeout(addressDetectTimeout);
                addressDetectTimeout = setTimeout(function () {
                    if (!addressVal) {
                        rowEl.find('.district-id').val('');
                        rowEl.find('.thana-id').val('');
                        locationCol.find('.badge').first().replaceWith(`
                            <div class="badge bg-soft-danger text-danger border border-danger p-1 px-2 mb-1" style="font-size: 11px; white-space: normal; text-align: left; line-height: 1.3;">
                                <i class="las la-exclamation-circle"></i> {{ __('Empty Address') }}
                            </div>
                        `);
                        inputEl.addClass('is-invalid border-danger');
                        rowEl.addClass('row-invalid');
                        updateStats();
                        return;
                    }

                    // Call backend address detection API
                    $.ajax({
                        url: "{{ route('parcel.detect.address') }}",
                        type: "POST",
                        data: {
                            _token: '{{ csrf_token() }}',
                            address: addressVal
                        },
                        success: function (res) {
                            if (res.is_valid && res.district_id && res.thana_id) {
                                rowEl.find('.district-id').val(res.district_id);
                                rowEl.find('.thana-id').val(res.thana_id);
                                locationCol.find('.badge').first().replaceWith(`
                                    <div class="badge bg-soft-success text-success border border-success p-1 px-2 mb-1" style="font-size: 11px; white-space: normal; text-align: left; line-height: 1.3;">
                                        <i class="las la-map-marker-alt"></i> <strong>${escapeHtml(res.district_name)}</strong> &gt; ${escapeHtml(res.thana_name)}
                                    </div>
                                `);
                                inputEl.removeClass('is-invalid border-danger');
                                rowEl.removeClass('row-invalid');
                                if (res.suggested_parcel_type) {
                                    rowEl.find('.parcel-type').val(res.suggested_parcel_type);
                                }
                            } else {
                                rowEl.find('.district-id').val('');
                                rowEl.find('.thana-id').val('');
                                locationCol.find('.badge').first().replaceWith(`
                                    <div class="badge bg-soft-danger text-danger border border-danger p-1 px-2 mb-1" style="font-size: 11px; white-space: normal; text-align: left; line-height: 1.3;">
                                        <i class="las la-exclamation-circle"></i> {{ __('Invalid Address') }}
                                    </div>
                                `);
                                inputEl.addClass('is-invalid border-danger');
                                rowEl.addClass('row-invalid');
                            }
                            updateStats();
                        },
                        error: function () {
                            updateStats();
                        }
                    });
                }, 350);
            });

            // Live validation on name, phone, quantity changes
            $(document).on('input', '.customer-phone, .customer-name, .total-quantity', function () {
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

            // Save / Confirm All Parcels with STRICT address and data verification
            $('#btnSaveParcels, #btnSaveParcelsBottom').on('click', function () {
                const rows = $('#previewTable tbody tr');
                if (rows.length === 0 || rows.find('.customer-name').length === 0) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('{{ __('No Data') }}', '{{ __('No parcel data to save.') }}', 'warning');
                    } else {
                        alert('{{ __('No parcel data to save.') }}');
                    }
                    return;
                }

                // Check merchant & shop selection
                const merchantVal = $('#selectMerchant').length ? $('#selectMerchant').val() : '1';
                const shopVal = $('#shop').val();

                if ($('#selectMerchant').length && !merchantVal) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('{{ __('Select Merchant') }}', '{{ __('Please select a merchant first.') }}', 'warning');
                    } else {
                        alert('{{ __('Please select a merchant first.') }}');
                    }
                    $('#selectMerchant').focus();
                    return;
                }

                if (!shopVal) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('{{ __('Select Shop') }}', '{{ __('Please select a shop first.') }}', 'warning');
                    } else {
                        alert('{{ __('Please select a shop first.') }}');
                    }
                    $('#shop').focus();
                    return;
                }

                const parcels = [];
                const invalidRows = [];
                let firstInvalidEl = null;

                // STRICT ADDRESS & DATA CHECK FOR EVERY ROW
                rows.each(function (index) {
                    const rowEl = $(this);
                    const rowNo = index + 1;
                    const name = rowEl.find('.customer-name').val() ? rowEl.find('.customer-name').val().trim() : '';
                    const phone = rowEl.find('.customer-phone').val() ? rowEl.find('.customer-phone').val().trim() : '';
                    const address = rowEl.find('.customer-address').val() ? rowEl.find('.customer-address').val().trim() : '';
                    const districtId = rowEl.find('.district-id').val();
                    const thanaId = rowEl.find('.thana-id').val();
                    const totalQty = rowEl.find('.total-quantity').val() || 1;
                    const price = rowEl.find('.price').val() || 0;
                    const sellingPrice = rowEl.find('.selling-price').val() || 0;
                    const weight = rowEl.find('.weight').val() || 1;
                    const invoice = rowEl.find('.customer-invoice').val() ? rowEl.find('.customer-invoice').val().trim() : '';
                    const parcelType = rowEl.find('.parcel-type').val();
                    const destBranchId = rowEl.find('.destination-branch-id').val();
                    const openBox = rowEl.find('.open-box').val() || 0;
                    const homeDelivery = rowEl.find('.home-delivery').val() || 1;
                    const note = rowEl.find('.note').val() ? rowEl.find('.note').val().trim() : '';

                    const rowIssues = [];
                    if (!name) {
                        rowIssues.push('{{ __('Missing Customer Name') }}');
                    }
                    const cleanPhone = phone.replace(/[^0-9]/g, '');
                    if (!phone) {
                        rowIssues.push('{{ __('Missing Phone Number') }}');
                    } else if (cleanPhone.length !== 11) {
                        rowIssues.push('{{ __('Phone must be 11 digits') }} (' + cleanPhone.length + ')');
                    }

                    if (!address) {
                        rowIssues.push('{{ __('Missing Address') }}');
                    } else if (!districtId || !thanaId) {
                        rowIssues.push('{{ __('Invalid Address: District & Thana not found in database') }}');
                    }

                    if (rowIssues.length > 0) {
                        rowEl.addClass('row-invalid flash-highlight');
                        if (!firstInvalidEl) {
                            firstInvalidEl = rowEl;
                        }
                        invalidRows.push({
                            row: rowNo,
                            name: name || '{{ __('Unnamed') }}',
                            address: address || '{{ __('No Address') }}',
                            issues: rowIssues
                        });
                    } else {
                        rowEl.removeClass('row-invalid flash-highlight');
                    }

                    parcels.push({
                        customer_name: name,
                        customer_phone_number: phone,
                        customer_address: address,
                        district_id: districtId,
                        thana_id: thanaId,
                        total_quantity: totalQty,
                        customer_invoice_no: invoice,
                        price: price,
                        selling_price: sellingPrice,
                        weight: weight,
                        parcel_type: parcelType,
                        destination_branch_id: destBranchId,
                        open_box: openBox,
                        home_delivery: homeDelivery,
                        note: note
                    });
                });

                // STOP IMMEDIATELY IF ANY ADDRESS OR FIELD IS INVALID
                if (invalidRows.length > 0) {
                    let alertHtml = `
                        <div style="text-align: left; max-height: 280px; overflow-y: auto; font-size: 13px;">
                            <div class="alert alert-danger py-2 px-3 mb-2" style="background-color: #fff2f0; border-color: #ffccc7; color: #cf1322;">
                                <i class="las la-exclamation-circle me-1"></i> <strong>{{ __('Cannot Save Parcels!') }}</strong>
                                {{ __('Found') }} <strong>${invalidRows.length}</strong> {{ __('row(s) with invalid address or missing details. Please fix the addresses or delete invalid rows before saving.') }}
                            </div>
                            <div class="list-group list-group-flush border rounded">
                    `;

                    invalidRows.forEach(function (item) {
                        alertHtml += `
                            <div class="list-group-item py-2 px-3" style="background: #fff;">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="badge bg-danger">Row #${item.row}</span>
                                    <strong class="text-dark">${escapeHtml(item.name)}</strong>
                                </div>
                                <div class="small text-muted mb-1">
                                    <i class="las la-map-marker me-1"></i> Address: <span class="fw-semibold text-dark">"${escapeHtml(item.address)}"</span>
                                </div>
                                <div class="text-danger small">
                                    <i class="las la-times-circle me-1"></i> <strong>${item.issues.join(' | ')}</strong>
                                </div>
                            </div>
                        `;
                    });

                    alertHtml += `
                            </div>
                            <div class="mt-2 text-muted small">
                                <i class="las la-lightbulb text-warning me-1"></i> {{ __('Tip: You can edit the address directly in the table (e.g. add District & Thana), or click the red trash button to remove invalid rows.') }}
                            </div>
                        </div>
                    `;

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __('Invalid Address Detected!') }}',
                            html: alertHtml,
                            confirmButtonText: '{{ __('OK, I will fix it') }}',
                            confirmButtonColor: '#dc3545',
                            width: '650px'
                        });
                    } else {
                        alert('{{ __('Cannot save parcels! Invalid address in rows: ') }}' + invalidRows.map(r => '#' + r.row + ' (' + r.address + ')').join("\n"));
                    }

                    // Smooth scroll to the first invalid row and focus on its address
                    if (firstInvalidEl) {
                        $('html, body').animate({
                            scrollTop: firstInvalidEl.offset().top - 120
                        }, 400);
                        firstInvalidEl.find('.customer-address').focus();
                    }

                    return false; // STRICTLY PREVENT SAVE
                }

                // All rows are valid -> Submit
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
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'success',
                                    title: '{{ __('Success!') }}',
                                    text: response.message || '{{ __('Parcels imported successfully!') }}',
                                    confirmButtonText: '{{ __('OK') }}',
                                    confirmButtonColor: '#28a745'
                                }).then(function () {
                                    window.location.href = response.redirect;
                                });
                            } else {
                                alert(response.message || '{{ __('Parcels imported successfully!') }}');
                                window.location.href = response.redirect;
                            }
                        } else {
                            saveBtn.prop('disabled', false).html('<i class="las la-save me-1"></i> {{ __('Save All Parcels') }}');
                            if (typeof Swal !== 'undefined') {
                                Swal.fire('{{ __('Failed') }}', response.message || '{{ __('Failed to save parcels.') }}', 'error');
                            } else {
                                alert(response.message || '{{ __('Failed to save parcels.') }}');
                            }
                        }
                    },
                    error: function (xhr) {
                        saveBtn.prop('disabled', false).html('<i class="las la-save me-1"></i> {{ __('Save All Parcels') }}');
                        let errTitle = '{{ __('Save Failed') }}';
                        let errMsg = '{{ __('Something went wrong while saving parcels.') }}';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errMsg = xhr.responseJSON.message;
                        }

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: errTitle,
                                html: '<div style="text-align: left; white-space: pre-line; font-size: 13px;">' + escapeHtml(errMsg) + '</div>',
                                confirmButtonColor: '#dc3545',
                                width: '600px'
                            });
                        } else {
                            alert(errMsg);
                        }
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

