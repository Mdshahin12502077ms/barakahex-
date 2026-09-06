<div>
    {{__($expense->details)}}<br>
    @if($expense->source == "withdraw")

        {{__('company')}} : {{@$expense->withdraw->merchant->company}}

    @endif
    @if($expense->parcel_id != "")
        {{__('parcel_no')}} : {{@$expense->parcel->parcel_no}}<br>
        {{__('company')}} : {{@$expense->parcel->merchant->company}}
    @endif

</div>
