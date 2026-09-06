<div>
    <div class="d-flex align-items-center gap-2 mb-1">
        <strong class="text-dark" style="font-size: 13px;">{{ $query->name }}</strong>
        @php
            $typeConfig = match($query->type) {
                'head_office' => ['class' => 'bg-dark text-white', 'icon' => 'la-building', 'label' => 'Head Office'],
                'hub'         => ['class' => 'bg-primary text-white', 'icon' => 'la-warehouse', 'label' => 'Hub'],
                'sort_center' => ['class' => 'bg-info text-white', 'icon' => 'la-boxes', 'label' => 'Sort Center'],
                default       => ['class' => 'bg-success text-white', 'icon' => 'la-store', 'label' => 'Branch'],
            };
        @endphp
        <span class="badge {{ $typeConfig['class'] }}" style="font-size: 10px; font-weight: 600; padding: 2px 6px;">
            <i class="las {{ $typeConfig['icon'] }}"></i> {{ $typeConfig['label'] }}
        </span>
    </div>
    @if($query->parent)
        <div class="mb-1" style="font-size: 11px;">
            <i class="las la-sitemap text-primary"></i> <span class="text-muted">{{ __('parent') }}:</span> <span class="fw-semibold text-dark">{{ $query->parent->name }}</span>
            <small class="text-muted">({{ ucfirst(str_replace('_', ' ', $query->parent->type)) }})</small>
        </div>
    @endif
    <small class="text-muted"><i class="las la-phone"></i> {{ isDemoMode() ? '**************' : ($query->phone_number ?? 'N/A') }}</small>
</div>
