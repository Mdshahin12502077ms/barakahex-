<ul class="d-flex gap-3">
    @if(hasPermission('language_read'))
        <li><a href="{{ route('language.translations.page',['lang' => $language->id])}}"><i class="las la-language"></i></a></li>
    @endif
    @if(hasPermission('language_update'))
        <li><a href="javascript:void(0)" class="edit_modal"
               data-fetch_url="{{ route('languages.edit', $language->id) }}"
               data-route="{{ route('languages.update', $language->id) }}" data-modal="language"
            ><i class="las la-edit"></i></a></li>
    @endif
    @if(hasPermission('language_delete'))
        <li><a href="#" onclick="delete_row('{{ route('languages.destroy', $language->id) }}')" data-toggle="tooltip"
               data-original-title="{{ __('delete') }}"><i class="las la-trash-alt"></i></a></li>
    @endif
</ul>
