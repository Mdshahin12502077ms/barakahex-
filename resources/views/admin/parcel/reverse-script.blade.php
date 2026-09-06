<script>
    $(document).ready(function () {
        $(document).on('click', '.delivery-reverse', function(e) {
            e.preventDefault();
            var id = $(this).closest("div.nk-tb-item").find("input").val() ?? $(this).data('id');
            $('#delivery-reverse-id').val(id);

            var url = $(this).attr('data-url');
            var formData = {
                id : id
            }
            $.ajax({
                type: "GET",
                dataType: 'html',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                success: function (data) {
                    $('#reverse').html(data);
                    // getDefaultShop(merchant_id);
                },
                error: function (data) {
                }
            });
        });

    });
    $(document).ready(function () {


        $(document).on('click', '.transfer-to-branch', function(e) {
        // $('.transfer-to-branch').on('click', function (e) {
            e.preventDefault();
            var id = $(this).closest("div.nk-tb-item").find("input").val() ?? $(this).data('id');
            $('#transfer-to-branch-id').val(id);

            var url = $(this).attr('data-url');
            var formData = {
                id : id
            }
            $.ajax({
                type: "GET",
                dataType: 'html',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                success: function (data) {
                    $('#transfer-branch-options').html(data);
                },
                error: function (data) {
                }
            });
        });

    });
</script>
