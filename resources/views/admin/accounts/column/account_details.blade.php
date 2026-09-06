
<div>
    @if(!blank($income->account))
    @if($income->account->method == 'bank')
        <span>{{__('name')}}: {{$income->account->account_holder_name}}</span><br>
        <span>{{__('account_no')}}: {{$income->account->account_no}}</span><br>
        <span>{{__('bank')}}: {{__($income->account->bank_name)}}</span><br>
        <span>{{__('branch')}}:{{$income->account->bank_branch}}</span><br>
    @elseif($income->account->method == 'cash')
        <span>{{__('name')}}: {{$income->account->user->first_name.' '.$income->account->user->last_name}}({{__($income->account->method)}})</span><br>
        <span>{{__('email')}}: {{$income->account->user->email}}</span><br>
    @else
        <span>{{__('name')}}: {{$income->account->account_holder_name}}</span><br>
        <span>{{__('number')}}: {{$income->account->number}}</span><br>
        <span>{{__('account_type')}}: {{__($income->account->type)}}</span><br>
    @endif
    @endif
</div>
