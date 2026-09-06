<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Merchant Name</th>
            <th>Parcel ID</th>
            <th>Type</th>
            <th>Weight</th>
            <th>Location</th>
            <th>Delivery Charge</th>
            <th>Payable</th>
            <th>Return Charge</th>
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
                <td>{{ $parcel->merchant?->company }}</td>
                <td>{{ $parcel->parcel_no }}</td>
                <td>
                    {{ $parcel->parcel_type === 'sub_urban_area'
            ? 'outside_dhaka'
            : ($parcel->parcel_type === 'inside_city' ? 'inside_dhaka' : $parcel->parcel_type) }}
                </td>

                <td>{{ $parcel->weight }}</td>

                <td>
                    {{ $parcel->location === 'sub_urban_area'
            ? 'outside_dhaka'
            : ($parcel->location === 'inside_city' ? 'inside_dhaka' : $parcel->location) }}
                </td>
                <td>{{ $parcel->total_delivery_charge }}</td>
                <td>{{ $parcel->payable }}</td>
                <td>{{ $parcel->return_charge }}</td>
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
            <td colspan="6">Total</td>
            <td>{{ $parcels->sum('total_delivery_charge') }}</td>
            <td>{{ $parcels->sum('payable') }}</td>
            <td>{{ $parcels->sum('return_charge') }}</td>
            <td>{{ $parcels->sum('price') }}</td>
            <td colspan="7"></td>
        </tr>
    </tbody>
</table>