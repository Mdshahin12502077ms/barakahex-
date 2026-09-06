<section class="about__section p-0" id="about">
    <div class="about__wrapper">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="section__title-wrapper flex-item wow fadeInUp" data-wow-delay=".2s">
                        <div class="section__title v2">
                            <h4 class="subtitle">{{ __('about_company') }}</h4>
                            <h2 class="title">{{ setting('about_section_title', app()->getLocale()) }}</h2>
                        </div>
                        <div class="section__title v2">
                            <p class="desc">{{ setting('about_section_subtitle', app()->getLocale()) }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row align-items-center">
                <div class="col-lg-6 col-md-6">
                    <div class="about__figure wow fadeInUp" data-wow-delay=".3s">
                        <img src="{{ getFileLink('526X617', setting('about_image')) }}" alt="about-thumb" />
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="aboutBox__wrapper wow fadeInUp" data-wow-delay=".3s">
                        <!-- About Icon Box -->
                        @foreach($abouts as $about)
                            <div class="aboutIcon__box">
                                <div class="icon">
                                    <img src="{{ getFileLink('44X44', $about['icon']) }}" alt="about-icon" />
                                </div>
                                <div class="content">
                                    <h4 class="title">{{ @$about->language->title }}</h4>
                                    <p class="desc">{{ @$about->language->description }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
