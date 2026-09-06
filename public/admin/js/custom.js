$(document).ready(function () {
    $('[data-toggle="popover"]').popover();

    $('#switch-mode').click(function (e) {
        e.preventDefault();
        var url = $('#url').val() ?? path;
        $.ajax({
            type: "GET",
            dataType: 'json',
            url: url + '/' + 'mode-change',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (data) {
                location.reload();
            },
            error: function (data) {
            }
        });
    });

});

$(document).ready(function () {

    $(document).on('change', '.change-role', function (e) {

    // $('.change-role').on('change', function (e) {
        e.preventDefault();
        var url = $('#url').val() ?? path;
        var role_id = $(this).val();


        var formData = {
            role_id : role_id
        }
        $.ajax({
            type: "GET",
            dataType: 'html',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: url + '/' + 'admin/change-role',
            success: function (data) {
                $('#permissions-table').html(data);
            },
            error: function (data) {
            }
        });
    });

});

function readURL(input, image_for) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $('#img_'+ image_for).attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
    }
}

$(document).on('change', '.image_pick', function (e) {
// $(".image_pick").change(function () {
    var image_for = $(this).attr('data-image-for');
    readURL(this, image_for);
});



$(document).ready(function () {

    $(document).on('click', '.cancel-parcel', function (e) {
        e.preventDefault();
        var id = $(this).closest("div.nk-tb-item").find("input").val() ?? $(this).data('id');
        $('#cancel-parcel-id').val(id);
    });

});
$(document).ready(function () {

    $(document).on('click', '.delivery-parcel-partially', function (e) {
        e.preventDefault();
        var id = $(this).closest("div.nk-tb-item").find("input").val() ?? $(this).data('id');
        $('#delivery-parcel-partially-id').val(id);

        var formData = {
            id : id
        }
        $.ajax({
            type: "GET",
            dataType: 'json',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: '/' + 'admin/get-current-cod',
            success: function (data) {
                $("form#partial-delivery-form .cod").parent('.form-control-wrap').addClass('focused');

                $("form#partial-delivery-form .cod").val(data[1]);
            },
            error: function (data) {
            }
        });
    });

});
$(document).ready(function () {

    $('#customer_phone_number').on('blur', function (e) {
        e.preventDefault();

        var phone_number = $(this).val();

        var formData = {
            phone_number : phone_number
        }
        $.ajax({
            type: "GET",
            dataType: 'json',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: '/' + 'get-customer-info',
            success: function (data) {
                if ($("#customer_name").val() == ""){
                    $("#customer_name").val(data['customer_name']);
                }
                if($("#customer_address").val() == ""){
                    $("#customer_address").val(data['customer_address']);
                }

            },
            error: function (data) {
            }
        });
    });

});

$(document).ready(function () {

    $(document).on('click', '.parcel-re-request', function (e) {
        e.preventDefault();
        var id = $(this).closest("div.nk-tb-item").find("input").val() ?? $(this).data('id');
        $('#re-request-parcel-id').val(id);
    });

});

$(document).ready(function () {

    $(document).on('click', '.delete-parcel', function (e) {
        e.preventDefault();
        var id = $(this).closest("div.nk-tb-item").find("input").val() ?? $(this).data('id');
        $('#delete-parcel-id').val(id);
    });

});

$(document).ready(function () {

    $(document).on('click', '.transfer-to-branch', function (e) {
        e.preventDefault();
        var id = $(this).closest("div.nk-tb-item").find("input").val() ?? $(this).data('id');
        $('#transfer-to-branch-id').val(id);
    });

});

$(document).ready(function () {

    $(document).on('click', '.receive-parcel-pickup', function (e) {
        e.preventDefault();
        var id = $(this).closest("div.nk-tb-item").find("input").val() ?? $(this).data('id');
        $('#receive-parcel-pickup-id').val(id);
    });

});

