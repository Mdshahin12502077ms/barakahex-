@if ($query['type'] == 'income')
    <td>
        {{ $query['amount'] }}
    </td>
@elseif($query['type'] == 'expense')
    <td class="nk-tb-col text-danger">
        {{ $query['amount'] }}
    </td>
@endif
