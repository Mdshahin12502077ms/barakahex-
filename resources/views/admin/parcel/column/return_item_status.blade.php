
@if($parcel->status=="partially-delivered")
@if(hasPermission('return_item_status_update'))
<select name="return_item_status" class="form-control return_item_status" data-id="{{ $parcel->id }}">
    <option value="pending_at_rider" @if($parcel->return_item_status == 'pending_at_rider') selected @endif>Pending at Rider</option>
    <option value="received_at_hub" @if($parcel->return_item_status == 'received_at_hub') selected @endif>Received at Hub</option>
    <option value="returned_to_merchant" @if($parcel->return_item_status == 'returned_to_merchant') selected @endif>Returned to Merchant</option>
    <option value="received_by_merchant" @if($parcel->return_item_status == 'received_by_merchant') selected @endif>Received by Merchant</option>
</select>
@else
{{ ucwords(str_replace('_', ' ', $parcel->return_item_status ?? 'Pending')) }}
@endif
@endif