@extends('layouts.app')
@section('content')

    <div class="container">

        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($message = Session::get('success'))
            <div class="alert alert-success alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
            </div>
        @endif

            <h1>{{ $infoarray[0] }}</h1>
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header"><h3>{{ $infoarray[1] }}</h3></div>

                        <div class="card-body">
                            @if (session('status'))
                                <div class="alert alert-success" role="alert">
                                    {{ session('status') }}
                                </div>
                            @endif

                                <form action="/parstate/submit" method="post">
                                    @csrf
                                    <div class="form-group">
                                        @foreach ($parstatedefines as $key => $value)
                                            <input type="radio" id={{ $key }} name="parstate_id" value={{ $key }} <?php echo ($infoarray[2]==$key)?'checked':'' ?> />
                                                <label class="label" for={{ $value }}> {{ $value }} </label><br>
                                        @endforeach
                                        <h1> </h1>
                                        <button type="submit" class="btn btn-primary">Lage melden</button>
                                    </div>
                                </form>
                        </div>
                    </div>
                </div>
            </div>
    </div>

@endsection
