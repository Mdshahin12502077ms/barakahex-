<span><b>{{__('price')}}:</b>{{ @$parcel->parcel_transaction->selling_price }}</span><br>
<span><b>{{__('collected')}}:</b>{{ @$parcel->parcel_transaction->cash_collection }}</span>
<span><b>{{__('delivery_charge')}}:</b>{{ @$parcel->parcel_transaction->total_delivery_charge }}</span>
{{-- <div><b>{{__('weight_charge')}}:</b>{{ 0 }}</div> --}}
<span><b>{{__('cod')}}:</b>{{ @$parcel->parcel_transaction->cash_collection }}</span>
@if(@$parcel->parcel_transaction->discount)
    <span><b>{{__('discount')}}:</b>{{ @$parcel->parcel_transaction->discount }}</span>
@endif
@if ($parcel->paid_at)
<span><b>{{__('paid_on')}}:</b>{{ $parcel->is_paid ? \Carbon\Carbon::parse($parcel->paid_at)->format('d-F-Y') : 'N/A' }}</span>
@endif
@if ($parcel->payment)
<span><b>{{__('paid_invoice')}}:</b>{{ $parcel->payment ? $parcel->payment->payment_id : 'N/A'}}</span>
@endif
@if (@$parcel->events->where('title','crm-note')
->sortByDesc('id')
->first())
<span><b>{{__('crm_note')}}:</b>
    {{ @$parcel->events->where('title','crm-note')
    ->sortByDesc('id')
    ->first()->note ?? NULL }}
</span>
@endif
@if(@$parcel->parcelPriceChangeApprovals)
    <span class="badge bg-warning">
        <b>{{__('new_amount')}}:</b>{{ @$parcel->parcelPriceChangeApprovals->new_amount }}
    </span>
@endif

