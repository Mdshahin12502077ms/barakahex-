
<div class="setting-check justify-content-end">
    <input type="checkbox" {{ ($partner_logo->status == 1) ? 'checked' : '' }} data-id="{{ $partner_logo->id }}" data-url="{{ route('partner-logo-status.change') }}"
           id="customSwitch2-{{$partner_logo->id}}" class="status-change">
    <label for="customSwitch2-{{$partner_logo->id}}"></label>
</div>
