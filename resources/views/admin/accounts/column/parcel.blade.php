<div class="">
    @if($income->parcel_id != "")
      <a  class="copy-to-clipboard" href="javascript:void(0)" data-text="{{@$income->parcel->parcel_no}}">{{ __('id').'# ' }} {{@$income->parcel->parcel_no}} <i class="la la-copy"></i></a>
    @endif
 </div>
