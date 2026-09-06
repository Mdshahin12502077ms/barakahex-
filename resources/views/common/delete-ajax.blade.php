@push('script')
    <script type="text/javascript">
        function delete_row(route, row_id) {
            var table_row = '#row_' + row_id;
            var url = "{{ url('') }}" + '/admin/' + route + row_id;
            Swal.fire({
                title: '{{ __('are_you_sure') }}',
                text: "{{ __('you_wont_be_able_to_revert_this') }}",
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
                                _method: 'DELETE'
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
                            if (response[1] != 'error') {
                                $(table_row).fadeOut(2000);
                            }
                        })
                        .fail(function(error) {
                            Swal.fire('{{ __('opps') }}...', '{{ __('something_went_wrong_with_ajax') }}',
                                'error');
                        })
                }
            });
        };
    </script>
@endpush
