<div>
    @if($fund_transfer->toAccount->method == 'bank')
        <span>{{__($fund_transfer->toAccount->method)}}</span><br>
        <span>{{__('name')}}: {{$fund_transfer->toAccount->account_holder_name}}</span><br>
        <span>{{__('account_no')}}: {{$fund_transfer->toAccount->account_no}}</span><br>
        <span>{{__('bank')}}: {{__($fund_transfer->toAccount->bank_name)}}</span><br>
        <span>{{__('branch')}}:{{$fund_transfer->toAccount->bank_branch}}</span><br>
    @elseif($fund_transfer->toAccount->method == 'cash')
        <span>{{__($fund_transfer->toAccount->method)}}</span><br>
        <span>{{__('name')}}: {{$fund_transfer->toAccount->user->first_name.' '.$fund_transfer->toAccount->user->last_name}}</span><br>
        <span>{{__('email')}}: {{$fund_transfer->toAccount->user->email}}</span><br>
    @else
        <span>{{__($fund_transfer->toAccount->method)}}</span><br>
        <span>{{__('name')}}: {{$fund_transfer->toAccount->account_holder_name}}</span><br>
        <span>{{__('number')}}: {{$fund_transfer->toAccount->number}}</span><br>
        <span>{{__('account_type')}}: {{__($fund_transfer->toAccount->type)}}</span><br>
    @endif
</div>
