<div class="action-card">
    <ul class="d-flex gap-30 justify-content-center align-items-center">
        <li>
            <a id="{{ $parcel->parcel_no }}" data-bs-toggle="{{ $parcel->parcel_no }}"
                data-text="{{ $parcel->parcel_no }}" class="parcel-id-copy  copy-to-clipboard" href="javascript:void(0)"><i
                    class="icon la la-copy"></i>
            </a>
        </li>
        <div class="dropdown">
            <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="las la-ellipsis-v"></i>
            </a>
            <ul class="dropdown-menu">
                <li>
                    <a class="dropdown-item"
                        href="{{ Sentinel::getUser()->user_type == 'merchant' ? route('merchant.parcel.detail', $parcel->id) : route('merchant.staff.parcel.detail', $parcel->id) }}"
                        data-original-title="{{ __('details') }}">
                        {{ __('view') }}
                    </a>
                </li>
                <li>
                    <a class="dropdown-item"
                        href="{{ Sentinel::getUser()->user_type == 'merchant' ? route('merchant.parcel.print', $parcel->id) : route('merchant.staff.parcel.print', $parcel->id) }}"
                        target="_blank" data-original-title="{{ __('print') }}">
                        <span> {{ __('print') }}</span>
                    </a>
                </li>
                @if ($parcel->status == 'pending' || $parcel->status == 'pickup-assigned' || $parcel->status == 're-schedule-pickup')
                    <li>
                        <a href="{{ Sentinel::getUser()->user_type == 'merchant' ? route('merchant.parcel.edit', $parcel->id) : route('merchant.staff.parcel.edit', $parcel->id) }}"
                            class="dropdown-item" data-original-title="{{ __('edit') }}">
                            <span> {{ __('edit') }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:void(0);" class="dropdown-item delete-parcel" data-id="{{ $parcel->id }}"
                            id="delete-parcel" data-bs-toggle="modal" data-bs-target="#parcel-delete">
                            <span> {{ __('delete') }} </span>
                        </a>
                    </li>
                @endif
                @if (
                    $parcel->status == 'received-by-pickup-man' ||
                        $parcel->status == 'received' ||
                        $parcel->status == 'transferred-to-branch' ||
                        $parcel->status == 'transferred-received-by-branch')
                    <li>
                        <a href="javascript:void(0);" class="cancel-parcel dropdown-item" data-id="{{ $parcel->id }}"
                            id="cancel-parcel" data-bs-toggle="modal" data-bs-target="#parcel-cancel">
                            <span> {{ __('cancel') }} </span>
                        </a>
                    </li>
                @endif
                @if ($parcel->status == 'cancel')
                    <li>
                        <a href="javascript:void(0);" class="btn-tooltip parcel-re-request dropdown-item"
                            data-id="{{ $parcel->id }}" id="parcel-re-request" data-bs-toggle="modal"
                            data-bs-target="#re-request-parcel">
                            <span>{{ __('re-reques') }}</span>
                        </a>
                    </li>
                @endif
                {{-- <li>
                    <a href="{{ Sentinel::getUser()->user_type == 'merchant' ? route('merchant.parcel.duplicate', $parcel->id) : route('merchant.staff.parcel.duplicate', $parcel->id) }}"
                        class="dropdown-item" data-original-title="{{ __('duplicate_parcel') }}">
                        <span> {{ __('duplicate') }}</span>
                    </a>
                </li> --}}
            </ul>
        </div>
    </ul>
</div>

{{-- <div class="action-card">
    <div class="d-flex gap-1 justify-content-center align-items-center">
        <div class="d-md-inline mr-1">
            <a href="{{ Sentinel::getUser()->user_type == 'merchant' ? route('merchant.parcel.detail', $parcel->id) : route('merchant.staff.parcel.detail', $parcel->id) }}"
                class="btn btn-sm btn-primary btn-tooltip text-light" data-original-title="{{ __('details') }}">
                <i class="icon las la-eye"></i>
            </a>
        </div>
        <div class="d-md-inline mr-1">
            <a href="{{ Sentinel::getUser()->user_type == 'merchant' ? route('merchant.parcel.print', $parcel->id) : route('merchant.staff.parcel.print', $parcel->id) }}"
                target="_blank" class="btn btn-sm btn-warning btn-tooltip text-light"
                data-original-title="{{ __('print') }}">
                <i class="la la-print"></i>
            </a>
        </div>
        @if ($parcel->status == 'pending' || $parcel->status == 'pickup-assigned' || $parcel->status == 're-schedule-pickup')
            <div class="d-md-inline mr-1">
                <a href="{{ Sentinel::getUser()->user_type == 'merchant' ? route('merchant.parcel.edit', $parcel->id) : route('merchant.staff.parcel.edit', $parcel->id) }}"
                    class="btn btn-sm btn-info btn-tooltip text-light" data-original-title="{{ __('edit') }}">
                    <i class="la la-edit"></i>
                </a>
            </div>
            <div class="d-md-inline mr-1">
                <a href="javascript:void(0);" class="btn-tooltip delete-parcel btn-sm text-light btn btn-danger"
                    data-id="{{ $parcel->id }}" id="delete-parcel" data-bs-toggle="modal"
                    data-bs-target="#parcel-delete"><i class="icon  las la-trash"></i>
                </a>
            </div>
        @endif
        @if ($parcel->status == 'received-by-pickup-man' || $parcel->status == 'received' || $parcel->status == 'transferred-to-branch' || $parcel->status == 'transferred-received-by-branch')
            <div class="d-md-inline mr-1">
                <a href="javascript:void(0);" class="cancel-parcel btn-tooltip btn btn-sm btn-danger text light"
                    data-id="{{ $parcel->id }}" id="cancel-parcel" data-bs-toggle="modal"
                    data-bs-target="#parcel-cancel"><i class="las la-ban"></i></a>
            </div>
        @endif
        @if ($parcel->status == 'cancel')
            <div class="d-md-inline mr-1">
                <a href="javascript:void(0);" class="btn-tooltip parcel-re-request btn btn-sm text-light btn-warning"
                    data-id="{{ $parcel->id }}" id="parcel-re-request" data-bs-toggle="modal"
                    data-bs-target="#re-request-parcel"><i class="las la-deaf"></i></a>
            </div>
        @endif
        <div class="d-md-inline mr-1">
            <a href="{{ Sentinel::getUser()->user_type == 'merchant' ? route('merchant.parcel.duplicate', $parcel->id) : route('merchant.staff.parcel.duplicate', $parcel->id) }}"
                class="btn-tooltip btn btn-sm btn-secondary btn-tooltip text-light"
                data-original-title="{{ __('duplicate_parcel') }}"><i class="icon las la-copy"></i></a>
        </div>
    </div>
</div> --}}
