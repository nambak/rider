@extends('layouts.material')

@section('content')
    <v-app>
        <order-pickup :order-id="{{ $order->id }}"></order-pickup>
    </v-app>
@endsection
