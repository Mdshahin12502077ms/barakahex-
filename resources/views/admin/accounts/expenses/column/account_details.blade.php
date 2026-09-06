<div>
    @if(!blank($expense->account))
    @if($expense->account->method == 'bank')
        <span>{{__('name')}}: {{$expense->account->account_holder_name}}</span><br>
        <span>{{__('account_no')}}: {{$expense->account->account_no}}</span><br>
        <span>{{__('bank')}}: {{__($expense->account->bank_name)}}</span><br>
        <span>{{__('branch')}}:{{$expense->account->bank_branch}}</span><br>
    @elseif($expense->account->method == 'cash')
        <span>{{__('name')}}: {{$expense->account->user->first_name.' '.$expense->account->user->last_name}}({{__($expense->account->method)}})</span><br>
        <span>{{__('email')}}: {{$expense->account->user->email}}</span><br>
    @else
        <span>{{__('name')}}: {{$expense->account->account_holder_name}}</span><br>
        <span>{{__('number')}}: {{$expense->account->number}}</span><br>
        <span>{{__('account_type')}}: {{__($expense->account->type)}}</span><br>
    @endif
    @endif
</div>
