@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <order-pickup :details="{{ $order->details }}"></order-pickup>
            </div>
        </div>
    </div>
@endsection
