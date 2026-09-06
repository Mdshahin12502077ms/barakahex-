<div>
    <span>
        {{$fund_transfer->date != "" ? date('M d, Y', strtotime($fund_transfer->date)) : ''}} <br>
        {{date('M d, Y h:i a', strtotime($fund_transfer->created_at))}}
    </span>
</div>
