<!-- Footer Section Start -->
<footer class="footer__section">
    <div class="container">
        <div class="row">
            <div class="col-lg-4">
                <div class="footer__wrapper wow fadeInUp" data-wow-delay=".2s">
                    <div class="footer__widget">
                        <div class="footer__slogun">{!! setting('high_lighted_text',app()->getLocale()) !!}</div>
                        <div class="footer__toplink">
                            <div class="footer__link d-flex align-items-center">
                                <div class="icon">
                                    <img src="{{ static_asset('website') }}/images/email.svg" alt="email" />
                                </div>
                                <a href="mailto:{!! setting('contact_email',app()->getLocale()) !!}">{!! setting('contact_email',app()->getLocale()) !!}</a>
                            </div>
                            <div class="footer__link d-flex align-items-center">
                                <div class="icon">
                                    <img src="{{ static_asset('website') }}/images/phone.svg" alt="phone" />
                                </div>
                                <a href="tel:{!! setting('contact_phone',app()->getLocale()) !!}">{!! setting('contact_phone',app()->getLocale()) !!}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col"></div>
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="footer__wrapper wow fadeInUp" data-wow-delay=".2s">
                    <div class="footer__widget">
                        @if(setting('show_quick_link') == 1)
                            <h4 class="widget__title">{{ setting('quick_link_title',app()->getLocale()) }}</h4>
                            <div class="widget__wrap">
                                <ul class="widget__list">
                                    @if($menu_quick_links && is_array(setting('footer_quick_link_menu')))
                                        @foreach($menu_quick_links as $key => $value)
                                            <li><a href="{{ @$value['url'] == 'javascript:void(0)' ? '#' : @$value['url'] }}">{{ @$value['label'] }}</a></li>
                                        @endforeach
                                    @endif
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col"></div>
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="footer__wrapper wow fadeInUp" data-wow-delay=".2s">
                    <div class="footer__widget">
                        <h4 class="widget__title">{{ __('working_time') }}</h4>
                        <div class="widget__wrap">
                            <ul class="widget__list">
                                <li>{{ setting('working_day', $lang) }}</li>
                                <li>{{ setting('closing_day', $lang) }}</li>
                            </ul>
                            <div class="social__icon">
                                <a href="{{ setting('facebook') }}" target="_blank"><i class="fab fa-facebook-f"></i></a>
                                <a href="{{ setting('twitter') }}" target="_blank"><i class="fab fa-twitter"></i></a>
                                <a href="{{ setting('instagram') }}" target="_blank"><i class="fab fa-instagram"></i></a>
                                <a href="{{ setting('linkedin') }}" target="_blank"><i class="fab fa-linkedin-in"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="footer__bottom">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="footer__content wow fadeInUp" data-wow-delay=".3s">
                        <div class="footer__logo">
                            <a href="{{ route('home') }}">
                                <img class="selected-img" src="{{ setting('light_logo') && @is_file_exists(setting('light_logo')['original_image']) ? get_media(setting('light_logo')['original_image']) : get_media('images/default/logo/logo_light.png') }}" alt="light_logo">
                            </a>
                        </div>
                        <div class="footer__apps">
                            <a href="{{ setting('play_store_link') }}" target="_blank"><img src="{{ static_asset('website') }}/images/google-play.png" alt="apps" /></a>
                            <a href="{{ setting('app_store_link') }}" target="_blank"><img src="{{ static_asset('website') }}/images/app-play.png" alt="apps" /></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer__copyright">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="copyright text-center">
                        <p>
                           {{ setting('copyright_title',$lang)}}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
<!-- Footer Section End -->
