<option value="">{{ __('select_batch') }}</option>
@foreach($batches as $batch)
    <option value="{{ $batch->id }}" {{ $withdraw->withdraw_batch_id == $batch->id ? 'selected' : '' }}>
         {{ $batch->batch_no.' ('.$batch->title.')' }}
    </option>
@endforeach
