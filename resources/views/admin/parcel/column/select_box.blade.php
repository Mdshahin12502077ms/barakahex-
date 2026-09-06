<div class="form-check">
    <input class="form-check-input selected_parcel" type="checkbox" value="{{$parcel->id}}" onclick="toggleDropdown()">
</div>

<script>
    function toggleDropdown() {
        const anyChecked = document.querySelectorAll('.selected_parcel:checked').length > 0;
        const dropdown = document.getElementById('actionDropdown');
        if (anyChecked) {
            dropdown.classList.remove('d-none');
        } else {
            dropdown.classList.add('d-none');
        }
    }

    function selectAll(event) {
        const checkboxes = document.querySelectorAll('.selected_parcel');
        checkboxes.forEach(cb => cb.checked = event.target.checked);
    }

    function selectedParcelBatchPrint() {
        const batchPrintBtn = document.getElementById('batchPrintBtn');
        const checkboxes = document.querySelectorAll('.selected_parcel');
        // Collect all checked parcel IDs
        const selectedParcelIds = [];
        checkboxes.forEach(function (checkbox) {
            if (checkbox.checked) {
                selectedParcelIds.push(checkbox.value);
            }
        });

        // If no parcels are selected, show an alert or prevent export
        if (selectedParcelIds.length === 0) {
            alert('Please select at least one parcel.');
            return;
        }

        var href = "{{ route('batch.print') }}";
        // Create the URL with the selected parcels as query parameter
        const url = new URL(href);
        console.log(url)
        url.searchParams.set('parcel_ids', selectedParcelIds.join(','));

        // Navigate to the modified URL
        window.open(url, '_blank');
    };

    function selectedParcelExport() {
        const bulkExportBtn = document.getElementById('bulkExportBtn');
        const checkboxes = document.querySelectorAll('.selected_parcel');

        // Collect all checked parcel IDs
        const selectedParcelIds = [];
        checkboxes.forEach(function (checkbox) {
            if (checkbox.checked) {
                selectedParcelIds.push(checkbox.value);
            }
        });

        // If no parcels are selected, show an alert or prevent export
        if (selectedParcelIds.length === 0) {
            alert('Please select at least one parcel.');
            return;
        }


        var href = "{{ route('export.parcel') }}";
        // Create the URL with the selected parcels as query parameter
        const url = new URL(href);

        url.searchParams.set('parcel_ids', selectedParcelIds.join(','));

        $.ajax({
            type: "get",
            url: url,
            success: function (response) {
                if (response) {
                    window.open(url, '_blank');
                }
            }
        });
    }

    function selectedParcelReceivedByPickupman() {
        const ids = [...document.querySelectorAll('.selected_parcel:checked')]
            .map(cb => cb.value);
        if (ids.length === 0) {
            return;
        }

        $.ajax({
            url: "{{ route('parcels.checkAssignedPickupman') }}",
            type: "POST",
            data: {
                ids,
                _token: "{{ csrf_token() }}"
            },
            success(res) {
                if (!res.all_received) {
                    toastr.error(`${res.invalid_count} of the selected parcel(s) are not yet in "Assign Pickup Man" status.`)
                    $('#parcel-receive-by-pickupman').modal('hide');
                    //$('.selected_parcel').prop('checked', false);
                } else {
                    // Pass the IDs to the hidden field for the eventual form submit
                    $('#parcel-receive-by-pickupman').modal('show');

                    $('#receive-parcel-pickup-id').val(ids.join(','));
                }
            },
            error() {
                toastr.error('Couldn’t verify parcel status. Try again.');
            }
        });
    }

    function selectedParcelReceive() {
        const ids = [...document.querySelectorAll('.selected_parcel:checked')]
            .map(cb => cb.value);
        if (ids.length === 0) {
            return;
        }

        $.ajax({
            url: "{{ route('parcels.checkPickedUp') }}",
            type: "POST",
            data: {
                ids,
                _token: "{{ csrf_token() }}"
            },
            success(res) {
                if (!res.all_received) {
                    toastr.error(`${res.invalid_count} of the selected parcel(s) are not yet in "Received By Pickup Man" status.`)
                    $('#parcel-receive').modal('hide');
                    //$('.selected_parcel').prop('checked', false);
                } else {
                    // Pass the IDs to the hidden field for the eventual form submit
                    $('#parcel-receive').modal('show');

                    $('#receive-parcel-id').val(ids.join(','));
                }
            },
            error() {
                toastr.error('Couldn’t verify parcel status. Try again.');
            }
        });
    }


    function selectedParcelShip() {
        const ids = [...document.querySelectorAll('.selected_parcel:checked')]
            .map(cb => cb.value);
        if (ids.length === 0) {
            return;
        }

        $.ajax({
            url: "{{ route('parcels.checkReceived') }}",
            type: "POST",
            data: {
                ids,
                _token: "{{ csrf_token() }}"
            },
            success(res) {
                if (!res.all_received) {
                    toastr.error(`${res.invalid_count} of the selected parcel(s) are not yet in "Received By Warehouse" status.`)
                    $('#assign-delivery').modal('hide');
                    //$('.selected_parcel').prop('checked', false);
                } else {
                    // Pass the IDs to the hidden field for the eventual form submit
                    $('#assign-delivery').modal('show');

                    $('#assign-delivery-parcel-id').val(ids.join(','));
                }
            },
            error() {
                toastr.error('Couldn’t verify parcel status. Try again.');
            }
        });
    }


    function selectedBulkDelivered() {
        const ids = [...document.querySelectorAll('.selected_parcel:checked')]
            .map(cb => cb.value);
        if (ids.length === 0) {
            return;
        }

        $.ajax({
            url: "{{ route('parcels.checkDeliveryAssigned') }}",
            type: "POST",
            data: {
                ids,
                _token: "{{ csrf_token() }}"
            },
            success(res) {
                if (!res.all_valid) {
                    if (res.not_assigned_count > 0) {
                        toastr.error(`${res.not_assigned_count} of the selected parcel(s) are not yet in "Delivery Assigned" status.`);
                    }
                    if (res.third_party_count > 0) {
                        toastr.error(`${res.third_party_count} of the selected parcel(s) are assigned to third-party delivery (Falcon/Elite).`);
                    }
                    $('#parcel-delivered-bulk').modal('hide');
                    return;
                }

                // All good — show modal and set IDs
                $('#parcel-delivered-bulk').modal('show');
                $('#delivery-parcel-id').val(ids.join(','));
            },
            error() {
                toastr.error('Couldn’t verify parcel status. Try again.');
            }
        });

    }

    function selectedBulkReturnToWarehouse() {
        const ids = [...document.querySelectorAll('.selected_parcel:checked')]
            .map(cb => cb.value);
        if (ids.length === 0) {
            return;
        }

        $.ajax({
            url: "{{ route('parcels.checkDeliveryAssigned') }}",
            type: "POST",
            data: {
                ids,
                _token: "{{ csrf_token() }}"
            },
            success(res) {
                if (!res.all_valid) {
                    if (res.not_assigned_count > 0) {
                        toastr.error(`${res.not_assigned_count} of the selected parcel(s) are not yet in "Delivery Assigned" status.`);
                    }
                    $('#return-delivery').modal('hide');
                    return;
                }

                // All good — show modal and set IDs
                $('#return-delivery').modal('show');
                $('#delivery-return-id').val(ids.join(','));
            },
            error() {
                toastr.error('Couldn’t verify parcel status. Try again.');
            }
        });
    }

    function selectedBulkReturnAssignToMerchant() {
        const ids = [...document.querySelectorAll('.selected_parcel:checked')]
            .map(cb => cb.value);
        if (ids.length === 0) {
            return;
        }
        $.ajax({
            url: "{{ route('parcels.checkReturnToWarehouse') }}",
            type: "POST",
            data: {
                ids,
                _token: "{{ csrf_token() }}"
            },
            success(res) {
                if (!res.all_valid) {
                    if (res.invalid_count > 0) {
                        toastr.error(`${res.invalid_count} of the selected parcel(s) are not yet in "Returned To Warehouse" status.`);
                    }
                    $('#return-assign-tomerchant').modal('hide');
                    return;
                }
                // All good — show modal and set IDs
                $('#return-assign-tomerchant').modal('show');
                $('#return-merchant-parcel-id').val(ids.join(','));
            },
            error() {
                toastr.error('Couldn’t verify parcel status. Try again.');
            }
        });
    } function selectedBulkReturnToMerchant() {
        const ids = [...document.querySelectorAll('.selected_parcel:checked')]
            .map(cb => cb.value);
        if (ids.length === 0) {
            return;
        }
        $.ajax({
            url: "{{ route('parcels.checkReturnAssignToMerchant') }}",
            type: "POST",
            data: {
                ids,
                _token: "{{ csrf_token() }}"
            },
            success(res) {
                if (!res.all_valid) {
                    if (res.invalid_count > 0) {
                        toastr.error(`${res.invalid_count} of the selected parcel(s) are not yet in "Returned Assign To Merchant" status.`);
                    }
                    $('#returned-to-merchant').modal('hide');
                    return;
                }
                // All good — show modal and set IDs
                $('#returned-to-merchant').modal('show');
                $('#returned-to-merchant-id').val(ids.join(','));
            },
            error() {
                toastr.error('Couldn’t verify parcel status. Try again.');
            }
        });
    } 
</script>