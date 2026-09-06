
    <div class="setting-check justify-content-end">
        <input type="checkbox" {{ ($service->status == 1) ? 'checked' : '' }} data-id="{{ $service->id }}" data-url="{{ route('website.service.status.change') }}"
               id="customSwitch2-{{$service->id}}" class="status-change">
        <label for="customSwitch2-{{$service->id}}"></label>
    </div>

