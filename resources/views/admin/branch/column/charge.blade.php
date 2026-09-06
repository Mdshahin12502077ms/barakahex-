<div>
    <span>{{__('name')}}: {{@$query->user->first_name.' '.@$query->user->last_name}}</span><br>
    <span>{{__('email')}}: {{ isDemoMode() ? '**************' :  @$query->user->email ?? ''}}</span><br>
</div>
