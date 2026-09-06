<option value="">{{ __('select_branch') }}</option>
@foreach($branchs as $branch)
    <option value="{{ $branch->id }}">{{ $branch->name . (!empty($branch->type) ? ' [' . ucfirst(str_replace('_', ' ', $branch->type)) . ']' : '') . ' (' . $branch->address . ')' }}</option>
@endforeach
