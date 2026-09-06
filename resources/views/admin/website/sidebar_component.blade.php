<div class="col-xxl-3 col-lg-4 col-md-4">
	<h3 class="section-title"{{ __('theme_option') }}></h3>
	<div class="bg-white redious-border py-3 py-sm-30 mb-30">
		<div class="email-tamplate-sidenav">
			<ul class="default-sidenav">
                @if (hasPermission('website.themes'))
                    <li>
                        <a href="{{ route('admin.theme.options') }}"
                            class="{{ request()->routeIs('admin.theme.options') ? 'active' : '' }}">
                            <span class="icon"><i class="lab la-themeisle"></i></span>
                            <span>{{ __('theme_options') }}</span>
                        </a>
                    </li>
                @endif
                @if (hasPermission('website.menu'))
                    <li>
                        <a href="{{ route('admin.menu') }}" class="{{ request()->routeIs('admin.menu') || request()->routeIs('admin.menu') || request()->routeIs('website.menu') ? 'active' : '' }}">
                            <span class="icon"><i class="las la-heading"></i></span>
                            <span>{{ __('menu') }}</span>
                        </a>
                    </li>
                @endif
                @if (hasPermission('section.title'))
                    <li>
                        <a href="{{ route('admin.section_title_subtitle') }}"
                           class="{{ request()->routeIs('admin.section_title_subtitle') ? 'active' : '' }}">
                            <span class="icon"><i class="las la-chevron-circle-down"></i></span>
                            <span>{{ __('title_subtitle_section') }}</span>
                        </a>
                    </li>
                @endif
                @if (hasPermission('hero.section'))
                    <li>
                        <a href="{{ route('admin.hero.section') }}"
                            class="{{ request()->routeIs('admin.hero.section') ? 'active' : '' }}">
                            <span class="icon"><i class="las la-hand-point-up"></i></span>
                            <span>{{ __('hero_section') }}</span>
                        </a>
                    </li>
                @endif
                @if (hasPermission('partner_logo.index'))
                    <li>
                        <a href="{{ route('partner-logo.index') }}"
                            class="{{ request()->routeIs(['partner-logo.index', 'partner-logo.create', 'partner-logo.edit']) ? 'active' : '' }}">
                            <span class="icon"><i class="las la-user-friends"></i></span>
                            <span>{{ __('partner_logo') }}</span>
                        </a>
                    </li>
                @endif
                @if (hasPermission('news_and_event.index'))
                    <li>
                        <a href="{{ route('news-and-events.index') }}"
                            class="{{ request()->routeIs(['news-and-events.index', 'news-and-events.edit', 'news-and-events.create']) ? 'active' : '' }}">
                            <span class="icon"><i class="las la-blog"></i></span>
                            <span>{{ __('news_and_event') }}</span>
                        </a>
                    </li>
                @endif
                @if (hasPermission('about.index'))
                    <li>
                        <a href="{{ route('abouts.index') }}"
                            class="{{ request()->routeIs(['abouts.index', 'abouts.create', 'abouts.edit']) ? 'active' : '' }}">
                            <span class="icon"><i class="las la-address-card"></i></span>
                            <span>{{ __('about') }}</span>
                        </a>
                    </li>
                @endif
                @if (hasPermission('service.index'))
                    <li>
                        <a href="{{ route('services.index') }}"
                            class="{{ request()->routeIs(['services.index', 'services.create', 'services.edit']) ? 'active' : '' }}">
                            <span class="icon"><i class="lab la-servicestack"></i></span>
                            <span>{{ __('service') }}</span>
                        </a>
                    </li>
                @endif
                @if (hasPermission('feature.index'))
                    <li>
                        <a href="{{ route('features.index') }}"
                            class="{{ request()->routeIs(['features.index', 'features.create', 'features.edit']) ? 'active' : '' }}">
                            <span class="icon"><i class="las la-chevron-circle-right"></i></span>
                            <span>{{ __('feature') }}</span>
                        </a>
                    </li>
                @endif
                @if (hasPermission('statistic.index'))
                    <li>
                        <a href="{{ route('admin.statistic') }}"
                            class="{{ request()->routeIs(['admin.statistic']) ? 'active' : '' }}">
                            <span class="icon"><i class="las la-chart-pie"></i></span>
                            <span>{{ __('statistic') }}</span>
                        </a>
                    </li>
                @endif
                @if (hasPermission('price.section'))
                    <li>
                        <a href="{{ route('admin.pricing.section') }}"
                            class="{{ request()->routeIs('admin.pricing.section') ? 'active' : '' }}">
                            <span class="icon"><i class="las la-money-bill"></i></span>
                            <span>{{ __('pricing') }}</span>
                        </a>
                    </li>
                @endif
                @if (hasPermission('testimonial.index'))
                    <li>
                        <a href="{{ route('testimonials.index') }}"
                            class="{{ request()->routeIs(['testimonials.index', 'testimonials.create', 'testimonials.edit']) ? 'active' : '' }}">
                            <span class="icon"><i class="las la-address-book"></i></span>
                            <span>{{ __('testimonial') }}</span>
                        </a>
                    </li>
                @endif
                @if (hasPermission('website.cta'))
                    <li>
                        <a href="{{ route('admin.cta') }}"
                            class="{{ request()->routeIs('admin.cta') ? 'active' : '' }}">
                            <span class="icon"><i class="lab la-gripfire"></i></span>
                            <span>{{ __('cta') }}</span>
                        </a>
                    </li>
                @endif
                <li>
                    <a href="{{ route('admin.contact.section') }}"
                        class="{{ request()->routeIs('admin.contact.section') ? 'active' : '' }}">
                        <span class="icon"><i class="las la-paste"></i></span>
                        <span>{{ __('contact') }}</span>
                    </a>
                </li>
                @if (hasPermission('footer.content'))
                    <li>
                        <a href="{{ route('footer.content') }}"
                            class="@if(request()->routeIs('footer.primary-content') || request()->routeIs('footer.primary-content') || request()->routeIs('footer.newsletter-settings') || request()->routeIs('footer.useful-links') || request()->routeIs('footer.resource-links') || request()->routeIs('footer.quick-links') || request()->routeIs('footer.apps-links') || request()->routeIs('footer.payment-banner-settings') || request()->routeIs('footer.copyright')) active @endif">
                            <span class="icon"><i class="las la-memory"></i></span>
                            <span>{{ __('footer_content') }}</span>
                        </a>
                    </li>
                @endif
                @if (hasPermission('website_setting.seo'))
                    <li>
                        <a href="{{ route('website.seo') }}"
                            class="{{ request()->routeIs('website.seo') ? 'active' : '' }}">
                            <span class="icon"><i class="las la-bullhorn"></i></span>
                            <span>{{ __('website_seo') }}</span>
                        </a>
                    </li>
                @endif
                @if (hasPermission('website_setting.custom_js'))
                    <li>
                        <a href="{{ route('custom.js') }}"
                            class="{{ request()->routeIs('custom.js') ? 'active' : '' }}">
                            <span class="icon"><i class="lab la-js-square"></i></span>
                            <span>{{ __('custom_js') }}</span>
                        </a>
                    </li>
                @endif
                @if (hasPermission('website_setting.custom_css'))
                    <li>
                        <a href="{{ route('custom.css') }}"
                            class="{{ request()->routeIs('custom.css') ? 'active' : '' }}">
                            <span class="icon"><i class="lab la-css3-alt"></i></span>
                            <span>{{ __('custom_css') }}</span>
                        </a>
                    </li>
                @endif
                @if (hasPermission('website_setting.google_setup'))
                    <li>
                        <a href="{{ route('google.setup') }}"
                            class="{{ request()->routeIs('google.setup') ? 'active' : '' }}">
                            <span class="icon"><i class="lab la-google"></i></span>
                            <span>{{ __('google_setup') }}</span>
                        </a>
                    </li>
                @endif
                @if (hasPermission('website_setting.fb_pixel'))
                    <li>
                        <a href="{{ route('fb.pixel') }}" class="{{ request()->routeIs('fb.pixel') ? 'active' : '' }}">
                            <span class="icon"><i class="lab la-facebook-square"></i></span>
                            <span>{{ __('fb_pixel') }}</span>
                        </a>
                    </li>
                @endif
			</ul>
		</div>
	</div>
</div>
