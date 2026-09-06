
@extends('website.layouts.master')
@section('content')
    @include('website.tracking_section._banner')
    <div id="parcel-events">
        @if ($parcel)
            @include('website.tracking_section._track', ['parcel' => $parcel])
        @elseif ($noParcelFound)
            <section class="tracking__section pt-90">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <div class="tracking__item text-center">
                                <p>{{ __('no_parcel_found_for_the_tracking_number') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endif
    </div>
    @include('website.tracking_section._call_to_action')
@endsection

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

