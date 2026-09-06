<ul class="nav pb-12 mb-20" id="pills-tab" role="tablist">
    <li class="nav-item" role="presentation">
        <a href="{{ route('footer.primary-content') }}" class="nav-link ps-0 {{ request()->routeIs('footer.primary-content') ? 'active' : '' }}">
            <span>{{ __('primary_content') }}</span>
        </a>
    </li>
    <li class="nav-item" role="presentation">
        <a href="{{ route('footer.quick-links') }}" class="nav-link ps-0 {{ request()->routeIs('footer.quick-links') ? 'active' : '' }}">
            <span>{{ __('quick_links') }}</span>
        </a>
    </li>

    <li class="nav-item" role="presentation">
        <a href="{{ route('footer.app') }}" class="nav-link ps-0 {{ request()->routeIs('footer.app') ? 'active' : '' }}">
            <span>{{ __('app') }}</span>
        </a>
    </li>
    <li class="nav-item" role="presentation">
        <a href="{{ route('footer.copyright') }}" class="nav-link ps-0 {{ request()->routeIs('footer.copyright') ? 'active' : '' }}">
            <span>{{ __('copyright') }}</span>
        </a>
    </li>
</ul>
