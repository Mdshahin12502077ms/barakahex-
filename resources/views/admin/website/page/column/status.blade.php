
<div class="setting-check  justify-content-end">
    <input type="checkbox" {{ ($page->status == 1) ? 'checked' : '' }} data-id="{{$page->id}}" data-url="{{ route('page.status.change') }}"
           id="customSwitch2-{{$page->id}}" class="status-change">
    <label for="customSwitch2-{{$page->id}}"></label>
</div>
