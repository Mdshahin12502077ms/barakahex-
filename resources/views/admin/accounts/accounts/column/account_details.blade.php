<div>
    @if(hasPermission('account_statement'))
    <a href="{{route('admin.account.statement', $account->id)}}">
    @endif

        <div class="bank-details">
            @if($account->method == 'bank')
                <span>{{__('name')}}: {{$account->account_holder_name}}</span><br>
                <span>{{__('account_no')}}: {{$account->account_no}}</span><br>
                <span>{{__('bank')}}: {{__($account->bank_name)}}</span><br>
                <span>{{__('branch')}}:{{$account->bank_branch}}</span><br>
            @elseif($account->method == 'cash')
                <span>{{__('name')}}: {{$account->user->first_name.' '.$account->user->last_name}}</span><br>
                <span>{{__('email')}}: {{$account->user->email}}</span><br>
            @else
                <span>{{__('name')}}: {{$account->account_holder_name}}</span><br>
                <span>{{__('number')}}: {{$account->number}}</span><br>
                <span>{{__('account_type')}}: {{__($account->type)}}</span><br>
            @endif
        </div>
    @if(hasPermission('account_statement'))
    </a>
    @endif
</div>
