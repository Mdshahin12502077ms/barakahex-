<div class="setting-check">
    <input type="checkbox" data-id="{{$warehouse->id}}" data-url="{{ route('merchant.warehouse.status') }}" {{ ($warehouse->status == \App\Enums\StatusEnum::ACTIVE) ? 'checked' :'' }} value="warehouse-status/{{$warehouse->id}}"
           id="customSwitch2-{{$warehouse->id}}" class="custom-control-input status-change">
    <label for="customSwitch2-{{$warehouse->id}}"></label>
</div>
