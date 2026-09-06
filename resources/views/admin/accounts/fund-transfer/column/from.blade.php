<div>
    @if($fund_transfer->fromAccount->method == 'bank')
        <span>{{__('name')}}: {{$fund_transfer->fromAccount->account_holder_name}}</span><br>
        <span>{{__('account_no')}}: {{$fund_transfer->fromAccount->account_no}}</span><br>
        <span>{{__('bank')}}: {{__($fund_transfer->fromAccount->bank_name)}}</span><br>
        <span>{{__('branch')}}: {{$fund_transfer->fromAccount->bank_branch}}</span><br>
    @elseif($fund_transfer->fromAccount->method == 'cash')
        <span>{{__('name')}}: {{$fund_transfer->fromAccount->user->first_name.' '.$fund_transfer->fromAccount->user->last_name}}({{__($fund_transfer->fromAccount->method)}})</span><br>
        <span>{{__('email')}}: {{$fund_transfer->fromAccount->user->email}}</span><br>
    @else
        <span>{{__('name')}}: {{$fund_transfer->fromAccount->account_holder_name}}</span><br>
        <span>{{__('number')}}: {{$fund_transfer->fromAccount->number}}</span><br>
        <span>{{__('account_type')}}: {{__($fund_transfer->fromAccount->type)}}</span><br>
    @endif
</div>
