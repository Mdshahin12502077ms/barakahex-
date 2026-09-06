@push('script')
    <script type="text/javascript">
        $(document).ready(function() {
            $(document).on('change','.default-change', function(e) {
                var shop_id = $(this).val();
                var merchant_id = $(this).attr('data-merchant');
                var token = "{{ csrf_token() }}";
                var url = $(this).attr('data-url');
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        shop_id: shop_id,
                        merchant_id: merchant_id,
                        _token: token
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: url,
                    success: function(response) {
                        location.reload();
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
                            toastr["error"](response.message);
                        }
                    },
                    error: function(response) {
                        toastr["error"](response.message);
                    }
                });
            });

        });
    </script>
@endpush


