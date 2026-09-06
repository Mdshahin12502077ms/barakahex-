<div class="">
    {{$income->date != "" ? date('M d, Y', strtotime($income->date)) : ''}} <br>
    {{date('M d, Y h:i a', strtotime($income->created_at))}}
</div>
