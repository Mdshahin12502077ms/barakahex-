

@php
    $id = uniqid();
@endphp
<tr id="row_{{ $id }}">
    <td>
        <span class="d-flex justify-content-center" id="weight"></span>
        <input type="hidden" value="" class="form-control weight" name="weights[]">
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
