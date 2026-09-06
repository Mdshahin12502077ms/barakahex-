<table>
    <thead>
    <tr>
        <th>#</th>
        <th>Date</th>
        <th>Paid To</th>
        <th>Parcel ID</th>
        <th>Amount</th>
    </tr>
    </thead>
    <tbody>
    @foreach($payments as $key => $payment)
        <tr>
            <td>{{ $key+1 }}</td>
            <td>{{$payment->date != "" ? date('M d, Y', strtotime($payment->date)) : ''}}</td>
            <td>
                @if(!blank($payment->account))
                    @if($payment->account->method == 'bank')
                        <span>{{__('name')}}: {{$payment->account->account_holder_name}}</span><br>
                        <span>{{__('account_no')}}: {{$payment->account->account_no}}</span><br>
                        <span>{{__('bank')}}: {{__($payment->account->bank_name)}}</span><br>
                        <span>{{__('branch')}}:{{$payment->account->bank_branch}}</span>
                    @elseif($payment->account->method == 'cash')
                        <span>{{__('name')}}: {{$payment->account->user->first_name.' '.$payment->account->user->last_name}}({{__($payment->account->method)}})</span>
                    @else
                        <span>{{__('name')}}: {{$payment->account->account_holder_name}}</span><br>
                        <span>{{__('number')}}: {{$payment->account->number}}</span><br>
                        <span>{{__('account_type')}}: {{__($payment->account->type)}}</span>
                    @endif
                @endif
            </td>
            <td>{{@$payment->parcel->parcel_no}}</td>
            <td>{{ $payment->amount }}</td>
        </tr>
    @endforeach
    <tr>
        <td colspan="4">Total</td>
        <td>{{ $payments->sum('amount') }}</td>
    </tr>
    </tbody>
</table>
