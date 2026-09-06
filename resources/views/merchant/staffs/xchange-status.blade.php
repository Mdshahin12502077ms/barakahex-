@push('script')
    <script type="text/javascript">
        $(document).ready(function(){
            $(".status-change").on('click', function(){
                var token = "{{ csrf_token() }}";

                var value = $(this).val().split('/');
                var url = "{{url('')}}"+'/merchant/'+value[0];

                if($(this).is(':checked')){
                    var status = 1;
                }else{
                    var status = 0;
                }

                var formData = {
                    id : value[1],
                    status : status
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
                    success: function (response) {
                        location.reload();

                        Swal.fire(
                            'Success!',
                            response,
                            'success'
                        );
                    },
                    error: function (response) {

                        Swal.fire('Oops...', response, 'error');
                    }

                });

            });
        });

    </script>
@endpush
