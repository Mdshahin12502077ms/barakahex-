@if($payment->type == 'bank')
    {{ __('bank') }}
@elseif($payment->type == 'mfs')
    {{ __('mfs') }}
@elseif($payment->type == 'cash')
    {{ __('cash') }}
@endif
