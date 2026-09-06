
@if(hasPermission('payment_method_update'))
    <div class="setting-check">
        <input type="checkbox" data-id="{{$payment->id}}" data-url="{{ route('admin.payment-method.update-status') }}" class="status-change" data-id="{{ $payment->id }}"
               value="payment-method-status/{{$payment->id}}"
               {{ $payment->status == \App\Enums\StatusEnum::ACTIVE ? 'checked' : '' }}
               id="customSwitch2-{{$payment->id}}">
        <label for="customSwitch2-{{ $payment->id }}"></label>
    </div>
@endif

