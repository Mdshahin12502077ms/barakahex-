<section class="testimonial__section">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="section__title-wrapper wow fadeInUp" data-wow-delay=".2s">
                    <div class="section__title text-center">
                        <h4 class="subtitle">{{ __('customer_stories') }}</h4>
                        <h2 class="title">{{ setting('testimonial_section_title',  app()->getLocale()) }}</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="swiper testimonial__slider wow fadeInUp" data-wow-delay=".3s">
                    <div class="swiper-wrapper">
                        <!-- Swiper Slide -->
                        @foreach($testimonials as $testimonial)
                            <div class="swiper-slide">
                                <div class="testimonial__item">
                                    <div class="testimonial__avatar">
                                        <div class="avatar">
                                            <img src="{{ getFileLink('96X96', $testimonial->image) }}"
                                                alt="avatar" width="100%" height="100%">
                                        </div>
                                        <div class="avatar__title">
                                            <div class="quote">
                                                <svg width="56" height="42" viewBox="0 0 56 42" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M5.49193 38.5749C2.40859 35.1832 0.558594 31.4832 0.558594 25.3165C0.558594 14.5249 8.26693 4.96654 19.0586 0.0332031L21.8336 4.04154C11.6586 9.59154 9.50026 16.6832 8.8836 21.3082C10.4253 20.3832 12.5836 20.0749 14.7419 20.3832C20.2919 20.9999 24.6086 25.3165 24.6086 31.1749C24.6086 33.9499 23.3753 36.7249 21.5253 38.8832C19.3669 41.0415 16.9003 41.9665 13.8169 41.9665C10.4253 41.9665 7.34193 40.4249 5.49193 38.5749ZM36.3253 38.5749C33.2419 35.1832 31.3919 31.4832 31.3919 25.3165C31.3919 14.5249 39.1003 4.96654 49.8919 0.0332031L52.6669 4.04154C42.4919 9.59154 40.3336 16.6832 39.7169 21.3082C41.2586 20.3832 43.4169 20.0749 45.5753 20.3832C51.1253 20.9999 55.4419 25.3165 55.4419 31.1749C55.4419 33.9499 54.2086 36.7249 52.3586 38.8832C50.5086 41.0415 47.7336 41.9665 44.6503 41.9665C41.2586 41.9665 38.1753 40.4249 36.3253 38.5749Z"
                                                        fill="#B0EACE"
                                                    />
                                                </svg>
                                            </div>
                                            {{ @$testimonial->language->title }}
                                        </div>
                                    </div>
                                    <p class="testimonial__content">
                                        {{ @$testimonial->language->description }}
                                    </p>
                                    <div class="avatar__content">
                                        <h4 class="title">{{ @$testimonial->language->name }}</h4>
                                        <div class="designation">{{ @$testimonial->language->designation }}</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="testimonial__pagination">
                        <div class="swiper-pagination"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
