<div>
    @if ($parcel->status == 'pending')
        <span class="badge {{ config('parcel_status.' . $parcel->status . '.color') }}">{{ __($parcel->status) }}</span><br>
    @elseif($parcel->status == 'deleted')
        <span class="badge {{ config('parcel_status.' . $parcel->status . '.color') }}">{{ __('deleted') }}</span><br>
    @elseif($parcel->status == 'cancel')
        <span class="badge {{ config('parcel_status.' . $parcel->status . '.color') }}">{{ __('cancelled') }}</span><br>
    @elseif($parcel->status == 'pickup-assigned')
        <span
            class="badge {{ config('parcel_status.' . $parcel->status . '.color') }}">{{ __('pickup-assigned') }}</span><br>
    @elseif($parcel->status == 're-schedule-pickup')
        <span
            class="badge {{ config('parcel_status.' . $parcel->status . '.color') }}">{{ __('re-schedule-pickup') }}</span><br>
    @elseif($parcel->status == 'received-by-pickup-man')
        <span
            class="badge {{ config('parcel_status.' . $parcel->status . '.color') }}">{{ __('received-by-pickup-man') }}</span><br>
    @elseif($parcel->status == 'received')
        <span
            class="badge {{ config('parcel_status.' . $parcel->status . '.color') }}">{{ __('received_by_warehouse') }}</span><br>
    @elseif($parcel->status == 'transferred-to-branch')
        <span
            class="badge {{ config('parcel_status.' . $parcel->status . '.color') }}">{{ __('transferred-to-branch') }}</span><br>
    @elseif($parcel->status == 'transferred-received-by-branch')
        <span
            class="badge {{ config('parcel_status.' . $parcel->status . '.color') }}">{{ __('transferred-received-by-branch') }}</span><br>
    @elseif($parcel->status == 'delivery-assigned')
        <span class="badge {{ config('parcel_status.' . $parcel->status . '.color') }}">{{ __('in_transit') }}</span><br>
    @elseif($parcel->status == 're-schedule-delivery')
        <span
            class="badge {{ config('parcel_status.' . $parcel->status . '.color') }}">{{ __('re-schedule-delivery') }}</span><br>
    @elseif($parcel->status == 'returned-to-warehouse')
        @if ($parcel->is_partially_delivered)
            <span
                class="badge {{ config('parcel_status.' . $parcel->status . '.color') }}">{{ __('partially-delivered') }}</span><br>
        @endif
        <span
            class="badge {{ config('parcel_status.' . $parcel->status . '.color') }}">{{ __('returned-to-warehouse') }}</span><br>
    @elseif($parcel->status == 'return-assigned-to-merchant')
        @if ($parcel->is_partially_delivered)
            <span
                class="badge {{ config('parcel_status.' . $parcel->status . '.color') }}">{{ __('partially-delivered') }}</span><br>
        @endif
        <span class="badge text-indigo">{{ __('return-assigned-to-merchant') }}</span><br>
    @elseif($parcel->status == 'partially-delivered')
        <span
            class="badge {{ config('parcel_status.' . $parcel->status . '.color') }}">{{ __('partially-delivered') }}</span><br>
    @elseif($parcel->status == 'delivered')
        <span class="badge {{ config('parcel_status.' . $parcel->status . '.color') }}">{{ __('delivered') }}</span><br>
    @elseif($parcel->status == 'delivered-and-verified')
        <span
            class="badge {{ config('parcel_status.' . $parcel->status . '.color') }}">{{ __('delivered-and-verified') }}</span><br>
    @elseif($parcel->status == 'returned-to-merchant')
        @if ($parcel->is_partially_delivered)
            <span
                class="badge {{ config('parcel_status.' . $parcel->status . '.color') }}">{{ __('partially-delivered') }}</span><br>
        @endif
        <span
            class="badge {{ config('parcel_status.' . $parcel->status . '.color') }}">{{ __('returned-to-merchant') }}</span><br>
    @elseif($parcel->status == 're-request')
        <span class="badge {{ config('parcel_status.' . $parcel->status . '.color') }}">{{ __('re-request') }}</span><br>
    @endif
    <span
        class="badge {{ config('parcel_status.' . $parcel->status . '.color') }}">{{ __($parcel->parcel_type) }}</span><br>

    @if ($parcel->short_url != '')
        <div class="d-inline align-items-center gap-2 link-ellips">
            <div>
                <button type="button" data-text="{{ $parcel->short_url }}"
                    class="copy-to-clipboard btn btn-default text-info mx-0 px-0 border-0">{{__('copy_tracking_url')}}</button>
            </div>
        </div> <br>
    @endif

    <div class="d-lg-none d-flex">
        <div class="tb-odr-btns d-md-inline mr-1">
            <a href="{{ Sentinel::getUser()->user_type == 'merchant' ? route('merchant.parcel.detail', $parcel->id) : route('merchant.staff.parcel.detail', $parcel->id) }}"
                class="btn btn-sm btn-primary btn-tooltip" data-original-title="{{ __('details') }}"><i
                    class="icon las la-eye"></i></a>
        </div>
        <div class="tb-odr-btns d-md-inline mr-1">
            <a href="{{ Sentinel::getUser()->user_type == 'merchant' ? route('merchant.parcel.print', $parcel->id) : route('merchant.staff.parcel.print', $parcel->id) }}"
                target="_blank" class="btn btn-sm btn-warning btn-tooltip" data-original-title="{{ __('print') }}"><i
                    class="icon las la-print"></i></a>
        </div>
        @if ($parcel->status == 'pending' || $parcel->status == 'pickup-assigned' || $parcel->status == 're-schedule-pickup')
            <div class="tb-odr-btns d-md-inline mr-1">
                <a href="{{ Sentinel::getUser()->user_type == 'merchant' ? route('merchant.parcel.edit', $parcel->id) : route('merchant.staff.parcel.edit', $parcel->id) }}"
                    class="btn btn-sm btn-info btn-tooltip" data-original-title="{{ __('edit') }}"><i
                        class="icon la la-edit"></i></a>
            </div>
            <div class="tb-odr-btns d-md-inline mr-1">
                <a href="javascript:void(0);" class="delete-parcel btn btn-sm btn-danger btn-tooltip text-light"
                    data-original-title="{{ __('deleted') }}" id="delete-parcel" data-bs-toggle="modal"
                    data-bs-target="#parcel-delete"><i class="icon  las la-trash"></i></a>
            </div>
        @endif
        @if (
                $parcel->status == 'received-by-pickup-man' ||
                $parcel->status == 'received' ||
                $parcel->status == 'transferred-to-branch' ||
                $parcel->status == 'transferred-received-by-branch'
            )
            <div class="tb-odr-btns d-md-inline mr-1">
                <a href="javascript:void(0);" class="cancel-parcel btn btn-sm btn-danger btn-tooltip textr-light"
                    data-original-title="{{ __('cancel') }}" id="cancel-parcel" data-bs-toggle="modal"
                    data-bs-target="#parcel-cancel"><i class="icon las la-times"></i></a>
            </div>
        @endif

        @if ($parcel->status == 'cancel')
            <div class="tb-odr-btns d-md-inline mr-1">
                <a href="javascript:void(0);" class="parcel-re-request btn btn-sm btn-success btn-tooltip"
                    data-original-title="{{ __('re_request') }}" id="parcel-re-request" data-bs-toggle="modal"
                    data-bs-target="#re-request-parcel"><i class="icon las la-arrow-left-circle"></i></a>
            </div>
        @endif
        <div class="tb-odr-btns d-md-inline mr-1">
            <a href="{{ Sentinel::getUser()->user_type == 'merchant' ? route('merchant.parcel.duplicate', $parcel->id) : route('merchant.staff.parcel.duplicate', $parcel->id) }}"
                class="btn btn-sm btn-secondary btn-tooltip" data-original-title="{{ __('duplicate_parcel') }}"><i
                    class="icon las la-copy"></i></a>
        </div>
    </div>
</div>