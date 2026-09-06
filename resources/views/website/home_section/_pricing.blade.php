<section class="pricing__section pt-0" id="pricing">
    <div class="pricing__wrapper">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="section__title-wrapper wow fadeInUp" data-wow-delay=".2s">
                        <div class="section__title text-center">
                            <h2 class="title">{{ setting('price_title', app()->getLocale()) }}</h2>
                            <p class="desc">{{ setting('price_subtitle', app()->getLocale()) }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="pricing__form wow fadeInUp" data-wow-delay=".3s">
                        <div class="form__title">
                            <h4 class="title">{{ __('calculate_the_charge') }}</h4>
                        </div>
                        <form action="#" method="post" id="contact-form" class="pricing">
                            <div class="flex__item">
                                <div class="input__group">
                                    <label for="parcel_type">{{ __('delivery_type') }}</label>
                                    <select class="form-select form-control parcel_type js-example-basic-single dropdown-select-item" id="parcel_type" aria-label="Default select example">
                                        <option value="">{{ __('select_delivery_type') }}</option>
                                        <option value="same_city">{{ __('same_city') }}</option>
                                        <option value="sub_city">{{ __('sub_city') }}</option>
                                        <option value="sub_urban_area">{{ __('outside_city') }}</option>
                                    </select>
                                </div>
                                <div class="input__group" id="day-group" style="display: none;">
                                    <label for="day">{{ __('day') }}</label>
                                    <select class="form-select form-control js-example-basic-single dropdown-select-item" id="day" aria-label="Default select example">
                                        <option value="">{{ __('select_day') }}</option>
                                        <option value="same_day">{{ __('same_day') }}</option>
                                        <!-- <option value="next_day">{{ __('next_day') }}</option> -->
                                    </select>
                                </div>
                            </div>
                            <div class="flex__item">
                                <div class="input__group">
                                    <label for="fragile">{{ __('fragile_charge') }}</label>
                                    <select class="form-select form-control js-example-basic-single dropdown-select-item" id="fragile" aria-label="Default select example">
                                        <option value="0">{{ __('non_fragile') }}</option>
                                        <option value="1">{{ __('fragile') }}</option>
                                    </select>
                                </div>
                                <div class="input__group" id="packaging-group" style="display: none;">
                                    <label for="packaging">{{ __('packaging') }}</label>
                                    <select class="form-select form-control js-example-basic-single dropdown-select-item" id="packaging" aria-label="Default select example">
                                        <option value="">{{ __('select_packaging') }}</option>
                                        @foreach (settingHelper('package_and_charges') as $package_and_charge)
                                            <option value="{{ $package_and_charge->id }}"
                                                {{ isset($parcel) ? ($parcel->packaging == $package_and_charge->id ? 'selected' : '') : '' }}>
                                                {{ __($package_and_charge->package_type) }}
                                                ({{ format_price($package_and_charge->charge) }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="flex__item">
                                <div class="input__group">
                                    <label for="weight">{{ __('weight') }}</label>
                                    <select class="form-select form-control js-example-basic-single dropdown-select-item" id="weight" aria-label="Default select example">
                                        <option value="">{{ __('select_weight') }}</option>
                                        @foreach ($charges as $charge)
                                            <option value="{{ $charge->weight }}"
                                                {{ $charge->weight == @$parcel->weight ? 'selected' : '' }}>
                                                {{ $charge->weight }} {{ __(setting('default_weight')) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="input__group">
                                    <label for="charge">{{ __('charge') }}</label>
                                    <input type="number" class="form-control" name="number" id="charge" placeholder="00 {{ setting('default_currency') }}" required="" />
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="pricing__content wow fadeInUp" data-wow-delay=".3s">
                        <ul class="content__list">
                            {!! setting('price_description', app()->getLocale()) !!}
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@push('script')
<script>
    $(document).ready(function() {
        $('#parcel_type').change(function() {
            var value = $(this).val();
            if (value === 'same_city') {
                $('#day-group').show();
            } else {
                $('#day-group').hide();
                $('#day').val('');
            }
            sendData();
        });

        $('#day').change(sendData);

        $('.parcel_type').change(sendData);

        $('#fragile').change(function() {
            var value = $(this).val();
            if (value === '1') {
                $('#packaging-group').show();
            } else {
                $('#packaging-group').hide();
                $('#packaging').val('');
            }
            sendData();
        });

        $('#packaging').change(sendData);

        $('#weight').change(sendData);

        $('#charge').change(sendData);

        function sendData() {
            var data = {
                city: $('#parcel_type').val(),
                day: $('#day').val(),
                fragile: $('#fragile').val(),
                packaging: $('#packaging').val(),
                weight: $('#weight').val(),
                charge: $('#charge').val()
            };

            axios.post('charges-details', data)
                .then(function(response) {
                    $('#charge').val(response.data.charge);
                })
                .catch(function(error) {
                    console.error('Error sending data:', error);
                });
        }
    });
</script>
@endpush

