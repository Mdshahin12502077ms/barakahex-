@if(hasPermission('country_update'))
    <div class="setting-check">
        <input type="checkbox" class="status-change" data-id="{{$country->id}}" data-url="{{ route('admin.countries.countries-status') }}"
               {{ ($country->status == \App\Enums\StatusEnum::ACTIVE) ? 'checked' : '' }} data-id="{{$country->id}}"
               value="countries-status/{{$country->id}}"
               id="customSwitch2-{{$country->id}}">
        <label for="customSwitch2-{{ $country->id }}"></label>
    </div>
@endif
