<input type="hidden" value="{{$bulk_payment->id}}" id="id">
<div>
    <div class="copy-to-clipboard">
        <input readonly type="text" class="text-info" data-text="{{ __('copied') }}" value="{{$bulk_payment->batch_no}}">
    </div>
</div>
