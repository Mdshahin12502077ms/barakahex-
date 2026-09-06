
    <div class="setting-check justify-content-end">
        <input type="checkbox" {{ ($feature->status == 1) ? 'checked' : '' }} data-id="{{ $feature->id }}" data-url="{{ route('website.feature.status.change') }}"
               id="customSwitch2-{{$feature->id}}" class="status-change">
        <label for="customSwitch2-{{$feature->id}}"></label>
    </div>