$(document).ready(function () {

    $(document).on('click', '.receive-parcel', function (e) {
        e.preventDefault();
        var id = $(this).closest("div.nk-tb-item").find("input").val() ?? $(this).data('id');
        $('#receive-parcel-id').val(id);
    });

});

$(document).ready(function () {

    $(document).on('click', '.transfer-receive-to-branch', function (e) {
        e.preventDefault();
        var id = $(this).closest("div.nk-tb-item").find("input").val() ?? $(this).data('id');
        $('#transfer-receive-to-branch-id').val(id);
    });

});


$(document).ready(function () {

    $(document).on('click', '.assign-pickup-man', function (e) {
        e.preventDefault();
        var id = $(this).closest("div.nk-tb-item").find("input").val() ?? $(this).data('id');
        $('#pickup-parcel-id').val(id);
    });


});
$(document).ready(function () {

    $(document).on('click', '.assign-delivery-man', function (e) {
        e.preventDefault();
        var id = $(this).closest("div.nk-tb-item").find("input").val() ?? $(this).data('id');
        $('#delivery-parcel-id').val(id);
    });

});
$(document).ready(function () {

    $(document).on('click', '.return-assign-to-merchant', function (e) {
        e.preventDefault();
        var id = $(this).closest("div.nk-tb-item").find("input").val() ?? $(this).data('id');
        $('#return-merchant-parcel-id').val(id);
    });

});

$(document).ready(function () {


    $(document).on('click', '.delivery-return', function (e) {
        e.preventDefault();
        var id = $(this).closest("div.nk-tb-item").find("input").val() ?? $(this).data('id');
        $('#delivery-return-id').val(id);
    });

});
$(document).ready(function () {

    $(document).on('click', '.parcel-returned-to-merchant', function (e) {
        e.preventDefault();
        var id = $(this).closest("div.nk-tb-item").find("input").val() ?? $(this).data('id');
        $('#returned-to-merchant-id').val(id);
    });

});

$(document).ready(function () {

    $(document).on('click', '.reverse-from-cancel', function (e) {
        e.preventDefault();
        var id = $(this).closest("div.nk-tb-item").find("input").val() ?? $(this).data('id');
        $('#reverse-from-cancel-id').val(id);
    });

});


$(document).ready(function () {

    $(document).on('click', '.reschedule-pickup', function (e) {
        e.preventDefault();
        var id = $(this).closest("div.nk-tb-item").find("input").val() ?? $(this).data('id');
        $('#re-schedule-pickup-parcel-id').val(id);

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
                $("#re-schedule-pickup-assign-man").append(data[1]);
                $("form#re-schedule-pickup-assign-form .outlined-date-picker").parent('.form-control-wrap').addClass('focused');

                $("form#re-schedule-pickup-assign-form .outlined-date-picker").val(data[2]);

                if(data[4] == 'frozen'){
                    $("form#re-schedule-pickup-assign-form .time").removeClass('d-none');
                    $("form#re-schedule-pickup-assign-form .time-picker").parent('.form-control-wrap').addClass('focused');
                    $("form#re-schedule-pickup-assign-form .time-picker").val(data[3]);
                }else{
                    $("form#re-schedule-pickup-assign-form .time").addClass('d-none');
                }

                $("#re-schedule-pickup-note").val(data[5]);
                flatpickr("form#re-schedule-pickup-assign-form .outlined-date-picker", {
                    dateFormat: "Y-m-d",
                    enableTime: false,
                    time_24hr: false,
                });
            },
            error: function (data) {
            }
        });
    });

});


