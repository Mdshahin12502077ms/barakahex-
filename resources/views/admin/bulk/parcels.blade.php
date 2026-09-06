@foreach($parcels as $key => $parcel)
    <tr id="row_{{$parcel->parcel_no}}">
        <td>
            <input type="text" name="parcels[]" hidden value="{{$parcel->id}}">
            <span>{{ $key + 1 }}</span>
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
                <li><a href="javascript:void(0)"  data-row="row_{{$parcel->parcel_no}}" class="btn btn-sm btn-danger delete-btn-remove text-white" id="delete-btn-remove"><i class="icon la la-trash"></i></a></li>
            </ul>
        </td>
    </tr>
@endforeach
