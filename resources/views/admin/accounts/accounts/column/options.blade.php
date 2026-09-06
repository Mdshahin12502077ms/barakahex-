<div class="container">
    <div class="row justify-content-center align-items-center">
        <div class="col-md-6">
            <div class="action-card d-flex align-items-center justify-content-center h-100">
                <div class="dropdown d-flex align-items-center">
                    <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="las la-ellipsis-v"></i>
                    </a>
                    <ul class="dropdown-menu">
                        @if(hasPermission('account_update'))
                        <li>
                            <a href="{{route('admin.account.edit', $account->id)}}" class="dropdown-item" href="javascript:void(0);">
                                <span> {{__('edit')}}</span>
                            </a>
                        </li>
                        @endif
                        @if(hasPermission('account_statement'))
                            <li>
                                <a href="{{route('admin.account.statement', $account->id)}}" class="dropdown-item" href="javascript:void(0);">
                                <span> {{__('statement')}}</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>