$(document).ready(function () {


    $(document).on('click', '.reschedule-delivery', function (e) {
        e.preventDefault();
        var url = $('#url').val() ?? path;
        var id = $(this).closest("div.nk-tb-item").find("input").val() ?? $(this).data('id');
        $('#re-schedule-delivery-parcel-id').val(id);

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
                $("#re-schedule-delivery-assign-man").append(data[1]);
                $("form#re-schedule-delivery-assign-form .date-picker").parent('.form-control-wrap').addClass('focused');

                $("form#re-schedule-delivery-assign-form .date-picker").val(data[2]);

                if(data[4] == 'frozen'){
                    $("form#re-schedule-delivery-assign-form .time").removeClass('d-none');
                    $("form#re-schedule-delivery-assign-form .time-picker").parent('.form-control-wrap').addClass('focused');
                    $("form#re-schedule-delivery-assign-form .time-picker").val(data[3]);
                }else{
                    $("form#re-schedule-delivery-assign-form .time").addClass('d-none');
                }

                $("#re-schedule-delivery-note").val(data[5]);

                if(data[6] == 'dhaka'){
                    $(".third-party").addClass('d-none');
                }else{
                    $(".third-party").removeClass('d-none');
                    $("#re-schedule-third-party").append(data[7]);
                }
            },
            error: function (data) {
            }
        });
    });

});


$(document).ready(function () {
    // Event listener for .select-shop change
    $(document).on('change', '.select-shop', function (e) {
        var shop_id = $(this).val();
        var url = $(this).attr('data-url');
        var formData = {
            shop_id: shop_id
        };

        $.ajax({
            type: "GET",
            dataType: 'json',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: url,
            success: function (data) {
                $('#shop_pickup_branch').val(data['shop_pickup_branch']);
                $('#shop_phone_number').val(data['shop_phone_number']);
                $('#shop_address').val(data['address']);
            },
            error: function (data) {
                console.error('Error:', data);
            }
        });
    });

    // Event listener for .select-merchant change
    $(document).on('change', '.select-merchant', function (e) {
        var merchant_id = $(this).val();
        var url = $(this).attr('data-url');
        var formData = {
            merchant_id: merchant_id
        };

        $.ajax({
            type: "GET",
            dataType: 'html',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: url,
            success: function (data) {
                $('#merchant_select').html(data);
                $('.select-shop').change();
            },
            error: function (data) {
                console.error('Error:', data);
            }
        });
    });
});




function getMerchantStaff(merchant_id){
    var url = '/admin/merchant/staff';
    var formData = {
        merchant_id : merchant_id
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
            $('#created_by').html(data);
        },
        error: function (data) {
        }
    });
}

$(document).ready(function () {

    $(document).on('click', '.process-withdraw', function (e) {
        e.preventDefault();
        var id = $(this).closest("div.nk-tb-item").find("input").val() ?? $(this).data('id');
        $('#withdraw-process-id').val(id);
    });

});

$(document).ready(function () {


    $(document).on('click', '.approve-withdraw', function (e) {
        e.preventDefault();
        var id = $(this).closest("div.nk-tb-item").find("input").val() ?? $(this).data('id');
        $('#withdraw-approve-id').val(id);
        var url = $(this).attr('data-url');
        getBatches(id, url);
    });

});

function getBatches(id, url){
    var formData = {
        withdraw_id : id
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
            $('.withdraw_batches').html(data);
        },
        error: function (data) {
        }
    });
}

$(document).ready(function () {

    $(document).on('click', '.reject-payment', function (e) {
        e.preventDefault();
        var id = $(this).closest("div.nk-tb-item").find("input").val() ?? $(this).data('id');
        $('#reject-payment-id').val(id);
    });

});
$(document).ready(function () {

    $(document).on('click', '.change-batch', function (e) {
        e.preventDefault();
        var id = $(this).closest("div.nk-tb-item").find("input").val() ?? $(this).data('id');
        $('#change-batch-id').val(id);
        var url = $(this).attr('data-url');
        getBatches(id, url);
    });

});
$(document).ready(function () {

    $(document).on('click', '.delivery-parcel', function (e) {
        e.preventDefault();
        var id = $(this).closest("div.nk-tb-item").find("input").val() ?? $(this).data('id');
        $('#delivery-parcel-id').val(id);
    });

});
$(document).ready(function () {

    $(document).on('click', '.assign-delivery-man', function (e) {
        e.preventDefault();
        var id = $(this).closest("div.nk-tb-item").find("input").val() ?? $(this).data('id');
        $('#assign-delivery-parcel-id').val(id);

        getLocation(id);
    });

});


