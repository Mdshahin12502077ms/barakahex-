@if(hasPermission('deliveryman_update'))
    <div class="setting-check">
        <input type="checkbox" {{ $income->status ? 'checked' :'' }} value="delivery-man/update-status/{{$income->user_id}}"
               id="customSwitch2-{{$income->id}}" class="status-change">
        <label for="customSwitch2-{{$income->id}}"></label>
    </div>
@endif


