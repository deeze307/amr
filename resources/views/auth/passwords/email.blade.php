@extends('angular')
@section('title','Login')
@section('body')
    <style>
        html, body {
            height: 100%;
        }

        .vertical-center {
            min-height: 100%;  /* Fallback for vh unit */
            min-height: 100vh; /* You might also want to use
                        'height' property instead.

                        Note that for percentage values of
                        'height' or 'min-height' properties,
                        the 'height' of the parent element
                        should be specified explicitly.

                        In this case the parent of '.vertical-center'
                        is the <body> element */

            /* Make it a flex container */
            display: -webkit-box;
            display: -moz-box;
            display: -ms-flexbox;
            display: -webkit-flex;
            display: flex;

            /* Align the bootstrap's container vertically */
            -webkit-box-align : center;
            -webkit-align-items : center;
            -moz-box-align : center;
            -ms-flex-align : center;
            align-items : center;

            /* In legacy web browsers such as Firefox 9
               we need to specify the width of the flex container */
            width: 100%;

            /* Also 'margin: 0 auto' doesn't have any effect on flex items in such web browsers
               hence the bootstrap's container won't be aligned to the center anymore.

               Therefore, we should use the following declarations to get it centered again */
            -webkit-box-pack : center;
            -moz-box-pack : center;
            -ms-flex-pack : center;
            -webkit-justify-content : center;
            justify-content : center;
        }
    </style>
<div class="container vertical-center">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Recuperar clave</div>
                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form class="form-horizontal" role="form" method="POST" action="{{ url('/password/email') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">E-Mail Address</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}">

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-btn fa-envelope"></i> Send Password Reset Link
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
