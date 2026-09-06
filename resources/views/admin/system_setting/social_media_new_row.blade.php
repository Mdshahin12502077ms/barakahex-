@php
    $id = uniqid();
@endphp
<tr id="row_{{ $id }}">
    <tr>
        <td>

            <input type="text" class="form-control"
                id=""
                value=""
                name="social_media[]">
            @if ($errors->has('social_media'))
                <div class="invalid-feedback help-block">
                    <p>{{ $errors->first('social_media') }}
                    </p>
                </div>
            @endif
        </td>
        <td>
            <input type="text" class="form-control"
                id=""
                value=""
                name="social_media_url[]">
            @if ($errors->has('social_media_url'))
                <div class="invalid-feedback help-block">
                    <p>{{ $errors->first('social_media_url') }}</p>
                </div>
            @endif
        </td>
        <td>
            <ul class="nk-tb-actions mt-1">
                <li>
                    <a href="javascript:void(0)"
                    class="text-white btn btn-sm sg-btn-primary delete-btn-remove text-white"
                    id="add-row"
                    data-url="admin/setting/add/social-media/"><i
                        class="icon  las la-plus"></i></a>
                        <a href="javascript:void(0)"
                        class="btn btn-sm sg-btn-primary delete-row-btn text-white"><i
                            class="icon  las la-trash"></i></a>
                    </a>
                </li>
            </ul>
        </td>
    </tr>
</tr>
