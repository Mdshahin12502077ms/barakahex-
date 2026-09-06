<section class="call__to__action v2 p-0 m-0">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="ctaBox__wrapper v2 wow fadeInUp" data-wow-delay=".2s">
                    <div class="ctaBox__content wow fadeInUp" data-wow-delay=".3s">
                        @if(setting('cta_enable') == 1)
                            <h2 class="ctaBox__title">{{ setting('cta_title', app()->getLocale()) }}</h2>
                            <a href="{{ setting('cta_main_action_btn_url') }}" class="btn btn-primary">{{ setting('cta_main_action_btn_label', app()->getLocale()) }}</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
