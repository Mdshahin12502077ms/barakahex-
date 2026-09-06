@extends('backend.layouts.master')

@section('title')
    {{ __('packaging_type_and_charges') }}
@endsection
@section('mainContent')
    <section class="oftions">
        <div class="container-fluid">
            <div class="row">
                <div class="d-flex justify-content-center align-items-center">
                    <div class="col-xxl-9 col-lg-8 col-md-8">
                        <div class="bg-white redious-border p-20 p-sm-30">
                            <div class="mb-12" style="display:flex; justify-content:space-between;">
                                <div>
                                    <h5>{{ __('packaging_type_and_charges') }}</h5>
                                </div>
                                @if (hasPermission('charge_setting_update'))
                                    <a class="d-flex align-items-center btn sg-btn-primary gap-2" id="add-row"
                                        data-url="admin/add-charge-packaging-row/"><i
                                            class="icon la la-plus"></i><span>{{ __('add') }}</span></a>
                                @endif
                            </div>
                            @if (hasPermission('charge_setting_update'))
                                <form action="{{ route('packaging.and.charge.update') }}" class="form-validate"
                                    method="POST" enctype="multipart/form-data">
                                    @csrf
                            @endif
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card-inner">
                                        <div class="row g-gs">
                                            <table class="table table-borderless">
                                                <thead class="mb-3">
                                                    <tr>
                                                        <th scope="col">{{ __('type') }}</th>
                                                        <th scope="col">{{ __('charge') }} ({{ setting('default_currency') }})</th>
                                                        @if (hasPermission('charge_setting_update'))
                                                            <th scope="col">{{ __('action') }}</th>
                                                        @endif
                                                    </tr>
                                                </thead>
                                                <tbody id="package-charge">
                                                    @foreach ($packaging_and_charges as $packaging_and_charge)
                                                        <tr id="row_{{ $packaging_and_charge->id }}">
                                                            <td>
                                                                    <input type="number" class="form-control"
                                                                        id="{{ 'ids_' . $packaging_and_charge->id }}"
                                                                        value="{{ $packaging_and_charge->id }}"
                                                                        name="ids[]" hidden>
                                                                    <input type="text" class="form-control"
                                                                        id="{{ 'type_' . $packaging_and_charge->id }}"
                                                                        value="{{ $packaging_and_charge->package_type }}"
                                                                        name="packaging_types[]" required>
                                                                    @if ($errors->has('packaging_types'))
                                                                        <div class="invalid-feedback help-block">
                                                                            <p>{{ $errors->first('packaging_types') }}
                                                                            </p>
                                                                        </div>
                                                                    @endif
                                                            </td>
                                                            <td>
                                                                    <input type="number" class="form-control"
                                                                        id="{{ 'charge_' . $packaging_and_charge->id }}"
                                                                        value="{{ $packaging_and_charge->charge }}"
                                                                        name="charges[]" min="0" required>
                                                                    @if ($errors->has('charges'))
                                                                        <div class="invalid-feedback help-block">
                                                                            <p>{{ $errors->first('charges') }}</p>
                                                                        </div>
                                                                    @endif
                                                            </td>
                                                            @if (hasPermission('charge_setting_update'))
                                                                <td>
                                                                    <ul class="nk-tb-actions mt-1">
                                                                        <li><a href="javascript:void(0)"
                                                                                data-row="row_{{ $packaging_and_charge->id }}"
                                                                                data-id="{{ $packaging_and_charge->id }}"
                                                                                class="btn btn-sm sg-btn-primary delete-btn-remove" id="delete-btn-remove"><i
                                                                                    class="icon  las la-trash"></i></a>
                                                                        </li>
                                                                    </ul>
                                                                </td>
                                                            @endif
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        @if (hasPermission('charge_setting_update'))
                                            <div class="row">
                                                <div class="col-md-12 text-right mt-4">
                                                    <div class="mb-3">
                                                        <button type="submit"  class="btn sg-btn-primary resubmit">{{ __('update') }}</button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                            </div>
                            @if (hasPermission('charge_setting_update'))
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    {{-- @include('admin.settings.script') --}}
@push('script')
<script>

    $(document).ready(function () {
        $(document).on('click','#add-row',function(e){
            e.preventDefault();
            var url = $('#url').val() ?? path;
            var add_url = $(this).attr('data-url');
            $.ajax({
                type: "GET",
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url + '/' + add_url,
                success: function (data) {
                    $('#package-charge').append(data.view);
                },
                error: function (data) {
                }
            });
        });

    });

    $(document).ready(function(){
        $(document).on('click','.delete-btn-remove',function(){

            var token = "{{ csrf_token() }}";
            var row = $(this).attr('data-row');
            var id = $(this).attr('data-id');

            if (id == ''){
                $('#'+row).remove();
                Swal.fire(
                    'Success!',
                    '{{ __('deleted_successfully') }}',
                    'success'
                )
                return true;
            }

            var url = "{{url('')}}"+'/admin/delete-packaging-charge/'+id;

            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: {
                    _token: token
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                success: function (response) {
                    if (response.success){
                        $('#'+row).remove();
                        Swal.fire(
                            'Success!',
                            response.message,
                            'success'
                        ).then((confirmed) => {
                            location.reload();
                        });
                    }
                    else{
                        Swal.fire(
                            'Oops..!',
                            response.message,
                            'error'
                        )
                    }
                },
                error: function (response) {
                    Swal.fire('Oops..!', response, 'error');
                }

            });
        });
    });
</script>
@endpush
@endsection
