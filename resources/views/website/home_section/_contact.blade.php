<section class="contact__section" id="contact">
    <div class="container">
        <div class="contact__wrapper wow fadeInUp" data-wow-delay=".2s">
            <div class="row align-items-center column-reverse-md">
                <div class="col-lg-5 col-md-6">
                    <div id="map">
                        <iframe
                            src="{{ setting('contact_map') }}"
                            style="border: 0"
                            allowfullscreen=""
                            loading="lazy"
                        ></iframe>
                    </div>
                </div>
                <div class="col-lg-7 col-md-6">
                    <div class="contact__form" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="200">
                        <div class="form__title text-sm-start">
                            <h4 class="title">{{ setting('contact_title', app()->getLocale()) }}</h4>
                            <p class="desc">{{ setting('contact_subtitle', app()->getLocale()) }}</p>
                        </div>
                        <form id="contact-form" class="contact-form">
                            @csrf
                            <div class="flex__item">
                                <div class="input__group">
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" placeholder="{{ __('name') }}" required />
                                    @if ($errors->has('name'))
                                        <div class="invalid-feedback help-block">
                                            <p>{{ $errors->first('name') }}</p>
                                        </div>
                                    @endif
                                </div>
                                <div class="input__group">
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" placeholder="{{ __('email') }}" required />
                                    @if ($errors->has('email'))
                                        <div class="invalid-feedback help-block">
                                            <p>{{ $errors->first('email') }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="flex__item">
                                <div class="input__group">
                                    <input type="number" class="form-control @error('phone') is-invalid @enderror" name="phone" placeholder="{{ __('phone') }}" required />
                                    @if ($errors->has('phone'))
                                        <div class="invalid-feedback help-block">
                                            <p>{{ $errors->first('phone') }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="input__group">
                                <textarea name="message" class="form-control text__area" placeholder="{{ __('write_something') }}" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary submit__btn">
                                <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <g clip-path="url(#clip0_31_88)">
                                        <path d="M22 9.16667V17.4167C22 19.9467 19.9467 22 17.4167 22H4.58333C2.05333 22 0 19.9467 0 17.4167V7.33333C0 4.80333 2.05333 2.75 4.58333 2.75H11.9167C12.4208 2.75 12.8333 3.1625 12.8333 3.66667C12.8333 4.17083 12.4208 4.58333 11.9167 4.58333H4.58333C3.52917 4.58333 2.62167 5.17917 2.15417 6.04083L9.05667 12.9433C10.1292 14.0158 11.8708 14.0158 12.9433 12.9433L16.445 9.44167C16.8025 9.08417 17.38 9.08417 17.7375 9.44167C18.095 9.79917 18.095 10.3767 17.7375 10.7342L14.2358 14.2358C13.3467 15.125 12.1642 15.5742 10.9908 15.5742C9.8175 15.5742 8.64417 15.125 7.74583 14.2358L1.83333 8.31417V17.4167C1.83333 18.9292 3.07083 20.1667 4.58333 20.1667H17.4167C18.9292 20.1667 20.1667 18.9292 20.1667 17.4167V9.16667C20.1667 8.6625 20.5792 8.25 21.0833 8.25C21.5875 8.25 22 8.6625 22 9.16667ZM14.6667 3.66667C14.6667 1.64083 16.3075 0 18.3333 0C20.3592 0 22 1.64083 22 3.66667C22 5.6925 20.3592 7.33333 18.3333 7.33333C16.3075 7.33333 14.6667 5.6925 14.6667 3.66667ZM16.5 3.66667C16.5 4.675 17.325 5.5 18.3333 5.5C19.3417 5.5 20.1667 4.675 20.1667 3.66667C20.1667 2.65833 19.3417 1.83333 18.3333 1.83333C17.325 1.83333 16.5 2.65833 16.5 3.66667Z" fill="white"/>
                                    </g>
                                </svg>
                                {{ __('submit_now') }}
                                <div class="spinner-border text-white loader d-none"></div>
                            </button>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@push('script')
    <script>
        $(document).ready(function() {
            $('.contact-form').submit(function(event) {
                event.preventDefault();
                var formData = new FormData(this);
                $('.submit__btn .loader').removeClass('d-none');

                $.ajax({
                    type: 'POST',
                    url: '{{ route("contacts-store") }}',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        toastr.success('Submitted successfully.');
                        $('.submit__btn .loader').addClass('d-none');
                        $('.contact-form')[0].reset();
                    },
                    error: function(xhr, status, error) {
                        console.error('Submission failed:', error);
                        toastr.error('Submission failed. Please try again later.');
                        $('.submit__btn .loader').addClass('d-none');
                    }
                });
            });
        });
    </script>
@endpush

