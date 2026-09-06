<option value="">{{ __('select_thana_union') }}</option>
@foreach($thana_unions as $thana_union)
    <option value="{{ $thana_union->id }}">{{ __('thana').': '.$thana_union->thana_name.', '.__('union_para').': '.$thana_union->union_para_name }}</option>
@endforeach
