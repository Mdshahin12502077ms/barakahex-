<div class="user-info">
    <span class="fs-bold">Pickup Fee:</span><span style="font-size: 13px;">{{$delivery_man->pick_up_fee == ""? '0.00':format_price($delivery_man->pick_up_fee)}}</span>
        <br><span class="fs-bold">Delivery Fee:</span><span style="font-size: 13px;">{{$delivery_man->delivery_fee  == ""? '0.00':format_price($delivery_man->delivery_fee)}}
        <br><span class="fs-bold">Return Fee:</span><span style="font-size: 13px;">{{$delivery_man->return_fee  == ""? '0.00':format_price($delivery_man->return_fee)}}
    </span>
</div>
