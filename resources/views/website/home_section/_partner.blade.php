<section class="partner__section pt-70 pb-0">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="partner__title wow fadeInUp" data-wow-delay=".2s">
                    <h2 class="title text-center">{{ setting('partner_logo_section_title', app()->getLocale()) }}</h2>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="partner__wrapper wow fadeInUp" data-wow-delay=".4s">
                    <div class="swiper partner__slider">
                        <div class="swiper-wrapper">
                            @foreach($partner_logos as $key=>$partner_logo)
                                <div class="swiper-slide">
                                    <div class="partner__image">
                                        
                                        <img style="width: 110px" src="{{ getFileLink('80X31', $partner_logo['image']) }}" alt="partner" />
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
