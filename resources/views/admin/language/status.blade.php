@if(hasPermission('language_update'))
    <div class="setting-check">
        <input type="checkbox" data-id="{{$language->id}}" data-url="{{ route('admin.languages.language-status') }}" class="status-change" data-id="{{ $language->id }}"
               value="language-status/{{$language->id}}"
               {{ $language->status == \App\Enums\StatusEnum::ACTIVE ? 'checked' : '' }}
               id="customSwitch2-{{$language->id}}">
        <label for="customSwitch2-{{ $language->id }}"></label>
    </div>
@endif
