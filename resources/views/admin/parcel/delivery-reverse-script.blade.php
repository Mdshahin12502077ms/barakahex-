<script type="text/javascript">

    $('#reverse').change(function(){
        var status = $(this).val();
        var parcel_id = $('#delivery-reverse-id').val();
        //hide current model if modal found for selected status
        if (document.getElementById(status+'-reverse')){
            $('#delivery-reverse').modal('hide');
        }
        $('#'+status+'-reverse').modal('show');
        $('#'+status+'-id-reverse').val(parcel_id);


        if(status == 're-schedule-pickup'){
            var id = parcel_id;

            var formData = {
                id : id
            }
            $.ajax({
                type: "POST",
                dataType: 'json',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '/' + 'admin/re-schedule-pickup',
                success: function (data) {
                    $("#re-schedule-pickup-assign-man-reverse").append(data[1]);
                    $("form#re-schedule-pickup-assign-form-reverse .date-picker").parent('.form-control-wrap').addClass('focused');

                    $("form#re-schedule-pickup-assign-form-reverse .date-picker").val(data[2]);

                    if(data[4] == 'frozen'){
                        $("form#re-schedule-pickup-assign-form-reverse .time").removeClass('d-none');
                        $("form#re-schedule-pickup-assign-form-reverse .time-picker").parent('.form-control-wrap').addClass('focused');
                        $("form#re-schedule-pickup-assign-form-reverse .time-picker").val(data[3]);
                    }else{
                        $("form#re-schedule-pickup-assign-form-reverse .time").addClass('d-none');
                    }

                },
                error: function (data) {
                }
            });
        }

        if(status == 're-schedule-delivery'){
            var url = $('#url').val() ?? path;
            var id = parcel_id;

            var formData = {
                id : id
            }
            $.ajax({
                type: "POST",
                dataType: 'json',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: path + '/' + 'admin/re-schedule-delivery',
                success: function (data) {
                    $("#re-schedule-delivery-assign-man-reverse").append(data[1]);
                    $("form#re-schedule-delivery-assign-form-reverse .date-picker").parent('.form-control-wrap').addClass('focused');

                    $("form#re-schedule-delivery-assign-form-reverse .date-picker").val(data[2]);

                    if(data[4] == 'frozen'){
                        $("form#re-schedule-delivery-assign-form-reverse .time").removeClass('d-none');
                        $("form#re-schedule-delivery-assign-form-reverse .time-picker").parent('.form-control-wrap').addClass('focused');
                        $("form#re-schedule-delivery-assign-form-reverse .time-picker").val(data[3]);
                    }else{
                        $("form#re-schedule-delivery-assign-form-reverse .time").addClass('d-none');
                    }
                },
                error: function (data) {
                }
            });
        }
    });
</script>
