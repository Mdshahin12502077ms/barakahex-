<script>
    function delete_row(route, row_id,notification) {
        var url =  "{{ url('admin/apikeys/revoke') }}";
        var token = "{{ @csrf_token() }}";
        Swal.fire({
            title: '<?php echo e(__('are_you_sure')); ?>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '<?php echo e(__('yes_change_this_status')); ?>',
            cancelButtonText: '<?php echo e(__('Cancel')); ?>',
            confirmButtonColor: '#ff0000'
        }).then((confirmed) => {
            if (confirmed.isConfirmed) {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    dataType: 'json',
                    data: {
                            'id':row_id,
                            _token: token
                        },
                    url: url,
                    success: function (response) {
                        Swal.fire(
                            response.title,
                            response.message,
                            response.status
                        ).then((confirmed) => {
                            location.reload();
                        });

                    },
                    error: function (response) {
                        Swal.fire(
                            response.title,
                            response.message,
                            response.status
                        ).then((confirmed) => {
                            location.reload();
                        });
                    }

                });
            }
        });
    }
</script>
