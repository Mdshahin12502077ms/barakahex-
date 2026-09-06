@push('script')
<script>
    $(document).ready(function () {
        $('.barcode').on('change, enter, paste, keyup, input', function (e) {
            e.preventDefault();
            var url = $('#url').val() ?? path;
            var parcel_no = $(this).val().toUpperCase();
            var add_url = $(this).attr('data-url');

            if( $("#row_"+parcel_no).length ) {
                Swal.fire('Oops...', '{{ __('already_added_to_list') }}', 'error');
                $('#barcode').val('');
                return;
            }

            var val =  parseInt($('#parcels').attr('data-val')) + 1;
            var formData = {
                val : val
            }

            $.ajax({
                type: "GET",
                dataType: 'json',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url + '/' + add_url+parcel_no,
                success: function (data) {
                    if (data.error == true){
                        Swal.fire('Oops...', data.message, 'error');
                        $('#barcode').val('');
                        return;
                    } else{
                        $('#barcode').val('');
                        $('#parcels').append(data.view);
                        $('#parcels').attr('data-val', data.val);
                    }
                },
                error: function (data) {
                }
            });
        });

    });

    $('#parcel-form').on('keyup keypress', function(e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) {
            e.preventDefault();
            return false;
        }
    });

    $(document).ready(function(){
        $(document).on('click','.delete-btn-remove',function(){
            var row = $(this).attr('data-row');
            $('#'+row).remove();
        });
    });

    $(document).on('change','.pickup-merchant',function (e){
    // $('.pickup-merchant').on('change',function (e){
        e.preventDefault();
        var merchant = $(this).val();
        var url = $(this).attr('data-url');

        var formData = {
            merchant : merchant
        }

        $.ajax({
            type: "GET",
            dataType: 'json',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: url,
            success: function (data) {
                if (data.error == true){
                    Swal.fire('Oops...', data.message, 'error');
                    return;
                } else{
                    $('#merchant-parcels').append(data.view);
                }
            },
            error: function (data) {
            }
        });
    })

</script>
@endpush
