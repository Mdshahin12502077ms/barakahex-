
    <div class="setting-check justify-content-end">
        <input type="checkbox" {{ ($testimonial->status == 1) ? 'checked' : '' }} data-id="{{ $testimonial->id }}" data-url="{{ route('testimonial.status.change') }}"
               id="customSwitch2-{{$testimonial->id}}" class="status-change">
        <label for="customSwitch2-{{$testimonial->id}}"></label>
    </div>

