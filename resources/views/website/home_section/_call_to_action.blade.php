<section class="call__to__action v2 p-0">
    <div class="container">
        <div class="row g-4">
            {{-- Card 1: Merchant / Business --}}
            <div class="col-lg-6 col-md-12">
                <div class="ctaBox__wrapper v2 wow fadeInUp h-100 d-flex flex-column justify-content-center" data-wow-delay=".2s">
                    <div class="ctaBox__content wow fadeInUp" data-wow-delay=".3s">
                        @if(setting('cta_enable') == 1)
                            <h2 class="ctaBox__title">{{ setting('cta_title', app()->getLocale()) }}</h2>
                            <a href="{{ setting('cta_main_action_btn_url') }}" class="btn btn-primary d-inline-flex align-items-center gap-2">
                                <i class="fas fa-store"></i>
                                <span>{{ setting('cta_main_action_btn_label', app()->getLocale()) }}</span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Card 2: Rider / Delivery Hero --}}
            <div class="col-lg-6 col-md-12">
                <div class="ctaBox__wrapper v2 wow fadeInUp h-100 d-flex flex-column justify-content-center" 
                     data-wow-delay=".4s" 
                     style="background: linear-gradient(135deg, #1a2238 0%, #0d1322 100%) !important; border: 1px solid rgba(255, 255, 255, 0.1);">
                    <div class="ctaBox__content wow fadeInUp" data-wow-delay=".5s">
                        <h2 class="ctaBox__title" style="color: #ffffff !important;">
                            {{ __('Deliver & Earn With Our Fast Logistic Network') }}
                        </h2>
                        <a href="{{ route('login') }}" 
                           class="btn d-inline-flex align-items-center gap-2 fw-bold"
                           style="background-color: #ff9800; color: #111827; border: none; padding: 12px 28px; border-radius: 8px; transition: all 0.3s ease;">
                            <i class="fas fa-motorcycle font-18"></i>
                            <span>{{ __('Login as Rider') }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
