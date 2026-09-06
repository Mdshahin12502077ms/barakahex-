<div class="user-info-panel d-flex gap-12 align-items-center">
    <div class="user-img">
        <img
        src="{{ getFileLink('80X80',  $query->image_id) }}"
        alt="favicon">
    </div>
    <div class="user-info">
        <h4>{{ @$query->first_name . ' ' . @$query->last_name . '(' . @$query->branch->name . ')' }}</h4>
        <span>{{ (isDemoMode() ? '**************' : (  @$query->email ?? '')) }}</span>
        @if(!empty($query->phone_number))
        | <span>{{  (isDemoMode() ? '**************' : ($query->phone_number ?? '')) }}</span>
        @endif
    </div>
</div>
