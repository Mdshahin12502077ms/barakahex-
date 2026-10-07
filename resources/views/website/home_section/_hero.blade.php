<section class="banner__section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="hero__text text-left wow fadeInUp" data-wow-delay=".2s">
                    <h4 class="sub__title">{{ __('quality_security_&_fastest_delivery') }}</h4>
                    <h1 class="title">{!! setting('hero_title',app()->getLocale()) !!}</h1>
                    <p class="desc">{!! setting('hero_subtitle',app()->getLocale()) !!}</p>
                    <div class="track__form">
                        <input type="number" name="parcel_no" id="parcel_no" class="form-control" placeholder="{{ __('enter_your_tracking_number') }}" />
                        <button type="button" id="track-parcel-btn" class="btn btn-primary">
                            <img src="{{ static_asset('website') }}/images/banner/track.png" alt="track" />
                        </button>
                    </div>
                </div>
            </div>

           <!-- Mobile Login Buttons -->
        <div class="row d-flex d-md-none mt-4 mx-0">
            <div class="col-6" style="padding-right: 8px; padding-left: 0;">
                <a href="{{ route('login') }}" class="mobile-login-card card-merchant">
                    <div class="icon-wrapper">
                        <i class="fas fa-home"></i>
                    </div>
                    <div class="card-title-text">Merchant</div>
                    <div class="card-subtitle-text">ব্যবসায়ী লগইন</div>
                </a>
            </div>
            <div class="col-6" style="padding-left: 8px; padding-right: 0;">
                <a href="{{ route('login') }}" class="mobile-login-card card-rider">
                    <div class="icon-wrapper">
                        <i class="fas fa-truck"></i>
                    </div>
                    <div class="card-title-text">Rider</div>
                    <div class="card-subtitle-text">ডেলিভারি ম্যান</div>
                </a>
            </div>
        </div>


            <div class="col-lg-6">
                <div class="hero__figure wow fadeInUp" data-wow-delay=".2s">
                    <img src="{{  getFileLink('577x505',setting('header1_hero_image1')) }}" class="animation__01" alt="banner-image" />
                </div>
            </div>

        </div>



    </div>
</section>
@push('script')
<script>
    $(document).ready(function() {
        $('#track-parcel-btn').on('click', function() {
            var tracking_no = $('#parcel_no').val();
            if(tracking_no) {
                var url = '{{ url(localeRoutePrefix().'/tracking') }}';
                window.location.href = url + '/' + tracking_no;
            } else {
                toastr.error('{{ __('please_enter_a_tracking_number') }}');
            }
        });
    });
</script>

@endpush
