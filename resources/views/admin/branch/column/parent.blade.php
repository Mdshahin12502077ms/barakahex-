@if($query->parent)
    <div class="d-flex align-items-center" style="font-size: 12px;">
        <i class="las la-sitemap text-primary me-1" style="font-size: 16px;"></i>
        <div>
            <span class="fw-semibold text-dark">{{ $query->parent->name }}</span>
            <br>
            <small class="badge bg-light text-secondary border px-1" style="font-size: 9px;">{{ ucfirst(str_replace('_', ' ', $query->parent->type)) }}</small>
        </div>
    </div>
@else
    <span class="badge bg-light text-muted border" style="font-size: 10px;">👑 Top Level / Root</span>
@endif
