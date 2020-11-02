{{-- extend layout --}}
@extends('layouts.contentLayoutMaster')

{{-- page title --}}
@section('title', 'Token Overview')

    {{-- vendor styles --}}
@section('vendor-style')
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/3.6.95/css/materialdesignicons.css"
        crossorigin="anonymous">
@endsection

{{-- page content --}}
@section('content')
    <div class="section">
        Nothing to see here
    </div>
@endsection

{{-- vendor scripts --}}
@section('vendor-script')
    <script src="{{ asset('fonts/fontawesome/js/all.js') }}"></script>
@endsection

{{-- page scripts --}}
@section('page-script')
@endsection
