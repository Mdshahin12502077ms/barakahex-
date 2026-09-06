<form action="{{ Sentinel::getUser()->user_type == 'merchant' ? route('merchant.update.shop') : (Sentinel::getUser()->user_type == 'merchant_staff' ? route('merchant.staff.update.shop') : route('admin.merchant.update.shop'))}}"
      class="form-validate" method="POST" id="edit-shop-form">
    @csrf

    <div class="tab-content">
        <div class="tab-pane active" id="personal">
            <div class="gy-4">
                    <div class="mb-3">
                        <label class="form-label" for="shop_name">{{__('shop_name')}}<span class="text-danger">*</span></label>
                        <input type="text" name="shop" value="{{ $shop->id }}" hidden>
                        <input type="text" name="shop_name" class="form-control" value="{{ old('shop_name') ? old('shop_name') : $shop->shop_name }}" id="shop_name" placeholder="{{__('shop_name')}}" required>
                        @if($errors->has('shop_name'))
                        <div class="invalid-feedback help-block">
                            <p>{{ $errors->first('shop_name') }}</p>
                        </div>
                    @endif
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="contact_number">{{__('contact_number')}}<span class="text-danger">*</span></label>
                        <input type="text" name="contact_number" class="form-control" value="{{ old('contact_number') ? old('contact_number') : (isDemoMode() ? '**************' : ($shop->contact_number ?? '')) }}" id="contact_number" placeholder="{{__('contact_number')}}" required>
                        @if($errors->has('contact_number'))
                            <div class="invalid-feedback help-block">
                                <p>{{ $errors->first('contact_number') }}</p>
                            </div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="pickup_branch">{{__('pickup_branch')}}<span class="text-danger">*</span></label>
                        <select class="without_search form-control" name="pickup_branch" required>
                            <option value="">{{ __('select_branch') }}</option>
                            @foreach ($branchs as $branch)
                                <option value="{{ @$branch->id }}"
                                    {{ @$shop->pickup_branch_id == $branch->id ? 'selected' : '' }}>
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
                        <label class="form-label" for="shop_phone_number">{{__('pickup_number')}}<span class="text-danger">*</span></label>
                        <input type="text" name="shop_phone_number" class="form-control" value="{{ old('shop_phone_number') ? old('shop_phone_number') : (isDemoMode() ? '**************' : ($shop->shop_phone_number ?? '')) }}" id="shop_phone_number" placeholder="{{__('pickup_number')}}" required>
                        @if($errors->has('shop_phone_number'))
                        <div class="invalid-feedback help-block">
                            <p>{{ $errors->first('shop_phone_number') }}</p>
                        </div>
                    @endif
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="address">{{__('pickup_address')}}<span class="text-danger">*</span></label>
                        <textarea name="address" class="form-control" required>{{ old('address') ? old('address') : $shop->address }}</textarea>
                        @if($errors->has('address'))
                        <div class="invalid-feedback help-block">
                            <p>{{ $errors->first('address') }}</p>
                        </div>
                    @endif
                    </div>

                <div class="col-md-12">
                    <div class="mb-3 text-right">
                        <button type="submit" class="btn sg-btn-primary resubmit">{{__('update')}}</button>
                    </div>
                </div>
            </div>
        </div><!-- .tab-pane -->
    </div><!-- .tab-content -->
</form>
