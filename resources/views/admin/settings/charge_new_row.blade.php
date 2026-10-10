
@php
    $id = uniqid();
@endphp
<tr id="row_{{ $id }}">
    <td>
        <input type="number" step="0.01" min="0" class="form-control text-center weight" value="" name="weights[]" required>
        <input type="hidden" value="" name="cod_ids[]">
    </td>
    <td>
        <input type="text"
            class="form-control"
            value="" name="same_day[]" required>
    </td>
    <td>
        <input type="text"
            class="form-control"
            value="" name="next_day[]" required>
    </td>
    <td>
        <input type="text"
            class="form-control"
            value="" name="sub_city[]" required>
    </td>
    <td>
        <input type="text"
            class="form-control"
            value=""
            name="sub_urban_area[]" required>
    </td>
    <td>
        <div>
            <ul class="nk-tb-actions mt-1">
                <li class="d-flex justify-content-center align-items-center gap-2">
                    <a href="javascript:void(0)" data-row="row_{{ $id }}" data-id = ""
                        class="btn btn-sm sg-btn-primary delete-btn-remove" id="delete-btn-remove"><i
                            class="icon  las la-trash"></i></a>
                </li>
            </ul>
        </div>
    </td>
</tr>
