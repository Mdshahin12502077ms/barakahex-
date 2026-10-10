<div class="modal fade" tabindex="-1" id="assign-pickup" aria-labelledby="exampleModalLabel" aria-hidden="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__('assign_pickup_man')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{route('assign.pickup.man')}}" method="POST" class="form-validate is-alter">
                    @csrf
                    <input type="hidden" name="id" value="" id="pickup-parcel-id">
                    <div class="mb-3">
                        <label class="form-label" for="area">{{__('pickup_man')}}  <span class="text-danger">*</span></label>
                            <select name="pickup_man"  id="assign_pickup_man_standalone" data-url="{{ route('get-delivery-man-live') }}" class="form-control" required>
                                <option value="">{{ __('select_pickup_man') }}</option>
                            </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="area">{{ __('note') }}</label>
                        <textarea name="note" class="form-control">{{ old('note') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <div class="preview-block">
                            <div class="custom-control custom-checkbox">
                                <label class="custom-control-label" for="customCheck1">
                                    <input type="checkbox" class="custom-control-input" id="customCheck1" name="notify_pickup_man" value="notify">
                                    <span>{{__('notify_pickup_man')}}</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <button type="submit" class="btn sg-btn-primary resubmit">{{__('submit')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@push('script')
<script>
    $(document).ready(function() {
        $('#assign-pickup').on('shown.bs.modal', function () {
            if (!$('#assign_pickup_man_standalone').hasClass("select2-hidden-accessible")) {
                $('#assign_pickup_man_standalone').select2({
                    dropdownParent: $('#assign-pickup'),
                    placeholder: "{{ __('select_pickup_man') }}",
                    ajax: {
                        type: "GET",
                        dataType: 'json',
                        url: $('#assign_pickup_man_standalone').data('url'),
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
        });
    });
</script>
@endpush
