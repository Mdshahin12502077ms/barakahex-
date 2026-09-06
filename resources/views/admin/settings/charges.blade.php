@extends('backend.layouts.master')
@section('title')
    {{ __('charges') }}
@endsection
@section('mainContent')
    <section class="oftions">
        <div class="container-fluid">
            <div class="row">
                <div class="d-flex justify-content-center align-items-center">
                    <div>
                        <div class="col-xxl-12 col-lg-12 col-md-12 bg-white redious-border p-20 p-sm-30">
                            <div class="mb-12">
                                <h5>{{ __('parcel_return_charges') }}</h5>
                            </div>
                            @if (hasPermission('charge_setting_update'))
                                <form action="{{ route('setting.store') }}" class="form-validate" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                            @endif
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card-inner">
                                        <div class="row g-gs">
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label class="form-label">{{ __('select') . ' ' . __('return_charge_type') }}
                                                        <span class="text-danger">*</span></label>
                                                    <select class="without_search form-select form-control return-charge-type"
                                                        name="return_charge_type" >
                                                        <option value="">{{ __('select_type') }}</option>
                                                        <option value="on_demand"
                                                            {{ settingHelper('return_charge_type') == 'on_demand' ? 'selected' : '' }}>
                                                            {{ __('on_demand') }}</option>
                                                        <option value="full_delivery_charge"
                                                            {{ settingHelper('return_charge_type') == 'full_delivery_charge' ? 'selected' : '' }}>
                                                            {{ __('full_delivery_charge') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label class="form-label" for="return_charge_city">{{ __('city') }} ({{ setting('default_currency') }})
                                                        <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="return_charge_city"
                                                        value="{{ settingHelper('return_charge_city') }}"
                                                        name="return_charge_city" >
                                                    @if ($errors->has('return_charge_city'))
                                                        <div class="invalid-feedback help-block">
                                                            <p>{{ $errors->first('return_charge_city') }}</p>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label class="form-label" for="return_charge_sub_city">{{ __('sub_city') }} ({{ setting('default_currency') }})
                                                        <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="return_charge_sub_city"
                                                        value="{{ settingHelper('return_charge_sub_city') }}"
                                                        name="return_charge_sub_city" >
                                                    @if ($errors->has('return_charge_sub_city'))
                                                        <div class="invalid-feedback help-block">
                                                            <p>{{ $errors->first('return_charge_sub_city') }}</p>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label class="form-label"
                                                        for="return_charge_outside_dhaka">{{ __('sub_urban_area') }} ({{ setting('default_currency') }})
                                                        <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="return_charge_outside_dhaka"
                                                        value="{{ settingHelper('return_charge_outside_dhaka') }}"
                                                        name="return_charge_outside_dhaka" >
                                                    @if ($errors->has('return_charge_outside_dhaka'))
                                                        <div class="invalid-feedback help-block">
                                                            <p>{{ $errors->first('return_charge_outside_dhaka') }}
                                                            </p>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label class="form-label" for="fragile_charge">{{ __('fragile_charge') }} ({{ setting('default_currency') }}) <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="fragile_charge"
                                                        value="{{ settingHelper('fragile_charge') }}" name="fragile_charge"
                                                        >
                                                    @if ($errors->has('fragile_charge'))
                                                        <div class="invalid-feedback help-block">
                                                            <p>{{ $errors->first('fragile_charge') }}</p>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            @if (hasPermission('charge_setting_update'))
                                                <div class="col-md-3 pt-4 ">
                                                    <div class="d-flex mt-1">
                                                        <button type="submit"
                                                        class="btn sg-btn-primary resubmit">{{ __('update') }}</button>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if (hasPermission('charge_setting_update'))
                                </form>
                            @endif
                        </div>

                        <div class="col-xxl-12 col-lg-12 col-md-12 mt-5 bg-white redious-border p-20 p-sm-30">
                            <form action="{{ route('update.charge') }}" method="POST">
                                @csrf
                                <div class="table-responsive">
                                    <table class="table table-bordered role-create-table mt-3"
                                        id="permissions-table"
                                        style="background-color: rgb(245,255,251);">
                                        <thead>
                                            <tr>
                                                <th scope="col">{{ __('weight') }} ({{ setting('default_weight') }})</th>
                                                <th scope="col">{{ __('same_day') }} ({{ __('same_city') }} {{ setting('default_currency') }})</th>
                                                <!-- <th scope="col">{{ __('next_day') }} ({{ __('same_city') }} {{ setting('default_currency') }})</th> -->
                                                <th scope="col">{{ __('sub_city') }} ({{ setting('default_currency') }})</th>
                                                <th scope="col">{{ __('sub_urban_area') }} ({{ setting('default_currency') }})</th>
                                            </tr>
                                        </thead>
                                        <tbody id="charge">
                                            @foreach ($charges as $charge)
                                            <tr>
                                                <td>
                                                    <span class="d-flex justify-content-center">{{ $charge->weight }}</span>
                                                    <input type="hidden" value="{{ $charge->weight }}" name="weights[]">
                                                    <input type="hidden" value="{{ $charge->id }}" name="cod_ids[]">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" value="{{ $charge->same_day }}" name="same_day[]" required>
    
                                                </td>
                                                <!-- <td>
                                                    <input type="text" class="form-control" value="{{ $charge->next_day }}" name="next_day[]" required>
    
                                                </td> -->
                                                <td>
                                                    <input type="text" class="form-control" value="{{ $charge->sub_city }}" name="sub_city[]" required>
    
    
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" value="{{ $charge->sub_urban_area }}" name="sub_urban_area[]" required>
    
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>

                                </div>
                                <div class="d-flex gap-2  justify-content-end">
                                    <div class="d-flex justify-content-end mb-3">
                                        <a href="javascript:void(0)"
                                        class="text-white btn sg-btn-primary text-white"
                                        id="add-charge-row"
                                        data-url="admin/add-charge-row/"><i
                                            class="icon  las la-plus"></i> {{ __('add') }}</a>
                                    </div>
                                    <div class="mb-3 d-flex align-items-center">
                                        <span>
                                            <button type="submit" class="btn sg-btn-primary">{{ __('submit') }}</button>
                                        </span>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('script')
        <script>
            $(document).ready(function () {
                var last_weight = {{ $charges->max('weight') + 1 }};

                $(document).on('click', '#add-charge-row', function (e) {
                    e.preventDefault();
                    var url     = $('#url').val() || path;
                    var add_url = $(this).data('url');

                    $.ajax({
                        type: "GET",
                        dataType: 'json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: url + '/' + add_url,
                        success: function (data) {
                            var newRow = $(data.view);
                            $('#charge').append(newRow);
                            var last = last_weight++;
                            newRow.find('#weight').text(last);
                            newRow.find('.weight').val(last);
                        },
                        error: function (xhr, status, error) {
                            console.error(xhr.responseText);
                        }
                    });
                });

                $(document).on('click', '.delete-btn-remove', function () {
                    $(this).closest('tr').remove();
                    last_weight--;
                });
            });

        </script>
    @endpush
@endsection
