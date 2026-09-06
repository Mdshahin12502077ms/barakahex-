@if($batch_type == 'bank')
    <table>
    <thead>
    <tr>
        <th>#</th>
        <th>Payment ID</th>
        <th>Bank Name</th>
        <th>Branch</th>
        <th>Account Holder</th>
        <th>Account Number</th>
        <th>Routing No.</th>
        <th>Amount</th>
        <th>Remarks</th>
    </tr>
    </thead>
    <tbody>
    @foreach($withdraws as $key => $withdraw)
        @php
            $account_details = json_decode($withdraw->account_details);
        @endphp
        <tr>
            <td>{{ $key+1 }}</td>
            <td>{{ $withdraw->withdraw_id }}</td>
            <td>{{ $account_details[0] }}</td>
            <td>{{ $account_details[1] }}</td>
            <td>{{ $account_details[2] }}</td>
            <td>{{ $account_details[3] }}</td>
            <td>{{ @$account_details[4] }}</td>
            <td>{{ $withdraw->amount }}</td>
        </tr>
    @endforeach
    <tr>
        <td colspan="7">Total</td>
        <td>{{ $withdraws->sum('amount') }}</td>
        <td colspan="1"></td>
    </tr>
    </tbody>
</table>
@else
    <table>
        <thead>
        <tr>
            <th>#</th>
            <th>Payment ID</th>
            <th>Account Number</th>
            <th>Account Type</th>
            <th>Amount</th>
            <th>Remarks</th>
        </tr>
        </thead>
        <tbody>
        @foreach($withdraws as $key => $withdraw)
            @php
                $account_details = json_decode($withdraw->account_details);
            @endphp
            <tr>
                <td>{{ $key+1 }}</td>
                <td>{{ $withdraw->withdraw_id }}</td>
                <td>{{ $account_details[1] }}</td>
                <td>{{ $account_details[2] }}</td>
                <td>{{ $withdraw->amount }}</td>
            </tr>
        @endforeach
        <tr>
            <td colspan="4">Total</td>
            <td>{{ $withdraws->sum('amount') }}</td>
            <td colspan="1"></td>
        </tr>
        </tbody>
    </table>
@endif
