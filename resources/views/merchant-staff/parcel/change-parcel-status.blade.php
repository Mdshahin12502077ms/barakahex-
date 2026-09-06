<script type="text/javascript">
    function parcelStatusUpdate(url, id, status) {

        var token = "{{ csrf_token() }}";
        var url = "{{url('')}}"+'/merchant/'+url+id+'/'+status;
        Swal.fire({
            title: '{{ __('are_you_sure') }}',
            text: "{{ __('you_won_t_be_able_to_revert_this') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: "{{ __('yes_i_m') }}"
          }).then((confirmed) => {
            if (confirmed.isConfirmed) {
                $.ajax({
                    type: 'GET',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: url,
                })
                .done(function(response) {
                    Swal.fire(
                         response[2],
                         response[0],
                         response[1]
                      ).then((confirmed) => {
                        location.reload();
                      });
                })
                .fail(function(error) {
                    Swal.fire('{{ __('opps') }}...', '{{ __('something_went_wrong_with_ajax') }}', 'error');
                })
            }
          });
    };
</script>
