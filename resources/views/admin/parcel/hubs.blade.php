<option value="">{{ __('select_branch') }}</option>
@foreach($branchs as $branch)
    <option value="{{ $branch->id }}" {{ $branch->id == $pickup_branch ? 'selected':'' }}>{{ __($branch->name).' ('.$branch->address.')' }}</option>
@endforeach
