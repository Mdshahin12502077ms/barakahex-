@push('script')
<script type="text/javascript">
    var currency = document.getElementById('currency').dataset.defaultCurrency;


    $(document).ready(function(){
       $(document).on('keyup', '.cash-collection, .weight', function(e) {
    e.preventDefault();
    if ($('#fragile').is(':checked')) {
        var fragile = 1;
    } else {
        var fragile = 0;
    }

    var url = path;
    var formData = {
        merchant     : $('.merchant').val(),
        parcel_type  : $('.parcel_type').val(),
        weight       : $('.weight').val(),
        cod          : $('.cash-collection').val(),   // ✅ fixed — always the cod field
        packaging    : $('.packaging').val(),
        fragile      : fragile
    }
    $.ajax({
        type: "GET",
        dataType: 'json',
        data: formData,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: url + '/' + 'charge-details',
        success: function (data) {
            $('#cash-collection-charge').html(data['cod']);
            $('#current-payable-charge').html(currency + ' ' + data['payable']);
            $('#delivery-charge').html(data['charge']);
            $('#cod-charge').html(data['cod_charge']);
            $('#vat-charge').html(data['vat']);
            $('#total-delivery-charge').html(data['total_delivery_charge']);
            $('#packaging-charge').html(data['packaging_charge']);
            $('#fragile-charge').html(data['fragile_charge']);   // ✅ fixed typo
        },
        error: function (data) {
        }
    });
});

$(document).on('change', '.merchant, .parcel_type, .packaging', function(e) {
    if ($('#fragile').is(':checked')) {
        var fragile = 1;
    } else {
        var fragile = 0;
    }
    var url = path;
    var formData = {
        merchant    : $('.merchant').val(),
        parcel_type : $('.parcel_type').val(),
        weight      : $('.weight').val(),
        cod         : $('.cash-collection').val(),
        packaging   : $('.packaging').val(),
        fragile     : fragile
    }
    $.ajax({
        type: "GET",
        dataType: 'json',
        data: formData,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: url + '/' + 'charge-details',
        success: function (data) {
            $('#cash-collection-charge').html(data['cod']);        // ✅ removed exit()
            $('#current-payable-charge').html(currency + ' ' + data['payable']);
            $('#delivery-charge').html(data['charge']);
            $('#cod-charge').html(data['cod_charge']);
            $('#vat-charge').html(data['vat']);
            $('#total-delivery-charge').html(data['total_delivery_charge']);
            $('#packaging-charge').html(data['packaging_charge']);
            $('#fragile-charge').html(data['fragile_charge']);
        },
        error: function (data) {
        }
    });
});

$(document).on('click', '#fragile', function(e) {
    if ($('#fragile').is(':checked')) {
        var fragile = 1;
    } else {
        var fragile = 0;
    }
    var url = path;
    var formData = {
        merchant    : $('.merchant').val(),
        parcel_type : $('.parcel_type').val(),
        weight      : $('.weight').val(),
        cod         : $('.cash-collection').val(),
        packaging   : $('.packaging').val(),
        fragile     : fragile
    }
    $.ajax({
        type: "GET",
        dataType: 'json',
        data: formData,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: url + '/' + 'charge-details',
        success: function (data) {
            $('#cash-collection-charge').html(data['cod']);
            $('#current-payable-charge').html(currency + ' ' + data['payable']);
            $('#delivery-charge').html(data['charge']);
            $('#cod-charge').html(data['cod_charge']);
            $('#vat-charge').html(data['vat']);
            $('#total-delivery-charge').html(data['total_delivery_charge']);
            $('#packaging-charge').html(data['packaging_charge']);
            $('#fragile-charge').html(data['fragile_charge']);
        },
        error: function (data) {
        }
    });
});
        
        $('body').on('change', '#city_to_thana', function (e) {
            e.preventDefault();
 
            var city_id = $(this).val();
            if (!city_id) {
                $("#thana_to_area").html("<option value=''>Select Thana</option>").trigger('change');
                return;
            }
 
            $.ajax({
                'url': "{{ route('merchant.parcel.thanabook') }}",
                'type': 'get',
                'dataType': 'text',
                data: { city_id: city_id, district_id: city_id },
                success: function (data) {
                    $("#thana_to_area").html(data);
                    if ($("#thana_to_area").hasClass("select2-hidden-accessible")) {
                        $("#thana_to_area").select2({ minimumResultsForSearch: Infinity });
                    }
                    $("#thana_to_area").trigger('change');
                    $("#thana_to_area").trigger('thanas_loaded');
                }
            });
 
        });

    });
</script>
@endpush
