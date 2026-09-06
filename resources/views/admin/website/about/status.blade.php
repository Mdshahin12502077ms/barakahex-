
    <div class="setting-check justify-content-end">
        <input type="checkbox" {{ ($about->status == 1) ? 'checked' : '' }} data-id="{{ $about->id }}" data-url="{{ route('website.about.status.change') }}"
               id="customSwitch2-{{$about->id}}" class="status-change">
        <label for="customSwitch2-{{$about->id}}"></label>
    </div>

