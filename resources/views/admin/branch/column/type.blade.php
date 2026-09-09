@php
    $typeConfig = match($query->type) {
        'head_office' => ['class' => 'bg-dark text-white', 'icon' => 'la-building', 'label' => __('head_office')],
        'hub'         => ['class' => 'bg-primary text-white', 'icon' => 'la-warehouse', 'label' => __('hub')],
        'sort_center' => ['class' => 'bg-info text-white', 'icon' => 'la-boxes', 'label' => __('sort_center')],
        default       => ['class' => 'bg-success text-white', 'icon' => 'la-store', 'label' => __('branch')],
    };
@endphp
<span class="badge {{ $typeConfig['class'] }} d-inline-flex align-items-center gap-1 py-1 px-2" style="font-size: 11px; font-weight: 600; border-radius: 4px;">
    <i class="las {{ $typeConfig['icon'] }}"></i> {{ $typeConfig['label'] }}
</span>
