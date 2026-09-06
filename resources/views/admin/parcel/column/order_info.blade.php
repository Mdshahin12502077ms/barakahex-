<a href="{{ route('admin.parcel.detail', $parcel->id) }}">
    <div>{{ __('id') }}:{{ $parcel->parcel_no }}</div>
    <span class="d-block">{{ __('invno') }}:{{ $parcel->customer_invoice_no }}</span>
    @if (
        $parcel->status != 'pending' &&
            $parcel->status != 'pickup-assigned' &&
            $parcel->status != 're-schedule-pickup' &&
            $parcel->status != 'received-by-pickup-man')
        @if (!blank($parcel->branch))
            <div>{{ __('branch') }}: {{ $parcel->branch->name . ' (' . $parcel->branch->address }})</div>
        @endif
    @endif
    @if ($parcel->status == 'transferred-to-branch')
        @if (!blank($parcel->transferToBranch))
            <div>{{ __('transferring_to') }}:
                {{ @$parcel->transferToBranch->name . ' (' . $parcel->transferToBranch->address }})
            </div>
        @endif
        @if (!blank($parcel->transferDeliveryMan))
            <div>{{ __('transferring_by') }}:
                {{ @$parcel->transferDeliveryMan->user->first_name . ' ' . $parcel->transferDeliveryMan->user->last_name }}
            </div>
        @endif
    @endif
    @if ($parcel->status == 'transferred-received-by-branch')
        @if (!blank($parcel->transferDeliveryMan))
            <div>{{ __('transferred_by') }}:
                {{ @$parcel->transferDeliveryMan->user->first_name . ' ' . $parcel->transferDeliveryMan->user->last_name }}
            </div>
        @endif
    @endif
    {{ $parcel->created_at != '' ? date('M d, Y h:i a', strtotime($parcel->created_at)) : '' }}
    @if ($parcel->parcel_type == 'frozen')
        <div>{{ __('pickup') }}:
            {{ $parcel->pickup_date != '' ? date('M d, Y', strtotime($parcel->pickup_date)) : '' }}
            {{ $parcel->pickup_time != '' ? date('h:i a', strtotime($parcel->pickup_time)) : '' }}</div>
        <div>{{ __('delivery') }}:
            {{ $parcel->delivery_date != '' ? date('M d, Y', strtotime($parcel->delivery_date)) : '' }}
            {{ $parcel->pickup_time != '' ? date('h:i a', strtotime($parcel->pickup_time)) : '' }}</div>
    @else
        <div>{{ __('pickup') }}:
            {{ $parcel->pickup_date != '' ? date('M d, Y', strtotime($parcel->pickup_date)) : '' }}</div>
        <div>{{ __('delivery') }}:
            {{ $parcel->delivery_date != '' ? date('M d, Y', strtotime($parcel->delivery_date)) : '' }}</div>
    @endif
    @if ($parcel->status == 'delivered' || $parcel->status == 'delivered-and-verified')
        <div>{{ __('delivered_at') }}:
            {{ $parcel->event != '' ? date('M d, Y h:i a', strtotime($parcel->event->created_at)) : '' }}</div>

        <div>{{ __('delivered_by') }}:
            {{ @$parcel->deliveryMan->user->first_name . ' ' . @$parcel->deliveryMan->user->last_name }}</div>
    @endif
    @if ($parcel->status == 'received-by-pickup-man')
        <div>{{ __('pickup_by') }}:
            {{ @$parcel->pickupMan->user->first_name . ' ' . @$parcel->pickupMan->user->last_name }}</div>
    @endif
    @if ($parcel->status == 'received')
        <div>{{ __('pickup_by') }}:
            {{ @$parcel->pickupMan->user->first_name . ' ' . @$parcel->pickupMan->user->last_name }}</div>
    @endif
    @if ($parcel->status == 'pickup-assigned' || $parcel->status == 're-schedule-pickup')
        <div>{{ __('pickup_man') }}:
            {{ $parcel->pickupMan != '' ? @$parcel->pickupMan->user->first_name . ' ' . @$parcel->pickupMan->user->last_name : '' }}
        </div>
    @endif
    @if ($parcel->status == 'return-assigned-to-merchant')
        <div>{{ __('returned_at') }}:
            {{ $parcel->event != '' ? date('M d, Y h:i a', strtotime($parcel->event->created_at)) : '' }}</div>

        <div>{{ __('return_delivery_man') }}:
            {{ @$parcel->returnDeliveryMan != '' ? @$parcel->returnDeliveryMan->user->first_name . ' ' . $parcel->returnDeliveryMan->user->last_name : '' }}
        </div>
    @endif
    @if ($parcel->status == 'returned-to-merchant')
        <div>{{ __('returned_at') }}:
            {{ $parcel->returnEvent != '' ? date('M d, Y h:i a', strtotime($parcel->returnEvent->created_at)) : '' }}
        </div>

        <div>{{ __('returned_by') }}:
            {{ $parcel->returnDeliveryMan != '' ? $parcel->returnDeliveryMan->user->first_name . ' ' . $parcel->returnDeliveryMan->user->last_name : '' }}
        </div>
    @endif
    @if ($parcel->status == 'delivery-assigned' || $parcel->status == 're-schedule-delivery')
        <div>{{ __('delivery_man') }}:
            {{ $parcel->deliveryMan != '' ? $parcel->deliveryMan->user->first_name . ' ' . $parcel->deliveryMan->user->last_name : '' }}
        </div>
    @endif
    @if (
        $parcel->status == 'pending' ||
            $parcel->status == 'pickup-assigned' ||
            $parcel->status == 're-schedule-pickup' ||
            $parcel->status == 'received-by-pickup-man')
        @if (!blank($parcel->pickupBranch))
            <div>{{ __('pickup_branch') }}: {{ @$parcel->pickupBranch->name . ' (' . $parcel->pickupBranch->address }})</div>
        @endif
    @endif
    <div> {{ \Carbon\Carbon::parse(@$parcel->created_at)->format('d/m/Y') }}</div>
</a>
