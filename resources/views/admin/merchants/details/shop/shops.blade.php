@extends('backend.layouts.master')

@section('title')
    {{__('shops').' '.__('list')}}
@endsection
@section('mainContent')
    <section class="oftions">
        <div class="container-fluid">
            <div class="row">
                @include('admin.merchants.details.menu')
                <div class="col-xxl-9 col-lg-8 col-md-8">
                    <div class="default-tab-list default-tab-list-v2 bg-white redious-border activeItem-bd-none p-20 p-sm-30">
                        <div class="d-flex justify-content-between align-items-center mb-12">
                            <div>
                                <h5>{{ __('shops') }}</h5>
                            </div>
                            @if(hasPermission('merchant_shop_create'))
                            <div class="d-flex">
                                <a href="" data-bs-toggle="modal" data-bs-target="#exampleModal" class="btn sg-btn-primary align-items-center gap-1 d-md-inline-flex"><i class="icon la la-plus"></i><span>{{__('add')}}</span></a>
                            </div>
                            @endif
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="default-list-table table-responsive yajra-dataTable">
                                            {{ $dataTable->table(['class' => 'dt-responsive table'], true) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- add shop modal --}}
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{__('add_shop')}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{route('admin.merchant.add.shop')}}" method="POST" class="form-validate is-alter" id="add-shop-form">
                        @csrf
                        <input type="hidden" name="id" value="" id="delivery-parcel-id">
                        <div class="mb-3">
                            <label class="form-label" for="shop_name">{{__('shop_name')}}</label>
                            <input type="text" name="merchant" hidden id="merchant" value="{{ $merchant->id }}">
                            <input type="text" name="shop_name" class="form-control" value="{{ old('shop_name') }}" id="shop_name" placeholder="{{__('shop_name')}}" required>
                            @if($errors->has('shop_name'))
                                <div class="invalid-feedback help-block">
                                    <p>{{ $errors->first('shop_name') }}</p>
                                </div>
                            @endif
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="contact_number">{{__('contact_number')}}</label>
                            <input type="text" name="contact_number" class="form-control" value="{{ old('contact_number')}}" id="contact_number" placeholder="{{__('contact_number')}}" required>
                            @if($errors->has('contact_number'))
                            <div class="invalid-feedback help-block">
                                <p>{{ $errors->first('contact_number') }}</p>
                            </div>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="pickup_branch">{{__('pickup_branch')}}</label>
                            <select class="without_search form-control" id="pickup_branch" name="pickup_branch">
                                <option value="">{{ __('select_branch') }}</option>
                                @foreach ($branchs as $branch)
                                    <option value="{{ @$branch->id }}">
                                        {{ __(@$branch->name) . ' (' . $branch->address . ')' }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('pickup_branch'))
                                <div class="invalid-feedback help-block">
                                    <p>{{ $errors->first('pickup_branch') }}</p>
                                </div>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="shop_phone_number">{{__('pickup_number')}}</label>
                            <input type="text" name="shop_phone_number" class="form-control" value="{{ old('shop_phone_number')}}" id="shop_phone_number" placeholder="{{__('pickup_number')}}" required>
                            @if($errors->has('shop_phone_number'))
                            <div class="invalid-feedback help-block">
                                <p>{{ $errors->first('shop_phone_number') }}</p>
                            </div>
                        @endif
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="address">{{__('pickup_address')}}</label>
                            <textarea name="address" class="form-control">{{ old('address') }}</textarea>
                            @if($errors->has('address'))
                            <div class="invalid-feedback help-block">
                                <p>{{ $errors->first('address') }}</p>
                            </div>
                        @endif
                        </div>
                        <div class="text-right">
                            <button type="submit" class="btn sg-btn-primary">{{__('submit')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{--edit shop--}}
    <div class="modal fade" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="false" id="edit-shop">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{__('edit_shop')}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="shop_update">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    @include('merchant.delete-ajax')
    @include('merchant.profile.default-shop-ajax')
    <script>
        $(document).on('click','.shop-update', function(e){
            e.preventDefault();
            var url = "{{url('')}}"+'/admin/merchant/shop/edit';
            var shop_id = $(this).attr('data-id');
            var formData = {
                shop_id : shop_id
            }
            $.ajax({
                type: "GET",
                dataType: 'html',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                success: function (data) {
                    $('#shop_update').html(data);
                },
                error: function (data) {
                }
            });
        })
    </script>
@endpush
