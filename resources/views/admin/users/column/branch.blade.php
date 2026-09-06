<div class="user-info">
    <span>{{@$query->branch->name}}</span> <br> <span>{{ (isDemoMode() ? '**************' : (  @$query->branch->phone_number ?? ''))}}</span>
</div>
