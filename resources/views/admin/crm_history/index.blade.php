@extends('backend.layouts.master')
@section('title', __('Customer CRM & History'))
@section('mainContent')
    <section class="oftions">
        <div class="container-fluid">
            <!-- Header Section -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="header-top d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h3 class="section-title mb-1">{{ __('Customer CRM & Return History') }}</h3>
                            <p class="text-muted mb-0">{{ __('Search by customer phone number to analyze order history, success rates, return risks & COD summary.') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search Card Section -->
            <div class="row justify-content-center">
                <div class="col-xl-10 col-lg-12 col-12">
                    <div class="bg-white redious-border p-4 p-md-5 mb-4 shadow-sm border">
                        <div class="text-center mb-4">
                            <div class="crm-icon-wrapper mb-2">
                                <i class="las la-user-shield text-primary" style="font-size: 42px;"></i>
                            </div>
                            <h4 class="fw-bold mb-1">{{ __('Look Up Customer Information') }}</h4>
                            <p class="text-muted small">{{ __('Enter 11-digit mobile number to view comprehensive customer analytics') }}</p>
                        </div>

                        <!-- Search Form -->
                        <form id="crmSearchForm" action="javascript:void(0);" onsubmit="return false;">
                            @csrf
                            <div class="crm-search-box">
                                <div class="input-group input-group-lg shadow-sm rounded-pill overflow-hidden border p-1 bg-white">
                                    <span class="input-group-text bg-transparent border-0 ps-3 pe-2 text-primary">
                                        <i class="las la-phone-volume fs-4"></i>
                                    </span>
                                    <input type="text" 
                                           id="crm_phone_number" 
                                           name="phone_number" 
                                           class="form-control border-0 shadow-none px-2" 
                                           placeholder="e.g. 01712345678, 018..., 019..." 
                                           autocomplete="off"
                                           maxlength="15"
                                           required>
                                    <button class="btn btn-sm btn-outline-secondary border-0 d-none me-1" type="button" id="crmClearBtn" style="border-radius: 50%;">
                                        <i class="las la-times"></i>
                                    </button>
                                    <button class="btn sg-btn-primary px-4 py-2 rounded-pill fw-semibold d-flex align-items-center gap-2 crmSearchBtn"  id="crmSearchBtn">
                                        <i class="las la-search fs-5"></i>
                                        <span>{{ __('Search') }}</span>
                                    </button>
                                </div>
                            </div>
                        </form>

                      
                    </div>
                </div>
            </div>

            
            <div class="row justify-content-center">
                <div class="col-xl-10 col-lg-12 col-12" id="crmResultArea">
                
                    <div class="bg-white redious-border p-5 text-center shadow-sm border" id="crmEmptyState">
                        <div class="mb-3 text-muted opacity-50">
                            <i class="las la-search-location" style="font-size: 64px;"></i>
                        </div>
                        <h5 class="fw-semibold text-secondary">{{ __('No Customer Profile Selected') }}</h5>
                        <p class="text-muted mb-0 small">{{ __('Search by phone number above to see customer profile and parcel summary.') }}</p>
                    </div>

                   
                    <div class="bg-white redious-border p-5 text-center shadow-sm border d-none" id="crmLoadingState">
                        <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <h6 class="fw-semibold text-secondary">{{ __('Fetching Customer CRM Records...') }}</h6>
                        <p class="text-muted small mb-0">{{ __('Please wait while we aggregate parcel and return statistics.') }}</p>
                    </div>

                    <div id="crmDataContainer" class="d-none"></div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('style')
<style>
    .crm-search-box {
        max-width: 680px;
        margin: 0 auto;
    }
    .crm-search-box .input-group {
        border: 2px solid #e2e8f0 !important;
        transition: all 0.25s ease-in-out;
    }
    .crm-search-box .input-group:focus-within {
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15) !important;
    }
    .crm-search-box input::placeholder {
        color: #94a3b8;
        font-size: 0.95rem;
    }
    .crm-icon-wrapper {
        width: 70px;
        height: 70px;
        line-height: 70px;
        border-radius: 50%;
        background: rgba(59, 130, 246, 0.1);
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    /* Enhanced CRM Results Styling */
    .crm-card {
        background: #ffffff;
        border-radius: 18px;
        border: 1px solid #edf2f7;
        box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.05);
        transition: all 0.25s ease;
    }
    .crm-card:hover {
        box-shadow: 0 8px 26px -2px rgba(15, 23, 42, 0.08);
    }
    .crm-avatar-box {
        width: 74px;
        height: 74px;
        border-radius: 50%;
        background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        border: 3px solid #ffffff;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }
    .trust-score-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #ecfdf5;
        color: #059669;
        border: 1.5px solid #a7f3d0;
        border-radius: 30px;
        font-weight: 600;
        font-size: 13px;
        padding: 7px 16px;
        box-shadow: 0 2px 6px rgba(16, 185, 129, 0.1);
    }
    .crm-stat-card {
        border-radius: 16px;
        padding: 20px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        position: relative;
        overflow: hidden;
    }
    .crm-stat-card:hover {
        transform: translateY(-2px);
    }
    .crm-table {
        border-collapse: separate;
        border-spacing: 0;
    }
    .crm-table thead th {
        background-color: #f8fafc !important;
        font-size: 12px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 14px 18px;
        border: none;
    }
    .crm-table tbody td {
        padding: 16px 18px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 13.5px;
        color: #334155;
    }
    .crm-table tbody tr:last-child td {
        border-bottom: none;
    }
    .crm-table tbody tr:hover td {
        background-color: #fbfcfe;
    }
    .badge-status-delivered {
        background-color: #d1fae5;
        color: #059669;
        border: 1px solid #a7f3d0;
        font-weight: 600;
        font-size: 12px;
        padding: 5px 14px;
        border-radius: 30px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .badge-status-returned {
        background-color: #fee2e2;
        color: #dc2626;
        border: 1px solid #fecaca;
        font-weight: 600;
        font-size: 12px;
        padding: 5px 14px;
        border-radius: 30px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
</style>
@endpush

@push('script')
<script>
    $(document).ready(function () {
        const phoneInput = $('#crm_phone_number');
        const clearBtn = $('#crmClearBtn');
        phoneInput.on('input', function () {
            if ($(this).val().trim().length > 0) {
                clearBtn.removeClass('d-none');
            } else {
                clearBtn.addClass('d-none');
            }
        });

        clearBtn.on('click', function () {
            phoneInput.val('').focus();
            $(this).addClass('d-none');
        });


     $(document).on('click','.crmSearchBtn',function(){
       const phoneNumber = $('#crm_phone_number').val();
       $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
       $.ajax({
        url:"{{ (Sentinel::getUser() && (Sentinel::getUser()->user_type == 'merchant' || Sentinel::getUser()->user_type == 'merchant_staff')) ? route('merchant.crm-history.search') : route('crm-history.crm.search') }}",
        type:"post",
        data:{"phone_number":phoneNumber},
        success:function(response){
          if(response.status==true){
            console.log(response.data);

            const trustScore = response.data.customer.trust_score || 0;
            let trustColor = '#059669';
            let trustBg = '#ecfdf5';
            let trustBorder = '#a7f3d0';

            if (trustScore < 50) {
                trustColor = '#dc2626';
                trustBg = '#fff1f2';
                trustBorder = '#fecdd3';
            } else if (trustScore < 80) {
                trustColor = '#d97706';
                trustBg = '#fffbeb';
                trustBorder = '#fde68a';
            }

            const initial = (response.data.customer.name || 'C').trim().charAt(0).toUpperCase();

            let html = `
            <!-- Top Section: Customer Profile & 4 Stats Cards -->
            <div class="row g-3 mb-4">
                <!-- Customer Profile Card -->
                <div class="col-lg-6 col-12">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white d-flex flex-column justify-content-between" style="border: 1px solid #e2e8f0 !important;">
                        <div>
                            <!-- Top Profile Header -->
                            <div class="d-flex align-items-start gap-3 mb-3">
                                <div class="position-relative flex-shrink-0">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white shadow-sm" style="width: 66px; height: 66px; background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); font-size: 26px; letter-spacing: 1px;">
                                        ${initial}
                                    </div>
                                    <span class="position-absolute bottom-0 end-0 rounded-circle border border-2 border-white" style="width: 15px; height: 15px; background-color: #10b981;" title="Active Customer"></span>
                                </div>
                                <div class="flex-grow-1 overflow-hidden">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-1 mb-1">
                                        <h4 class="fw-bold text-dark mb-0 text-truncate d-flex align-items-center gap-1">
                                            ${response.data.customer.name}
                                            <i class="las la-check-circle text-primary fs-5" title="Verified Customer"></i>
                                        </h4>
                                        <span class="badge px-2.5 py-1 rounded-pill small fw-semibold" style="background-color: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe;">
                                            <i class="las la-id-card me-1"></i> Customer CRM
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 flex-wrap text-muted small mt-1">
                                        <span class="d-inline-flex align-items-center gap-1 bg-light border px-2 py-0.5 rounded-pill font-monospace text-dark fw-medium">
                                            <i class="las la-phone text-primary"></i> ${response.data.customer.phone}
                                        </span>
                                        <span class="text-secondary small">
                                            <i class="las la-check-double text-success"></i> Phone Verified
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Address Box -->
                            <div class="p-3 rounded-3 mb-3" style="background: #f8fafc; border: 1px dashed #cbd5e1;">
                                <div class="d-flex align-items-start gap-2">
                                    <div class="rounded-circle p-1 d-flex align-items-center justify-content-center flex-shrink-0 mt-0.5" style="background: rgba(239, 68, 68, 0.1); color: #ef4444; width: 26px; height: 26px;">
                                        <i class="las la-map-marker-alt" style="font-size: 15px;"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <span class="text-uppercase text-muted fw-bold d-block" style="font-size: 10.5px; letter-spacing: 0.5px;">Delivery Address</span>
                                        <div class="text-dark small fw-medium mt-0.5" style="line-height: 1.4;">${response.data.customer.address}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Trust Score Progress & Risk Level -->
                        <div class="p-3 rounded-3" style="background: ${trustBg}; border: 1px solid ${trustBorder};">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="fw-bold small d-flex align-items-center gap-1.5" style="color: ${trustColor}; font-size: 13.5px;">
                                    <i class="las la-shield-alt fs-5"></i> Trust Score: <strong>${trustScore}%</strong>
                                </span>
                                <span class="badge px-2.5 py-1 rounded-pill small fw-semibold" style="background: ${trustColor}; color: #ffffff;">
                                    ${response.data.customer.risk_label}
                                </span>
                            </div>
                            <div class="progress" style="height: 6px; background-color: rgba(0,0,0,0.06); border-radius: 10px;">
                                <div class="progress-bar rounded-pill" role="progressbar" style="width: ${trustScore}%; background-color: ${trustColor};" aria-valuenow="${trustScore}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4 Stat Cards in 2x2 Grid -->
                <div class="col-lg-6 col-12">
                    <div class="row g-3 h-100">
                        <!-- Total Orders -->
                        <div class="col-6">
                            <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white d-flex flex-column justify-content-between" style="border: 1px solid #eef2f6 !important;">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="text-secondary small fw-semibold">Total Orders:</span>
                                    <span class="badge rounded-circle p-2" style="background: #f1f5f9; color: #475569;">
                                        <i class="las la-shopping-bag fs-5"></i>
                                    </span>
                                </div>
                                <div>
                                    <div class="h2 fw-bold text-dark mb-0">${response.data.stats.total_orders}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Delivered -->
                        <div class="col-6">
                            <div class="card border-0 shadow-sm rounded-4 p-3 h-100 d-flex flex-column justify-content-between" style="background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%); border: 1.5px solid #a7f3d0 !important;">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="small fw-semibold" style="color: #065f46;">Delivered:</span>
                                    <span class="badge rounded-circle p-2" style="background: rgba(16, 185, 129, 0.2); color: #059669;">
                                        <i class="las la-check-circle fs-5"></i>
                                    </span>
                                </div>
                                <div>
                                    <div class="h2 fw-bold mb-0" style="color: #059669;">${response.data.stats.delivered_count} <span class="fs-6 fw-semibold text-success">(${response.data.stats.delivered_percent}%)</span></div>
                                </div>
                            </div>
                        </div>

                        <!-- Returned -->
                        <div class="col-6">
                            <div class="card border-0 shadow-sm rounded-4 p-3 h-100 d-flex flex-column justify-content-between" style="background: linear-gradient(135deg, #fff1f2 0%, #fee2e2 100%); border: 1.5px solid #fecdd3 !important;">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="small fw-semibold" style="color: #991b1b;">Returned:</span>
                                    <span class="badge rounded-circle p-2" style="background: rgba(239, 68, 68, 0.2); color: #dc2626;">
                                        <i class="las la-undo-alt fs-5"></i>
                                    </span>
                                </div>
                                <div>
                                    <div class="h2 fw-bold mb-0" style="color: #dc2626;">${response.data.stats.returned_count} <span class="fs-6 fw-semibold text-danger">(${response.data.stats.returned_percent}%)</span></div>
                                </div>
                            </div>
                        </div>

                        <!-- Total COD Value -->
                        <div class="col-6">
                            <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white d-flex flex-column justify-content-between" style="border: 1px solid #eef2f6 !important;">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="text-secondary small fw-semibold">Total COD Value:</span>
                                    <span class="badge rounded-circle p-2" style="background: #fffbeb; color: #d97706;">
                                        <i class="las la-wallet fs-5"></i>
                                    </span>
                                </div>
                                <div>
                                    <div class="h3 fw-bold text-dark mb-0">${response.data.stats.total_cod_amount}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Middle Section: Donut Ring Chart Card -->
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white text-center" style="border: 1px solid #eef2f6 !important;">
                <div class="d-flex justify-content-center align-items-center py-2 position-relative" style="height: 160px;">
                    <svg width="150" height="150" viewBox="0 0 42 42" class="donut" style="transform: rotate(-90deg);">
                        <circle cx="21" cy="21" r="15.91549430918954" fill="transparent" stroke="#f1f5f9" stroke-width="5"></circle>
                        <!-- Delivered Green -->
                        <circle cx="21" cy="21" r="15.91549430918954" fill="transparent" stroke="#10b981" stroke-width="5" stroke-dasharray="${response.data.stats.delivered_percent} ${100 - response.data.stats.delivered_percent}" stroke-dashoffset="0"></circle>
                        <!-- Returned Red -->
                        <circle cx="21" cy="21" r="15.91549430918954" fill="transparent" stroke="#ef4444" stroke-width="5" stroke-dasharray="${response.data.stats.returned_percent} ${100 - response.data.stats.returned_percent}" stroke-dashoffset="-${response.data.stats.delivered_percent}"></circle>
                        <!-- Cancel Purple -->
                        <circle cx="21" cy="21" r="15.91549430918954" fill="transparent" stroke="#6366f1" stroke-width="5" stroke-dasharray="${response.data.stats.cancelled_percent} ${100 - response.data.stats.cancelled_percent}" stroke-dashoffset="-${response.data.stats.delivered_percent + response.data.stats.returned_percent}"></circle>
                    </svg>
                    <!-- Center Stats Label inside Donut -->
                    <div class="position-absolute text-center" style="pointer-events: none;">
                        <div class="h3 fw-bolder mb-0 text-dark">${response.data.stats.delivered_percent}%</div>
                        <div class="text-muted text-uppercase" style="font-size: 10px; font-weight: 700; letter-spacing: 0.5px;">Success Rate</div>
                    </div>
                </div>

                <!-- Legend Breakdown -->
                <div class="d-flex align-items-center justify-content-center gap-3 mt-3 flex-wrap font-monospace" style="font-size: 14px;">
                    <span class="badge px-3 py-2 rounded-pill" style="background-color: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; font-size: 13px;">
                        <i class="las la-circle" style="color: #10b981;"></i> ${response.data.stats.delivered_percent}% Success
                    </span>
                    <span class="text-muted fw-bold">vs</span>
                    <span class="badge px-3 py-2 rounded-pill" style="background-color: #fff1f2; color: #dc2626; border: 1px solid #fecdd3; font-size: 13px;">
                        <i class="las la-circle" style="color: #ef4444;"></i> ${response.data.stats.returned_percent}% Return
                    </span>
                    <span class="text-muted fw-bold">vs</span>
                    <span class="badge px-3 py-2 rounded-pill" style="background-color: #eef2ff; color: #4f46e5; border: 1px solid #c7d2fe; font-size: 13px;">
                        <i class="las la-circle" style="color: #6366f1;"></i> ${response.data.stats.cancelled_percent}% Cancel
                    </span>
                </div>
            </div>

            <!-- Bottom Section: Customer Order History Table -->
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white" style="border: 1px solid #eef2f6 !important;">
                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                    <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i class="las la-history text-primary fs-4"></i> Customer Order History
                    </h5>
                    <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill font-monospace" style="font-size: 12px;">
                        <i class="las la-box me-1"></i> ${response.data.stats.total_orders} Orders Recorded
                    </span>
                </div>
                <div class="table-responsive">
                    <table class="table crm-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Tracking Code</th>
                                <th>Merchant Name</th>
                                <th>Order Date</th>
                                <th>COD Amount</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${(response.data.parcels || []).map(parcel => {
                                let statusBadge = '';
                                const status = (parcel.status || '').toLowerCase();
                                if (status === 'delivered' || status === 'partially-delivered') {
                                    statusBadge = `<span class="badge-status-delivered"><i class="las la-check-circle"></i> Delivered</span>`;
                                } else if (status.includes('return')) {
                                    statusBadge = `<span class="badge-status-returned"><i class="las la-undo-alt"></i> Returned</span>`;
                                } else if (status === 'cancel' || status === 'cancle') {
                                    statusBadge = `<span class="badge px-3 py-1 rounded-pill" style="background-color: #fee2e2; color: #dc2626; border: 1px solid #fecaca; font-weight: 600;"><i class="las la-times-circle"></i> Cancelled</span>`;
                                } else {
                                    statusBadge = `<span class="badge bg-light text-secondary border px-3 py-1 rounded-pill fw-semibold text-capitalize">${parcel.status || 'Pending'}</span>`;
                                }

                                return `
                                <tr>
                                    <td>
                                        <span class="font-monospace fw-semibold text-primary d-flex align-items-center gap-1">
                                            <i class="las la-barcode fs-5 text-secondary"></i> ${parcel.parcel_no}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-medium text-dark d-flex align-items-center gap-1">
                                            <i class="las la-store text-muted"></i> ${parcel.merchant_name}
                                        </div>
                                    </td>
                                    <td class="text-muted">
                                        <i class="lar la-calendar text-muted me-1"></i> ${parcel.order_date}
                                    </td>
                                    <td>
                                        <span class="fw-bold text-dark">${parcel.cod_amount}</span>
                                    </td>
                                    <td class="text-center">
                                        ${statusBadge}
                                    </td>
                                </tr>`;
                            }).join('')}
                        </tbody>
                    </table>
                </div>
            </div>
            `;

            $('#crmEmptyState').addClass('d-none');
            $('#crmLoadingState').addClass('d-none');
            $('#crmDataContainer').removeClass('d-none').html(html);
          }
          else{
            $('#crmEmptyState').removeClass('d-none');
            $('#crmLoadingState').addClass('d-none');
            $('#crmDataContainer').addClass('d-none').html('');
          }
        }
       })
     })



    });
</script>
@endpush
