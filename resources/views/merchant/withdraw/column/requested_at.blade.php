<td>
    {{ $withdraw->created_at != '' ? date('M d, Y h:i a', strtotime($withdraw->created_at)) : '' }}
</td>
