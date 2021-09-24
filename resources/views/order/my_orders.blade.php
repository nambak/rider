@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <my-orders :order="{{ $order }}"></my-orders>
            </div>
        </div>
    </div>
@endsection
