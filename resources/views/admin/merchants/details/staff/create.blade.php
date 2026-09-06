@extends('backend.layouts.master')

@section('title')
    {{__('add')}} {{__('staff')}}
@endsection

@section('mainContent')
<div class="container-fluid">
    <div class="row gx-20">
        <div class="col-lg-12">
                <div class="header-top d-flex justify-content-between align-items-center mb-12">
                    <div>
                        <h3 class="section-title">{{__('add')}} {{__('staff')}}</h3>

                    </div>
                    <div>
                        <a href="{{url()->previous()}}" class="d-flex align-items-center btn sg-btn-primary gap-2"><i class="icon las la-arrow-left"></i><span>{{__('back')}}</span></a>
                    </div>
                </div>
                <div class="bg-white redious-border p-20 p-sm-30">
                    <form action="{{ route('detail.merchant.staff.store')}}"  method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card-inner">
                                    <div class="row g-gs">
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label class="form-label" for="fv-full-name">{{__('first_name')}}  <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="fv-full-name" name="first_name" required value="{{old('first_name')}}">
                                                @if($errors->has('first_name'))
                                                    <div class="invalid-feedback help-block">
                                                        <p>{{ $errors->first('first_name') }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label class="form-label" for="fv-full-name">{{__('last_name')}}</label>
                                                    <input type="text" class="form-control" id="fv-full-name" name="last_name" value="{{old('last_name')}}">
                                                    <input type="hidden" class="form-control" name="merchant" value="{{ $merchant->id }}">
                                                @if($errors->has('last_name'))
                                                    <div class="invalid-feedback help-block">
                                                        <p>{{ $errors->first('last_name') }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label class="form-label" for="phone_number">{{__('phone_number')}}  <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="phone_number" name="phone_number" required value="{{old('phone_number')}}">
                                                @if($errors->has('phone_number'))
                                                    <div class="invalid-feedback help-block">
                                                        <p>{{ $errors->first('phone_number') }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label class="form-label" for="fv-email">{{__('email')}}  <span class="text-danger">*</span></label>
                                                    <input type="email" class="form-control" id="fv-email" name="email" required value="{{old('email')}}">
                                                @if($errors->has('email'))
                                                    <div class="invalid-feedback help-block">
                                                        <p>{{ $errors->first('email') }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label class="form-label" for="fv-email">{{__('password')}}  <span class="text-danger">*</span></label>
                                                    <input type="password" class="form-control" id="fv-email" name="password" required value="{{old('password')}}">
                                                @if($errors->has('password'))
                                                    <div class="invalid-feedback help-block">
                                                        <p>{{ $errors->first('password') }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-lg-12 input_file_div">
                                            <div class="mb-3 mt-2">
                                                <label class="form-label mb-1">{{__('profile_image') }}</label>
                                                <input class="form-control sp_file_input file_picker" type="file" id="image"
                                                        name="image_id" accept="image/*">
                                            </div>
                                            <div class="selected-files d-flex flex-wrap gap-20">
                                                <div class="selected-files-item">
                                                    <img class="selected-img" src="{{ getFileLink('original_image',[]) }}"
                                                            alt="favicon">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="card-inner">
                                    <table class="table table-bordered role-create-table role-permission" id="permissions-table">
                                        <thead>
                                            <tr>
                                                <th scope="col">{{__('modules')}}</th>
                                                <th scope="col">{{__('permission')}}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><span class="text-capitalize">{{__('parcel')}}</span></td>
                                                <td>
                                                    <div class="custom-checkbox mb-2">
                                                        <label class="custom-control-label" for="manage_parcel">
                                                            <input type="checkbox" class="custom-control-input read common-key" id="{{'manage_parcel'}}" name="permissions[]" value="manage_parcel">
                                                            <span class="text-capitalize">{{ __('allow') }}</span>

                                                        </label>
                                                    </div>

                                                    <div class="custom-checkbox mb-2">
                                                        <label class="custom-control-label" for="all_parcel">
                                                            <input type="checkbox" class="custom-control-input read common-key" id="{{'all_parcel'}}" name="permissions[]" value="all_parcel">
                                                            <span class="text-capitalize">{{ __('allow_all') }}</span>

                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><span class="text-capitalize">{{__('payment')}}</span></td>
                                                <td>
                                                    <div class="custom-checkbox mb-2">
                                                        <label class="custom-control-label" for="manage_payment">
                                                            <input type="checkbox" class="custom-control-input read common-key" id="{{'manage_payment'}}" name="permissions[]" value="manage_payment">
                                                            <span class="text-capitalize">{{ __('allow') }}</span>

                                                        </label>
                                                    </div>

                                                    <div class="custom-checkbox mb-2">
                                                        <label class="custom-control-label" for="all_parcel_payment">
                                                            <input type="checkbox" class="custom-control-input read common-key" id="{{'all_parcel_payment'}}" name="permissions[]" value="all_parcel_payment">
                                                            <span class="text-capitalize">{{ __('allow_all') }}</span>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><span class="text-capitalize">{{__('logs')}}</span></td>
                                                <td>
                                                    <div class="custom-checkbox mb-2">
                                                        <label class="custom-control-label" for="read_logs">
                                                            <input type="checkbox" class="custom-control-input read common-key" id="{{'read_logs'}}" name="permissions[]" value="read_logs">
                                                            <span class="text-capitalize">{{ __('read_logs') }}</span>

                                                        </label>
                                                    </div>

                                                    <div class="custom-checkbox mb-2">
                                                        <label class="custom-control-label" for="all_parcel_logs">
                                                            <input type="checkbox" class="custom-control-input read common-key" id="{{'all_parcel_logs'}}" name="permissions[]" value="all_parcel_logs">
                                                            <span class="text-capitalize">{{ __('all_parcel_logs') }}</span>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><span class="text-capitalize">{{__('others_access')}}</span></td>
                                                <td>
                                                    <div class="custom-checkbox mb-2">
                                                        <label class="custom-control-label" for="manage_company_information">
                                                            <input type="checkbox" class="custom-control-input read" id="{{'manage_company_information'}}" name="permissions[]" value="manage_company_information">
                                                            <span class="text-capitalize">{{ __('manage_company_information') }}</span>

                                                        </label>
                                                    </div>

                                                    <div class="custom-checkbox mb-2">
                                                        <label class="custom-control-label" for="manage_payment_accounts">
                                                            <input type="checkbox" class="custom-control-input read" id="{{'manage_payment_accounts'}}" name="permissions[]" value="manage_payment_accounts">
                                                            <span class="text-capitalize">{{ __('manage_payment_accounts') }}</span>
                                                        </label>
                                                    </div>
                                                    <div class="custom-checkbox mb-2">
                                                        <label class="custom-control-label" for="manage_shops">
                                                            <input type="checkbox" class="custom-control-input read" id="{{'manage_shops'}}" name="permissions[]" value="manage_shops">
                                                            <span class="text-capitalize">{{ __('manage_shops') }}</span>

                                                        </label>
                                                    </div>

                                                    <div class="custom-checkbox mb-2">
                                                        <label class="custom-control-label" for="delivery_charge">
                                                            <input type="checkbox" class="custom-control-input read" id="{{'delivery_charge'}}" name="permissions[]" value="delivery_charge">
                                                            <span class="text-capitalize">{{ __('delivery_charge') }}</span>
                                                        </label>
                                                    </div>

                                                    <div class="custom-checkbox mb-2">
                                                        <label class="custom-control-label" for="cash_on_delivery_charge">
                                                            <input type="checkbox" class="custom-control-input read" id="{{'cash_on_delivery_charge'}}" name="permissions[]" value="cash_on_delivery_charge">
                                                            <span class="text-capitalize">{{ __('cash_on_delivery_charge') }}</span>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><span class="text-capitalize">{{__('shops_access')}}</span></td>
                                                <td>
                                                    @foreach($merchant->shops as $shop)
                                                    <div class="custom-control custom-checkbox mb-2">
                                                        <label class="custom-control-label" for="shop-{{ $shop->id }}">
                                                            <input type="checkbox" class="custom-control-input read" id="shop-{{ $shop->id }}" name="shops[]"  value="{{ $shop->id }}">
                                                            <span class="text-capitalize">{{ $shop->shop_name.' ('.$shop->address.')' }}</span>
                                                        </label>
                                                    </div>
                                                    @endforeach
                                                    @if($errors->has('shops'))
                                                        <div class="invalid-feedback help-block">
                                                            <p>{{ $errors->first('shops') }}</p>
                                                        </div>
                                                    @endif
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <div class="d-flex justify-content-end align-items-center mt-30">
                                        <button type="submit" class="btn sg-btn-primary resubmit">{{__('submit') }}</button>
                                        @include('backend.common.loading-btn',['class' => 'btn sg-btn-primary'])
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @include('admin.roles.script')
    @push('script')
        <script>
            $(document).on("change", ".file_picker", function(e) {
                let file = e.target.files[0];
                let selector = $(this).closest(".input_file_div");
                selector.find(".file-upload-text").text(file.name);
                selector
                    .find(".selected-img")
                    .attr("src", URL.createObjectURL(file));
            });
        </script>
    @endpush
@endsection




@include('admin.roles.script')
