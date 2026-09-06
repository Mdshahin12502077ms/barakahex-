<tr id="row_{{$parcel->parcel_no}}">
    <td>
        <input type="text" name="parcel_list[]" hidden value="{{$parcel->id}}">
        <span>{{ $val }}</span>
    </td>
    <td>
        {{$parcel->parcel_no}}
    </td>
    <td>
        {{ $parcel->customer_name  }}
    </td>
    <td>
        {{ $parcel->customer_address  }}
    </td>
    <td>
        <ul class="gx-1">
            <li><a href="javascript:void(0)"  data-row="row_{{$parcel->parcel_no}}" class="btn btn-sm btn-danger delete-btn-remove text-white" id="delete-btn-remove"><i class="icon  las la-trash"></i></a></li>
        </ul>
    </td>
</tr>
