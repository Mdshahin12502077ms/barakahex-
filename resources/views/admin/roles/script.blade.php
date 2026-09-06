@push('script')
<script type="text/javascript">
	$(document).ready(function(){
		$('.create').on('click', function(){
			if ($(this).is(':checked')) {
				if (!$(this).closest('tr').find('.read').is(':checked')) {
					$(this).closest('tr').find('.read').prop('checked', true);
				}
			}
		});

		$('.update').on('click', function(){
			if ($(this).is(':checked')) {
				if (!$(this).closest('tr').find('.read').is(':checked')) {
					$(this).closest('tr').find('.read').prop('checked', true);
				}
			}
		});

		$('.delete').on('click', function(){
			if ($(this).is(':checked')) {
				if (!$(this).closest('tr').find('.read').is(':checked')) {
					$(this).closest('tr').find('.read').prop('checked', true);
				}
			}
		});

		$('.read').on('click', function(){
			if (!$(this).is(':checked')) {
				$(this).closest('tr').find('.create').prop('checked', false);
				$(this).closest('tr').find('.update').prop('checked', false);
				$(this).closest('tr').find('.delete').prop('checked', false);
			}
		})
	});

    $(document).on('click', '.common-key', function(){
        var value = $(this).val();
        var value = value.split("_");
        if(value[1] == 'read' || value[0] == 'manage'){
            if (!$(this).is(':checked')) {
                $(this).closest('tr').find('.common-key').prop('checked', false);
            }
        }
        else{
            if ($(this).is(':checked')) {
                $(this).closest('tr').find('.common-key').first().prop('checked', true);
            }
        }
    });

</script>
@endpush
