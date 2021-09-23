@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <delivery-complete order-id="{{ $order->id }}"></delivery-complete>
            </div>
        </div>
    </div>
@endsection

