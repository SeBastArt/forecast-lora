{{-- layout --}}
@extends('layouts.contentLayoutMaster')

{{-- page title --}}
@section('title', 'Company Dashboard')

    {{-- page style --}}
@section('page-style')
    <link rel="stylesheet" type="text/css" href="{{ asset('css/pages/page-users.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/pages/page-companies.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/pages/dashboard.css') }}">
@endsection

{{-- page content --}}
@section('content')    
    @include('panels.workCollection')
@endsection

{{-- page script --}}
@section('page-script')
    <script src="{{ asset('js/scripts/page-users.js') }}"></script>
    <script src="{{ asset('js/scripts/page-companies.js') }}"></script>
@endsection
