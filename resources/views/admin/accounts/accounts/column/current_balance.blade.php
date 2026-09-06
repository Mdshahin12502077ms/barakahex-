<div>
    {{format_price( $account->incomes()->sum('amount') + $account->fundReceives()->sum('amount') - $account->expenses()->sum('amount') - $account->fundTransfers()->sum('amount'),2)}}

</div>
