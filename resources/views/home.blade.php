@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <qr-code-scanner :branch-office-id="{{ $branchOfficeID }}"></qr-code-scanner>
        </div>
    </div>
</div>
@endsection
