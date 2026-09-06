@push('script')
    <script type="text/javascript">
        $('.merchant-live-search').select2({
            placeholder: "{{ __('select_merchant') }}",
            minimumInputLength: 2,
            ajax: {
                type: "GET",
                dataType: 'json',
                url: '{{ route('get-merchant-live') }}',
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
        }).on('select2:select', function (e) {
            var data = e.params.data;

            // check if balance exists and amount field exists
            if (data.balance && $('#mamount').length) {
                $('#mamount').attr('max', data.balance); $('#mamount').val(data.balance);
            }
        });
    </script>
@endpush