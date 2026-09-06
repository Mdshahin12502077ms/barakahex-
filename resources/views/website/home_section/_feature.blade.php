<section class="feature__section bg-color">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="section__title-wrapper wow fadeInUp" data-wow-delay=".2s">
                    <div class="section__title text-center">
                        <h4 class="subtitle text-white">{{ __('features') }}</h4>
                        <h2 class="title text-white">{{ setting('feature_section_title', app()->getLocale()) }}</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            @foreach($features as $feature)
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                    <div class="featureBox wow fadeInUp" data-wow-delay=".3s">
                        <div class="featureBox__icon">
                            <img src="{{ getFileLink('37X36', $feature['icon']) }}" alt="about-icon" />
                        </div>
                        <div class="featureBox__content">
                            <h4 class="title">{{ @$feature->language->title }}</h4>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
