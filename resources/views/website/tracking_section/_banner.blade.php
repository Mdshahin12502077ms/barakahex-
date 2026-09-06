<!-- Breadcrumb Start -->
<div class="breadcrumb__area pb-100">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="text-center">
                    <div class="track__form mt-0">
                        <input type="number" name="parcel_no" id="parcel_no" class="form-control" placeholder="{{ __('enter_your_tracking_number') }}" />
                        <button type="button" id="track-parcel-btn" class="btn btn-primary">
                            <img src="{{ static_asset('website') }}//images/banner/track.png" alt="track" />
                            {{setting('hero_main_action_btn_label',app()->getLocale())}}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Breadcrumb End -->
