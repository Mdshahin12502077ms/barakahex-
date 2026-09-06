
    <div class="setting-check justify-content-end">
        <input type="checkbox" {{ ($news_and_event->status == 1) ? 'checked' : '' }} data-id="{{ $news_and_event->id }}" data-url="{{ route('website.news-and-event.status.change') }}"
               id="customSwitch2-{{$news_and_event->id}}" class="status-change">
        <label for="customSwitch2-{{$news_and_event->id}}"></label>
    </div>

