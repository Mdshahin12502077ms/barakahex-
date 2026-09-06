@if($statement->type == 'income')
<td><span>{{ format_price($statement->amount) }} </span></td>
@else
<td class="tb-col-os text-danger"><span>{{ format_price($statement->amount) }} </span></td>
@endif
