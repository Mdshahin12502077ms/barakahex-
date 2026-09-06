@push('script')
    <script type="text/javascript">
        $(document).ready(function(){
            $(document).on('click','.status-change-for', function(){
                var token = "{{ csrf_token() }}";
                var checkbox = $(this);
                var value = $(this).val().split('/');
                var url = $(this).data('url');

                var change_for = $(this).attr('data-change-for');

                if($(this).is(':checked')){
                    var status = 1;
                }else{
                    var status = 0;
                }

                var formData = {
                    id : value[1],
                    status : status,
                    change_for : change_for,
                }


                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        data: formData,
                        _token: token
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: url,
                    success: function(response) {
                        toastr.clear();
                        if (
                            response.status == 200 ||
                            response.status == "success"
                        ) {
                            if (response.reload) {
                                toastr["success"](response.message);
                                location.reload();
                            } else {
                                toastr["success"](response.message);
                            }
                        } else {
                            checkbox.prop("checked", !status);
                            toastr["error"](response.message);
                        }
                    },
                    error: function(response) {
                        checkbox.prop("checked", !status);
                        toastr["error"](response.message);
                    }

                });

            });
        });

    </script>
@endpush
