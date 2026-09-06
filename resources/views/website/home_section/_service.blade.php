<section class="service__section" id="service">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="section__title-wrapper wow fadeInUp" data-wow-delay=".2s">
                    <div class="section__title text-center">
                        <h4 class="subtitle">{{ __('what_we_do') }}</h4>
                        <h2 class="title">{{ setting('service_section_title', app()->getLocale()) }}</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            @foreach($services as $service)
                <div class="col-lg-4 col-md-6 col-sm-6">
                    <div class="serviceBox wow fadeInUp" data-wow-delay=".3s">
                        <a href="#" class="serviceBox__thumb">
                            <img src="{{ getFileLink('358X270', $service['image']) }}" alt="service-thumb" />
                        </a>
                        <div class="serviceBox__content">
                            <h4 class="title">
                                <a href="#">{{ @$service->language->title }}</a>
                            </h4>
                            <p class="desc">
                                {{ @$service->language->description }}
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
