@push('script')
    <script type="text/javascript">
        function delete_row(route, row_id) {

            var table_row = '#row_' + row_id;
            var token = "{{ csrf_token() }}";
            var url = "{{url('')}}"+route+row_id;
            Swal.fire({
                title: '{{ __('are_you_sure') }}',
                text: "{{ __('you_won_t_be_able_to_revert_this') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '{{ __('yes_delete_it') }}'
            }).then((confirmed) => {
                if (confirmed.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            id: row_id,
                            _token: token,
                            _method: 'DELETE'
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: url,
                    })
                        .done(function(response) {
                            Swal.fire(
                                '{{ __('deleted') }}',
                                response,
                                'success'
                            ).then((confirmed) => {
                                location.reload();
                            });
                            $(table_row).fadeOut(2000);

                        })
                        .fail(function(error) {
                            Swal.fire('{{ __('opps') }}...', '{{ __('something_went_wrong_with_ajax') }}', 'error');
                        })
                }
            });
        };
    </script>
@endpush
