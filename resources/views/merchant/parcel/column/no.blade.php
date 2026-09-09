@php
    /** @var \App\Models\Parcel $parcel */
    $currentUser = Sentinel::getUser();
    $detailRoute = ($currentUser && $currentUser->user_type == 'merchant_staff')
        ? route('merchant.staff.parcel.detail', $parcel->id)
        : route('merchant.parcel.detail', $parcel->id);

    $createdDate = !empty($parcel->created_at) ? date('d M Y, h:i A', strtotime($parcel->created_at)) : '';
    $returnRider = $parcel->returnDeliveryMan ?? $parcel->deliveryMan;
    $deliveryRider = $parcel->deliveryMan;
    $pickupRider = $parcel->pickupMan;
@endphp

<div class="parcel-no-col">
    <a href="{{ $detailRoute }}" class="fw-bold text-primary font-14">
        #{{ $parcel->parcel_no }}
    </a>

    @if (!empty($parcel->customer_invoice_no))
        <div class="small text-muted">
            <span>{{ __('invoice_no') }}:</span> <span class="fw-semibold text-dark">{{ $parcel->customer_invoice_no }}</span>
        </div>
    @endif

    @if (!empty($createdDate))
        <div class="small text-secondary mt-1">
            <i class="las la-calendar text-muted"></i> {{ $createdDate }}
        </div>
    @endif

    @if (!empty($parcel->destination_branch_id) && $parcel->destinationBranch)
        <div class="small mt-1">
            <span class="badge badge-info bg-info text-white" style="font-size: 11px;">
                <i class="las la-warehouse"></i> {{ __('target_branch') }}: {{ $parcel->destinationBranch->name }}
            </span>
        </div>
    @endif

    {{-- Return Rider Info --}}
    @if (($parcel->status == 'returned-to-merchant' || $parcel->status == 'return-assigned-to-merchant') && $returnRider && $returnRider->user)
        <div class="small text-danger mt-1 p-1 rounded bg-light border border-danger-subtle d-inline-block">
            <i class="las la-undo-alt fw-bold"></i>
            <strong>{{ __('returned_by') }}:</strong>
            <span class="text-dark fw-semibold">{{ $returnRider->user->first_name }} {{ $returnRider->user->last_name }}</span>
            @php $riderPhone = $returnRider->user->phone_number ?: $returnRider->phone_number; @endphp
            @if (!empty($riderPhone))
                <span class="text-muted">({{ $riderPhone }})</span>
            @endif
        </div>

    {{-- Delivery Rider Info --}}
    @elseif (($parcel->status == 'delivered' || $parcel->status == 'delivered-and-verified') && $deliveryRider && $deliveryRider->user)
        <div class="small text-success mt-1">
            <i class="las la-motorcycle"></i>
            <strong>{{ __('delivered_by') }}:</strong>
            <span class="text-dark">{{ $deliveryRider->user->first_name }} {{ $deliveryRider->user->last_name }}</span>
        </div>

    {{-- In Transit / Delivery Assigned --}}
    @elseif (($parcel->status == 'delivery-assigned' || $parcel->status == 're-schedule-delivery') && $deliveryRider && $deliveryRider->user)
        <div class="small text-info mt-1">
            <i class="las la-shipping-fast"></i>
            <strong>{{ __('rider') }}:</strong>
            <span class="text-dark">{{ $deliveryRider->user->first_name }} {{ $deliveryRider->user->last_name }}</span>
        </div>

    {{-- Pickup Man Info --}}
    @elseif (($parcel->status == 'pickup-assigned' || $parcel->status == 'received-by-pickup-man' || $parcel->status == 're-schedule-pickup') && $pickupRider && $pickupRider->user)
        <div class="small text-secondary mt-1">
            <i class="las la-people-carry"></i>
            <strong>{{ __('pickup_man') }}:</strong>
            <span>{{ $pickupRider->user->first_name }} {{ $pickupRider->user->last_name }}</span>
        </div>
    @endif
</div>