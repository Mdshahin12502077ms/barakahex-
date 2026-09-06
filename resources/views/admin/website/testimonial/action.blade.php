<ul class="d-flex gap-3">
    @if (hasPermission('testimonial.edit'))
        <li>
            <a href="{{ route('testimonials.edit',$testimonial->id) }}"><i
                        class="las la-edit"></i></a>
        </li>
    @endif
    @if (hasPermission('testimonial.destroy'))
        <li>
            <a href="javascript:void(0)"
                onclick="delete_row('{{ route('testimonials.destroy', $testimonial->id) }}')"
                data-toggle="tooltip"
                data-original-title="{{ __('delete') }}"><i class="las la-trash-alt"></i></a>
        </li>
    @endif
</ul>
