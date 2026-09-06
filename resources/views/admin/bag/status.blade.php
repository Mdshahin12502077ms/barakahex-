@if($bag->status == 'open')
    <div class="badge badge-primary" style="width: 100px">{{ __('open') }}</div>
@elseif($bag->status == 'in_transit')
    <div class="badge badge-info" style="width: 100px">{{ __('in_transit') }}</div>
@elseif($bag->status == 'received')
    <div class="badge badge-success" style="width: 100px">{{ __('received') }}</div>
@elseif($bag->status == 'closed')
    <div class="badge badge-danger" style="width: 100px">{{ __('closed') }}</div>
@endif
