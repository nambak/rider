@extends('layouts.material')

@section('content')
    <v-app>
        <qr-code-scanner
            :branch-office-id="{{ $branchOfficeID }}"
            user-name="{{ Auth::user()->name }}"
        ></qr-code-scanner>
    </v-app>
@endsection
