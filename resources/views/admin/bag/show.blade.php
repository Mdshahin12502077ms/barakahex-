@extends('backend.layouts.master')
@section('title', __('bag_details'))
@section('mainContent')
    <section class="oftions">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="header-top d-flex justify-content-between align-items-center">
                        <h3 class="section-title">{{ __('bag_details') }} - {{ $bag->bag_no }}</h3>
                        <div class="oftions-content-right mb-12 d-flex gap-2">
                            <a href="{{ route('bag.index') }}" class="d-flex align-items-center btn sg-btn-outline-primary gap-2">
                                <i class="las la-arrow-left"></i>
                                <span>{{ __('back') }}</span>
                            </a>
                            <a href="{{ route('bag.print-manifest', $bag->id) }}" target="_blank" class="d-flex align-items-center btn sg-btn-primary gap-2">
                                <i class="las la-print"></i>
                                <span>{{ __('print_manifest') }}</span>
                            </a>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-4">
                            <div class="bg-white redious-border p-20 p-sm-30 pt-sm-30 mb-4">
                                <h5>{{ __('bag_info') }}</h5>
                                <ul class="list-group list-group-flush mt-3">
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        {{ __('status') }}
                                        <span class="badge badge-{{ $bag->status == 'open' ? 'primary' : ($bag->status == 'closed' ? 'secondary' : ($bag->status == 'in_transit' ? 'info' : 'success')) }}">
                                            {{ __($bag->status) }}
                                        </span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        {{ __('from_branch') }}
                                        <span>{{ $bag->fromBranch->name ?? 'N/A' }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        {{ __('to_branch') }}
                                        <span>{{ $bag->toBranch->name ?? 'N/A' }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        {{ __('total_parcels') }}
                                        <span id="total_parcels_count" class="font-weight-bold">{{ $bag->total_parcels }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        {{ __('max_capacity') }}
                                        <span>{{ $bag->max_capacity }}</span>
                                    </li>
                                    @if($bag->note)
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        {{ __('note') }}
                                        <span class="text-muted small">{{ $bag->note }}</span>
                                    </li>
                                    @endif
                                    @if($bag->dispatched_at)
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        {{ __('dispatched_at') }}
                                        <span class="small">{{ \Carbon\Carbon::parse($bag->dispatched_at)->format('d M Y H:i') }}</span>
                                    </li>
                                    @endif
                                    @if($bag->received_at)
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        {{ __('received_at') }}
                                        <span class="small">{{ \Carbon\Carbon::parse($bag->received_at)->format('d M Y H:i') }}</span>
                                    </li>
                                    @endif
                                </ul>

                                @if($bag->status == 'open')
                                    <div class="mt-4">
                                        <form action="{{ route('bag.close', $bag->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-warning w-100">
                                                <i class="las la-lock"></i> {{ __('close_bag') }}
                                            </button>
                                        </form>
                                    </div>
                                @elseif($bag->status == 'closed')
                                    <div class="mt-4">
                                        <form action="{{ route('bag.dispatch', $bag->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-info w-100">
                                                <i class="las la-truck"></i> {{ __('dispatch_bag') }}
                                            </button>
                                        </form>
                                    </div>
                                @elseif($bag->status == 'in_transit')
                                    <div class="mt-4">
                                        <form action="{{ route('bag.receive', $bag->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-success w-100">
                                                <i class="las la-box-open"></i> {{ __('receive_bag') }}
                                            </button>
                                        </form>
                                    </div>
                                @endif

                                <div class="mt-4 text-center border-top pt-3">
                                    <p class="text-muted small mb-2">{{ __('bag_barcode') }}</p>
                                    <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($bag->bag_no, 'C93', 1.5, 40) }}"
                                         alt="{{ $bag->bag_no }}" class="img-fluid" style="max-height:60px;" />
                                    <p class="mt-1" style="font-family:monospace; font-size:13px; font-weight:bold;">{{ $bag->bag_no }}</p>
                                    <a href="{{ route('bag.barcode', $bag->id) }}" target="_blank"
                                       class="btn btn-sm btn-outline-secondary mt-1">
                                        <i class="las la-barcode"></i> {{ __('print_barcode') }}
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div class="bg-white redious-border p-20 p-sm-30 pt-sm-30">
                                <ul class="nav nav-tabs mb-3" id="bagTabs" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="parcels-tab" data-bs-toggle="tab" href="#tab-parcels" role="tab">
                                            <i class="las la-boxes"></i> {{ __('parcels') }}
                                            <span class="badge badge-primary ms-1">{{ $bag->total_parcels }}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="history-tab" data-bs-toggle="tab" href="#tab-history" role="tab">
                                            <i class="las la-history"></i> {{ __('movement_history') }}
                                        </a>
                                    </li>
                                </ul>

                                <div class="tab-content" id="bagTabsContent">
                                    <div class="tab-pane fade show active" id="tab-parcels" role="tabpanel">
                                        @if($bag->status == 'open')
                                            <div class="mb-4">
                                                <label class="form-label" for="barcode">{{ __('scan_barcode/enter_percel_no') }}</label>
                                                <input type="text" class="form-control" id="barcode" placeholder="{{ __('scan_here') }}" autofocus>
                                                <span id="scan_msg" class="text-danger mt-1 d-block"></span>
                                            </div>
                                        @endif

                                        <h5>{{ __('parcels_in_bag') }}</h5>
                                        <div class="table-responsive mt-3">
                                            <table class="table table-bordered table-striped" id="parcelTable">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('sl') }}</th>
                                                        <th>{{ __('parcel_no') }}</th>
                                                        <th>{{ __('merchant') }}</th>
                                                        <th>{{ __('customer') }}</th>
                                                        <th>{{ __('status') }}</th>
                                                        @if($bag->status == 'open')
                                                            <th class="text-center">{{ __('action') }}</th>
                                                        @endif
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($bag->bagParcels as $index => $bagParcel)
                                                        @if($bagParcel->parcel)
                                                        <tr id="row-{{ $bagParcel->parcel->id }}">
                                                            <td>{{ $index + 1 }}</td>
                                                            <td><a href="{{ route('admin.parcel.detail', $bagParcel->parcel->id) }}" target="_blank">{{ $bagParcel->parcel->parcel_no }}</a></td>
                                                            <td>{{ $bagParcel->parcel->merchant->company ?? 'N/A' }}</td>
                                                            <td>{{ $bagParcel->parcel->customer_name }} <br><small>{{ $bagParcel->parcel->customer_phone_number }}</small></td>
                                                            <td>
                                                                @php
                                                                    $pStatus = $bagParcel->parcel->status;
                                                                    $pColor = match($pStatus) {
                                                                        'delivered', 'delivered-and-verified', 'partially-delivered' => 'success',
                                                                        'cancel', 'deleted' => 'danger',
                                                                        'returned-to-warehouse', 'return-assigned-to-merchant', 'returned-to-merchant' => 'warning',
                                                                        'transferred-to-branch', 'transferred-received-by-branch', 'received', 'received-by-pickup-man' => 'info',
                                                                        'pickup-assigned', 're-schedule-pickup', 'delivery-assigned', 're-schedule-delivery', 're-request' => 'primary',
                                                                        default => 'secondary'
                                                                    };
                                                                @endphp
                                                                <span class="badge badge-{{ $pColor }}" style="font-size:11px;">{{ __($pStatus) }}</span>
                                                            </td>
                                                            @if($bag->status == 'open')
                                                                <td class="text-center">
                                                                    <button type="button" class="btn btn-sm btn-danger remove-parcel" data-id="{{ $bagParcel->parcel->id }}">
                                                                        <i class="las la-trash"></i>
                                                                    </button>
                                                                </td>
                                                            @endif
                                                        </tr>
                                                        @endif
                                                    @endforeach
                                                    @if(count($bag->bagParcels) == 0)
                                                        <tr id="empty-row">
                                                            <td colspan="6" class="text-center">{{ __('no_parcels_found') }}</td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="tab-history" role="tabpanel">
                                        <h5 class="mb-3">{{ __('movement_history') }}</h5>

                                        @if($movements->count() > 0)
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-sm">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>{{ __('parcel_no') }}</th>
                                                            <th>{{ __('from_branch') }}</th>
                                                            <th>{{ __('to_branch') }}</th>
                                                            <th>{{ __('sent_by') }}</th>
                                                            <th>{{ __('received_by') }}</th>
                                                            <th>{{ __('sent_at') }}</th>
                                                            <th>{{ __('received_at') }}</th>
                                                            <th>{{ __('status') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($movements as $movement)
                                                            <tr>
                                                                <td><small class="font-monospace">{{ $movement->tracking_number }}</small></td>
                                                                <td><small>{{ $movement->fromBranch->name ?? 'N/A' }}</small></td>
                                                                <td><small>{{ $movement->toBranch->name ?? 'N/A' }}</small></td>
                                                                <td><small>{{ $movement->sender->first_name ?? 'N/A' }}</small></td>
                                                                <td><small>{{ $movement->receiver->first_name ?? '-' }}</small></td>
                                                                <td><small>{{ $movement->sent_at ? \Carbon\Carbon::parse($movement->sent_at)->format('d M Y H:i') : '-' }}</small></td>
                                                                <td><small>{{ $movement->received_at ? \Carbon\Carbon::parse($movement->received_at)->format('d M Y H:i') : '-' }}</small></td>
                                                                <td>
                                                                    @if($movement->status == 'in_transit')
                                                                        <span class="badge badge-info">{{ __('in_transit') }}</span>
                                                                    @elseif($movement->status == 'received')
                                                                        <span class="badge badge-success">{{ __('received') }}</span>
                                                                    @else
                                                                        <span class="badge badge-warning">{{ $movement->status }}</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>

                                            <div class="mt-4">
                                                <h6 class="mb-3 border-bottom pb-2">{{ __('bag_timeline') }}</h6>
                                                <ul class="list-unstyled" style="padding-left:30px;">
                                                    <li style="position:relative; padding:10px 0 10px 20px; border-left:3px solid #e9ecef;">
                                                        <span style="position:absolute; left:-10px; top:14px; width:18px; height:18px; border-radius:50%; background:#28a745; display:flex; align-items:center; justify-content:center;">
                                                            <i class="las la-plus" style="color:#fff;font-size:10px;"></i>
                                                        </span>
                                                        <strong>{{ __('bag_created') }}</strong><br>
                                                        <small class="text-muted">{{ $bag->created_at->format('d M Y H:i') }} — {{ $bag->creator->first_name ?? '' }}</small>
                                                    </li>
                                                    @if($bag->dispatched_at)
                                                    <li style="position:relative; padding:10px 0 10px 20px; border-left:3px solid #e9ecef;">
                                                        <span style="position:absolute; left:-10px; top:14px; width:18px; height:18px; border-radius:50%; background:#17a2b8; display:flex; align-items:center; justify-content:center;">
                                                            <i class="las la-truck" style="color:#fff;font-size:10px;"></i>
                                                        </span>
                                                        <strong>{{ __('dispatched') }}</strong> → {{ $bag->toBranch->name ?? 'N/A' }}<br>
                                                        <small class="text-muted">{{ \Carbon\Carbon::parse($bag->dispatched_at)->format('d M Y H:i') }}</small>
                                                    </li>
                                                    @endif
                                                    @if($bag->received_at)
                                                    <li style="position:relative; padding:10px 0 10px 20px; border-left:3px solid #e9ecef;">
                                                        <span style="position:absolute; left:-10px; top:14px; width:18px; height:18px; border-radius:50%; background:#007bff; display:flex; align-items:center; justify-content:center;">
                                                            <i class="las la-box-open" style="color:#fff;font-size:10px;"></i>
                                                        </span>
                                                        <strong>{{ __('received') }}</strong> — {{ $bag->toBranch->name ?? 'N/A' }}<br>
                                                        <small class="text-muted">{{ \Carbon\Carbon::parse($bag->received_at)->format('d M Y H:i') }} — {{ $bag->receiver->first_name ?? '' }}</small>
                                                    </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        @else
                                            <div class="text-center py-5 text-muted">
                                                <i class="las la-history" style="font-size:40px;"></i>
                                                <p class="mt-2">{{ __('no_movement_history_yet') }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
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
            let bagId = '{{ $bag->id }}';

            $('#barcode').on('keypress', function (e) {
                if (e.which == 13) {
                    e.preventDefault();
                    let barcode = $(this).val().trim();
                    if (!barcode) return;
                    $('#scan_msg').text('').removeClass('text-danger text-success');

                    $.ajax({
                        url: '{{ route('bag.add-parcel') }}',
                        type: 'POST',
                        data: { _token: '{{ csrf_token() }}', bag_id: bagId, barcode: barcode },
                        success: function (response) {
                            if (response.success) {
                                toastr.success(response.message);
                                $('#barcode').val('');
                                setTimeout(function(){ location.reload(); }, 500);
                            } else {
                                $('#scan_msg').addClass('text-danger').text(response.message);
                                $('#barcode').select();
                            }
                        },
                        error: function (xhr) {
                            if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                                let errors = xhr.responseJSON.errors;
                                let msg = Object.values(errors).map(e => e.join(', ')).join('<br>');
                                $('#scan_msg').addClass('text-danger').html(msg);
                            } else {
                                $('#scan_msg').addClass('text-danger').text('Error adding parcel');
                            }
                            $('#barcode').select();
                        }
                    });
                }
            });

            $(document).on('click', '.remove-parcel', function () {
                let parcelId = $(this).data('id');
                let row = $('#row-' + parcelId);
                if (confirm('{{ __('are_you_sure') }}')) {
                    $.ajax({
                        url: '{{ url("admin/bag/remove-parcel") }}/' + bagId,
                        type: 'POST',
                        data: { _token: '{{ csrf_token() }}', parcel_id: parcelId },
                        success: function (response) {
                            if (response.success) {
                                toastr.success(response.message);
                                row.remove();
                                let count = parseInt($('#total_parcels_count').text());
                                $('#total_parcels_count').text(count - 1);
                                if (count - 1 == 0) {
                                    $('#parcelTable tbody').html('<tr id="empty-row"><td colspan="6" class="text-center">{{ __('no_parcels_found') }}</td></tr>');
                                }
                            } else {
                                toastr.error(response.message);
                            }
                        }
                    });
                }
            });
        });
    </script>
@endpush