$(document).ready(function () {

    $(document).on('change', '.select-merchant-for-credit', function (e) {
    // $('.select-merchant-for-credit').on('change', function (e) {
        var merchant_id = $(this).val();
        var url = $(this).attr('data-url');
        var formData = {
            merchant_id : merchant_id
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
                $('#parcel_select').html(data);
            },
            error: function (data) {
            }
        });
    });

});

function getLocation(id){
    var url = $('#url').val() ?? path;
    var formData = {
        id : id
    }
    $.ajax({
        type: "GET",
        dataType: 'json',
        data: formData,
        url: url + '/' + 'admin/get-parcel-location',
        success: function (data) {

            if(data.location == 'dhaka'){
                $(".third-party").addClass('d-none');
            }else{
                $(".third-party").removeClass('d-none');
            }
        },
        error: function (data) {
        }
    });
}


$(document).ready(function() {


    $(document).on('click', '.copy-to-clipboard input', function (e) {
            var text = $(this).attr('data-text');
            var inputValue = $(this).val();
            // Create a temporary textarea element to copy the text
            var tempTextarea = $('<textarea>');
            tempTextarea.val(inputValue).css('position', 'absolute').css('left', '-9999px');
            $('body').append(tempTextarea);
            tempTextarea.select();
            document.execCommand('copy');
            tempTextarea.remove();
            toastr.clear();
            toastr.success(text + ': ' + inputValue);

    });

    $(document).on('click', '.copy-to-clipboard', function (e) {
            var text = $(this).attr('data-text');
            var inputValue = text;
            // Create a temporary textarea element to copy the text
            var tempTextarea = $('<textarea>');
            tempTextarea.val(inputValue).css('position', 'absolute').css('left', '-9999px');
            $('body').append(tempTextarea);
            tempTextarea.select();
            document.execCommand('copy');
            tempTextarea.remove();
            toastr.clear();
            toastr.success('Copied:' + ': ' + text);

    });


});


$('.sms-provider').on('change', function () {
    if ($(this).val() === "onnorokom") {
        $(".onnorokom").removeClass('d-none');
        $(".reve").addClass('d-none');

        $("#onnorokom_url").attr("required", true);
        $("#onnorokom_username").attr("required", true);
        $("#onnorokom_password").attr("required", true);

        $("#reve_url").attr("required", false);
        $("#reve_api_key").attr("required", false);
        $("#reve_secret").attr("required", false);
    }
    else if ($(this).val() === "reve") {
        $(".reve").removeClass('d-none');
        $(".onnorokom").addClass('d-none');

        $("#onnorokom_url").attr("required", false);
        $("#onnorokom_username").attr("required", false);
        $("#onnorokom_password").attr("required", false);

        $("#reve_url").attr("required", true);
        $("#reve_api_key").attr("required", true);
        $("#reve_secret").attr("required", true);

    } else {
        $(".onnorokom").addClass('d-none');
        $(".reve").addClass('d-none');

        $("#onnorokom_url").attr("required", false);
        $("#onnorokom_username").attr("required", false);
        $("#onnorokom_password").attr("required", false);

        $("#reve_url").attr("required", false);
        $("#reve_api_key").attr("required", false);
        $("#reve_secret").attr("required", false);
    }
});

