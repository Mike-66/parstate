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
            <h3>{{ $infoarray[1] }}</h3>
            <h3>{{ $infoarray[2] }}</h3>
            <h2>{{ $infoarray[3] }}</h2>

    </div>

@endsection
