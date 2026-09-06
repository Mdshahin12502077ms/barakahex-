<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Parcel ID</th>
            <th>Type</th>
            <th>Weight</th>
            <th>Location</th>
            @if($type != 'returned_parcels')
                <th>Delivery Charge</th>
            @endif
            @if($type != 'returned_parcels')
                <th>Payable</th>
            @endif
            @if($type != 'delivered_parcels')
                <th>Return Charge</th>
            @endif
            <th>COD</th>
            <th>Customer Name</th>
            <th>Invoice No</th>
            <th>Phone</th>
            <th>Address</th>
            <th>Status</th>
            <th>Selling Price</th>
            <th>Created Date</th>
        </tr>
    </thead>
    <tbody>
        @foreach($parcels as $key => $parcel)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $parcel->parcel_no }}</td>
                <td>{{ $parcel->parcel_type }}</td>
                <td>{{ $parcel->weight }}</td>
                <td>{{ $parcel->location }}</td>
                @if($type != 'returned_parcels')
                    <td>{{ $parcel->total_delivery_charge }}</td>
                @endif
                @if($type != 'returned_parcels')
                    <td>{{ $parcel->payable }}</td>
                @endif
                @if($type != 'delivered_parcels')
                    <td>{{ $parcel->return_charge }}</td>
                @endif
                <td>{{ $parcel->price }}</td>
                <td>{{ $parcel->customer_name }}</td>
                <td>{{ $parcel->customer_invoice_no }}</td>
                <td>{{ $parcel->customer_phone_number }}</td>
                <td>{{ $parcel->customer_address }}</td>
                <td>{{ $parcel->status }}</td>
                <td>{{ $parcel->selling_price }}</td>
                <td>{{ $parcel->created_at }}</td>
            </tr>
        @endforeach
        <tr>
            <td colspan="5">Total</td>
            @if($type != 'returned_parcels')
                <td>{{ $parcels->sum('total_delivery_charge') }}</td>
            @endif
            @if($type != 'returned_parcels')
                <td>{{ $parcels->sum('payable') }}</td>
            @endif
            @if($type != 'delivered_parcels')
                <td>{{ $parcels->sum('return_charge') }}</td>
            @endif
            <td>{{ $parcels->sum('price') }}</td>
            <td colspan="7"></td>
        </tr>
    </tbody>
</table>