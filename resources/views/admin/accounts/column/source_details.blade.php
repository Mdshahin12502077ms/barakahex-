<div class="column-max-width">
    @if($income->source != "")
    {{ __($income->source)  }} <br>
    @endif
    @if($income->delivery_man_id != "")
    {{@$income->deliveryMan->user->first_name.' '.@$income->deliveryMan->user->last_name}}
    @endif
    @isset($income->merchant)
        {{$income->merchant->user->first_name.' '.$income->merchant->user->last_name}}
        ({{$income->merchant->company}})
        @endisset
    @if($income->details)
        <br>{{ __('note').': '. $income->details }}
    @endif
</div>
