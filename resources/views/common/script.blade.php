@push('script')
    <script type="text/javascript">
        function logout_user_devices(route, id) {
            if (id != '') {
                var url = "{{ url('') }}" + '/admin/' + route + id;
            } else {
                var url = "{{ url('') }}" + route;
            }

            Swal.fire({
                title: '{{ __('are_you_sure') }}',
                text: "{{ __('you_wont_be_able_to_revert_this') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '{{ __('yes_logout') }}'
            }).then((confirmed) => {
                if (confirmed.isConfirmed) {
                    $.ajax({
                            type: 'GET',
                            dataType: 'json',
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
                            );

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
