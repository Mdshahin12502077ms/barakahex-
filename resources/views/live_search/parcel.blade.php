@push('script')
    <script type="text/javascript">
        $('.parcel-live-search').select2({
            placeholder: "{{ __('select_parcel') }}",
            minimumInputLength: 2,
            ajax: {
                type: "GET",
                dataType: 'json',
                url: '{{ route('get-parcel-live') }}',
                data: function (params) {
                    return {
                        q: params.term // search term
                    };
                },
                delay: 250,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                },
                cache: true
            }
        });
    </script>
@endpush
