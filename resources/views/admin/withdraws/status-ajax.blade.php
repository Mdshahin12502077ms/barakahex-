@push('script')
    <script type="text/javascript">
        function changeWithdrawStatus(route, row_id, status) {

            var token = "{{ csrf_token() }}";
            var url = "{{url('')}}"+route+row_id+'/'+status;

            var text = "{{ __('this_item_will_be_rejected') }}";
            var confirmButtonText = '{{ __('yes_reject_it') }}';
            if(status == 'processed'){
                var text = "{{ __('this_item_will_be_processed') }}";
                var confirmButtonText = '{{ __('yes_processed_it') }}';
            }
            if(status == 'approved'){
                var text = "{{ __('this_item_will_be_approved') }}";
                var confirmButtonText = '{{ __('yes_approve_it') }}';
            }
            Swal.fire({
                title: '{{ __('are_you_sure') }}',
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: confirmButtonText
            }).then((confirmed) => {
                if (confirmed.isConfirmed) {
                    $.ajax({
                        type: 'POST',
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

        function approveWithdraw(route, row_id){
            var token = "{{ csrf_token() }}";
            var url = "{{url('')}}"+route+row_id+'/approved';

            $.ajax({
                type: 'POST',
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
    </script>
@endpush