$('.database-storage').on('change', function () {
    if ($(this).val() === "local") {
        $(".google-drive").addClass('d-none');

        $("#google_drive_client_id").attr("required", false);
        $("#google_drive_client_secret").attr("required", false);
        $("#google_drive_refresh_token").attr("required", false);
        $("#google_drive_folder_id").attr("required", false);
    }
    else if ($(this).val() === "both" ||  $(this).val() === "google-drive") {
        $(".google-drive").removeClass('d-none');

        $("#google_drive_client_id").attr("required", true);
        $("#google_drive_client_secret").attr("required", true);
        $("#google_drive_refresh_token").attr("required", true);
        $("#google_drive_folder_id").attr("required", true);

    } else {
        $(".google-drive").addClass('d-none');

        $("#google_drive_client_id").attr("required", false);
        $("#google_drive_client_secret").attr("required", false);
        $("#google_drive_refresh_token").attr("required", false);
        $("#google_drive_folder_id").attr("required", false);
    }
});

$(document).on('change', '.get-delivery-man-balance', function (e) {
// $('.get-delivery-man-balance').on('change', function () {
    var url = $('#url').val();
    var id  = $(this).val();
    var data_for = $(this).attr('data-for');
    var company_account_id = $(this).attr('data-id');
    var formData = {
        id: id,
        data_for: data_for,
        company_account_id: company_account_id
    }

    $.ajax({
        type: "GET",
        dataType: 'json',
        data: formData,
        url: url + '/' + 'admin/income/get-delivery-man-balance',
        success: function (data) {
            $('.current-balance').text(data.balance);
        },
        error: function (data) {
        }
    });
});

function getKey(length = 16, id) {
    var api_key = "";
    var string = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@$%^&*()";

    for (var i = 0; i < length; i++)
        api_key += string.charAt(Math.floor(Math.random() * string.length));

    $("#"+id).val(api_key);
}

$(document).ready(function () {
    $('.resubmit').on('click', function (e) {
        var loading = 2;
        $('.resubmit').css("pointer-events", "none");
        $('.resubmit').addClass('disabled')
        startTimer(loading);
    });
});
function startTimer(duration) {
    var timer = duration, minutes, seconds;
    var trigger =  setInterval(function () {
        minutes = parseInt(timer / 60, 10)
        seconds = parseInt(timer % 60, 10);
        minutes = minutes < 10 ? "0" + minutes : minutes;
        seconds = seconds < 10 ? "0" + seconds : seconds;
        if (timer >= 0) {
            --timer;
        }
        else if(timer < 0){
            $('.resubmit').css("pointer-events", "auto");
            $('.resubmit').removeClass('disabled');
        }
    }, 2000);
}

$(document).on('change', '.return-charge-type', function (e) {
// $('.return-charge-type').on('change', function () {
    var value = $(this).val();

    if (value == 'on_demand'){
        $('.charges').removeClass('d-none');
    } else{
        $('.charges').addClass('d-none');
    }
});


$(document).ready(function () {


    $(document).on('click', '.create-paperfly-parcel', function (e) {
        e.preventDefault();
        var id = $(this).closest("div.nk-tb-item").find("input").val();
        $('#create-paperfly-parcel-id').val(id);
        $('.thana-union').html('');
        $('.get-thana-union').html('');
        var url = $(this).attr('data-url');

        $.ajax({
            type: "GET",
            dataType: 'html',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: url,
            success: function (data) {
                $('.get-thana-union').html(data);
                $('.get-thana-union').select2();
                $('.thana-union').select2();
            },
            error: function (data) {
            }
        });

    });

});


$(document).ready(function () {
    $(document).on('change', '.get-thana-union', function (e) {
    // $('.get-thana-union').on('change', function (e) {
        var district = $(this).val();
        $('.thana-union').html('');
        var url = $(this).attr('data-url');
        var formData = {
            district : district
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
                $('.thana-union').html(data);
            },
            error: function (data) {
            }
        });
    });
});

$(document).ready(function () {
    $('.modal#create-paperflyparcel').on('hidden.bs.modal', function () {
        $('.thana-union').select2("destroy")
        $('.get-thana-union').select2("destroy")
    })
});
