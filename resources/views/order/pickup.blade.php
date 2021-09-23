@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <order-pickup :order-id="{{ $order->id }}"></order-pickup>
            </div>
        </div>
    </div>
@endsection
