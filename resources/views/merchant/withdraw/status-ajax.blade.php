@push('script')
    <script type="text/javascript">
        function changeWithdrawStatus(route, row_id, status) {

            var token = "{{ csrf_token() }}";
            var url = "{{url('')}}"+route+row_id+'/'+status;

            var text = "{{ __('this_item_will_be_cancelled') }}";
            var confirmButtonText = '{{ __('yes_cancel_it') }}';
            Swal.fire({
                title: '{{ __('are_you_sure') }}',
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: confirmButtonText
            }).then((confirmed) => {
                if (confirmed.isConfirmed) {
                    $.ajax({
                        type: 'GET',
                        dataType: 'json',
                        data: {
                            id: row_id,
                            _token: token
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: url,
                    })
                        .done(function(response) {
                            Swal.fire(
                                response[2],
                                response[0],
                                response[1]
                            ).then((confirmed) => {
                                location.reload();
                            });

                        })
                        .fail(function(error) {
                            Swal.fire('{{ __('opps') }}...', '{{ __('something_went_wrong_with_ajax') }}', 'error');
                        })
                }
            });
        };
    </script>
@endpush
