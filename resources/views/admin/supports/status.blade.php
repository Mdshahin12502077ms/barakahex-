@if(hasPermission('support_ticket_update'))
<select name="status" id="status" class="form-select form-select-lg" style="min-width:100px" data-id="{{ $ticket->id }}">
    <option value="new" {{ $ticket->status == 'new' ? 'selected' : '' }}>New</option>
    <option value="processing" {{ $ticket->status == 'processing' ? 'selected' : '' }}>Processing</option>
    <option value="resolved" {{ $ticket->status == 'resolved' ? 'selected' : '' }}>Resolved</option>
    <option value="closed" {{ $ticket->status == 'closed' ? 'selected' : '' }}>Closed</option>
</select>
@else
<span class="badge bg-secondary">{{ ucfirst($ticket->status) }}</span>
@endif