<!DOCTYPE html>
<html>
<head>
    <title>Import Export Excel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
</head>
<body>

<div class="container">
    <div class="card bg-light mt-3">
        <div class="card-header">
            Import Export Excel to database Example
        </div>
        @if(isset($errors) && $errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error)
                    {{ $error }}
                @endforeach
            </div>
        @endif
        <div class="card-body">
            <form action="{{ route('import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="file" name="file" class="form-control">
                <br>
                <button class="btn btn-success">Import User Data</button>
                <a class="btn btn-warning" href="{{ route('export') }}">Export User Data</a>

                @if($errors->has('merchant'))
                    <div class="invalid-feedback help-block">
                        <p>{{ $errors->first('merchant') }}</p>
                    </div>
                @endif
            </form>
        </div>
    </div>
</div>

</body>
</html>
