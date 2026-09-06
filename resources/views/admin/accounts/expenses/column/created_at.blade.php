<div>
    {{$expense->date != "" ? date('M d, Y', strtotime($expense->date)) : ''}} <br>
    {{date('M d, Y h:i a', strtotime($expense->created_at))}}
</div>
