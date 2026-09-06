<div class="action-card">
    <ul class="d-flex  justify-content-end">
        <li>
            <a class="dropdown-item" href="{{ route('merchant.products.edit', $product->id ) }}">
                <i class="las la-edit"></i>
            </a>
        </li>
        <li>
            <a class="dropdown-item" href="javascript:void(0);"
               onclick="delete_row('/merchant/products/delete/', {{ $product->id }})" id="delete-btn">
                <i class="las la-trash-alt"></i>
            </a>
        </li>
    </ul>
</div>
