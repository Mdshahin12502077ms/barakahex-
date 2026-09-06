<div class="action-card">
    <ul class="d-flex  justify-content-center">
        <li>
            <a class="dropdown-item" href="{{ route('merchant.warehouse.edit', $warehouse->id ) }}">
                <i class="las la-edit"></i>
            </a>
        </li>
        <li>
            <a class="dropdown-item" href="javascript:void(0);"
               onclick="delete_row('/merchant/warehouse/delete/', {{ $warehouse->id }})" id="delete-btn">
                <i class="las la-trash-alt"></i>
            </a>
        </li>
    </ul>
</div>
