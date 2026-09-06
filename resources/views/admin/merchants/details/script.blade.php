@push('script')
    <script type="text/javascript">
        $(document).on('click', '.common-key', function(){
            var value = $(this).val();
            var value = value.split("_");

            if(value[0] == 'read'){
                if (!$(this).is(':checked')) {
                    $('.common-key').prop('checked', false);
                }
            }
            else{
                if ($(this).is(':checked')){
                    $('.common-key').prop('checked', true);
                }
            }
        });
    </script>
@endpush
