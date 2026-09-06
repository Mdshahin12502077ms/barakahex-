{{ $statement->created_at != '' ? date('M d, Y h:i a', strtotime($statement->created_at)) : '' }}
