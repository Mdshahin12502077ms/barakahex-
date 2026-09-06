
@php
    $id = uniqid();
@endphp

<tr id="row_{{ $id }}">
    <td>
        <div>
            <input type="number" class="form-control" id="{{ 'ids_' . $id }}" value="" name="ids[]" hidden>
            <input type="text" class="form-control" id="{{ 'type_' . $id }}" value="" name="packaging_types[]"
                required>
            @if ($errors->has('packaging_types'))
                <div class="invalid-feedback help-block">
                    <p>{{ $errors->first('packaging_types') }}</p>
                </div>
            @endif
        </div>
    </td>
    <td>
        <div>
            <input type="number" class="form-control" id="{{ 'charge_' . $id }}" value="" name="charges[]"
                min="0" required>
            @if ($errors->has('charges'))
                <div class="invalid-feedback help-block">
                    <p>{{ $errors->first('charges') }}</p>
                </div>
            @endif
        </div>
    </td>
    <td>
        <div>
            <ul class="nk-tb-actions mt-1">
                <li><a href="javascript:void(0)" data-row="row_{{ $id }}" data-id = ""
                        class="btn btn-sm sg-btn-primary delete-btn-remove text-white" id="delete-btn-remove"><i
                            class="icon  las la-trash"></i></a></li>
            </ul>
        </div>
    </td>
</tr>
