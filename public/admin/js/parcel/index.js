$(document).ready(function () {
    $(document).on('click', '#download', function() {
        const csrfToken             = $('meta[name="csrf-token"]').attr('content');
        const phone_number          = $('#phone_number').val();
        const merchant_id           = $('#merchant_id').val();
        const customer_invoice_no   = $('#customer_invoice_no').val();
        const created_at            = $('#created_at').val();
        const pickup_date           = $('#pickup_date').val();
        const delivery_date         = $('#delivery_date').val();
        const delivered_date        = $('#delivered_date').val();
        const pickup_man_id         = $('#pickup_man_id').val();
        const delivery_man_id   = $('#delivery_man_id').val();
        const third_party = $('#third_party').val();
        const status = $('#status').val();
        const weight = $('#weight').val();
        const parcel_type = $('#parcel_type').val();
        const location = $('#location').val();
        const branch_id = $('#branch_id').val();
        const pickup_branch_id = $('#pickup_branch_id').val();

        const dataset = {
            phone_number: phone_number,
            merchant_id: merchant_id,
            customer_invoice_no: customer_invoice_no,
            created_at: created_at,
            pickup_date: pickup_date,
            delivery_date: delivery_date,
            delivered_date: delivered_date,
            pickup_man_id: pickup_man_id,
            delivery_man_id: delivery_man_id,
            third_party: third_party,
            status: status,
            weight: weight,
            parcel_type: parcel_type,
            location: location,
            branch_id: branch_id,
            pickup_branch_id: pickup_branch_id,
        };

        axios.post(download_url, dataset, {
            responseType: 'blob'
        })
        .then(response => {
            const blob = new Blob([response.data], {
                type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            });
            const link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.setAttribute('download', 'delivery-report.xlsx');
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        })
        .catch(error => {
            console.error('Error downloading file:', error);
            alert('Failed to download the report. Please try again.');
        });

    });

    searchCategory($('#select_category'));
    searchOrganization($('#ins_by_org'));

    $('#filterBTN').click(function() {
        $('#filterSection').toggleClass('show');
    });


    $('#pickup_man_id').select2(
        getLiveSearch(
            $('#pickup_man_id').data('url'),
            'Select Pickup Hero'
        )
    )

    $('#delivery_man_id').select2(
        getLiveSearch(
            $('#delivery_man_id').data('url'),
            'Select Delivery Hero'
        )
    )

    $(document).on('change', '.filterable', function() {
        advancedSearchMapping({
            name: $(this).attr('name'),
            value: $(this).val(),
        });
    })

    $(document).on('click', '#reset', () => {
        $('.filterable').val('').trigger('change');
        $('#dataTableBuilder').DataTable().ajax.reload();
    })
    $(document).on('click', '#filter', () => {
        $('#checkAll').prop('checked', false).trigger('change');
        $('#dataTableBuilder').DataTable().ajax.reload();
    })

    const advancedSearchMapping = (attribute) => {
        $('#dataTableBuilder').on('preXhr.dt', function (e, settings, data) {
            data[attribute.name] = attribute.value;
        });
    }
    function initializeSelect2(element, dropdownParent, placeholder) {
        element.select2({
            dropdownParent: dropdownParent,
            placeholder: placeholder,
            minimumInputLength: 2,
            ajax: {
                type: "GET",
                dataType: 'json',
                url: element.data('url') ?? delivery_man_search_url,
                data: function (params) {
                    return {
                        q: params.term,
                    }
                },
                delay: 400,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                processResults: function (data) {
                    return {
                        results: data
                    }
                },
                cache: true
            }
        });
    }
    initializeSelect2($('#assign_pickup_man'), $('#assign-delivery'), "Select Pickup Hero");
    initializeSelect2($('.delivery-man-live-search'), $('#parcel-transfer-to-branch'), "Select Delivery Man");
    initializeSelect2($('#assign_delivery_man'), $('#assign-delivery'), "Select Delivery Man");
    initializeSelect2($('#re-schedule-delivery-assign-man'), $('#re-schedule-delivery-assign-form'), "Select Delivery Hero");
    initializeSelect2($('#return_assigned_delivery_man'), $('#return-assign-tomerchant'), "Select Delivery Hero");

    $('#assign_pickup_man_').select2({
        dropdownParent: $('#assign-pickup'),
        placeholder: "Select Pickup Man",
        minimumInputLength: 2,
        ajax: {
            type: "GET",
            dataType: 'json',
            url: $('#assign_pickup_man_').data('url') ?? delivery_man_search_url,
            data: function (params) {
                return {
                    q: params.term,
                }
            },
            delay: 400,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            processResults: function (data) {
                return {
                    results: data
                }
            },
            cache: true
        }

    })



});
const refreshDataTable = () => {
    $('#dataTableBuilder').DataTable().ajax.reload();
}

$(document).ready(function () {
    function initializeFlatpickr(selector) {
      flatpickr(selector, {
        dateFormat: "Y-m-d",
        static: true,
      });
    }
    $('#re-schedule-pickup').on('shown.bs.modal', function () {
      initializeFlatpickr(".reschedule-pickup-date");
    });
    $('#re-schedule-delivery').on('shown.bs.modal', function () {
      initializeFlatpickr(".reschedule-date");
    });
  });
