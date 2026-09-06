@if(hasPermission('merchant_update') || hasPermission('merchant_delete') || hasPermission('download_closing_report'))
    <div class="action-card d-flex align-items-center justify-content-center">
        <div class="dropdown">
            <a class="dropdown-toggle" href="#" role="button"
            data-bs-toggle="dropdown" aria-expanded="false">
                <i class="las la-ellipsis-v"></i>
            </a>
            <ul class="dropdown-menu">
                @if(hasPermission('merchant_update'))
                    <li>
                        <a class="dropdown-item" href="{{route('merchant.edit', $merchant->id)}}"> {{__('edit')}}</a>
                    </li>
                @endif
                @if(hasPermission('merchant_delete'))
                    <li><a class="dropdown-item" href="javascript:void(0);"
                        onclick="delete_row('merchant/delete/', {{$merchant->id}})"
                        id="delete-btn">{{__('delete')}}</a>
                    </li>
                @endif
                @if(hasPermission('download_closing_report'))
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.merchant.closing.report', $merchant->id) }}"> {{__('closing_report')}}</a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
@endif
