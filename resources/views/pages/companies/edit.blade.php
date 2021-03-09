{{-- extend layout --}}
@extends('layouts.contentLayoutMaster')

{{-- page title --}}
@section('title', 'Company Setting')

    {{-- vendor styles --}}
@section('vendor-style')
    <link rel="stylesheet" type="text/css" href="{{ asset('vendors/animate-css/animate.css') }}">
@endsection

{{-- page styles --}}
@section('page-style')
@endsection

{{-- page content --}}
@section('content')
    <div class="section">
        @include('panels.alert')
        <div class="col s12 card card-default hoverable">
            <div class="card-content">
                <span class="card-title">
                    <div class="col c10">
                        {{ $company->name }}-Node Settings
                    </div>
                </span>
                <form method="POST" action="{{ action('Web\CompanyController@update', ['company' => $company->id]) }}">
                    @csrf
                    @method('PATCH')
                    @can('update', $company)
                        <button class="btn-floating waves-effect waves-light lightrn-1 right" type="submit">
                            <i class="material-icons">save</i>
                        </button>
                    @endcan
                    <div id="inline-form" class="">
                        <div class="row">
                            <div class="input-field col s12">
                                <input id="InputTitle" name="name" type="text" class="validate" value="{{ $company->name }}"
                                @cannot('update', $company)
                                    disabled
                                @endcannot>
                                <label for="InputTitle">Name</label>
                            </div>
                            <div class="input-field col s12">
                                <input id="InputDevEui" name="city" type="text" class="validate" value="{{ $company->city }}"
                                @cannot('update', $company)
                                    disabled
                                @endcannot>
                                <label for="InputDevEui">City</label>
                            </div>
                            <div class="input-field col s12">
                                <input id="InputCountry" name="country" type="text" class="validate" value="{{ $company->country }}"
                                @cannot('update', $company)
                                    disabled
                                @endcannot>
                                <label for="InputCountry">Country</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

{{-- vendor scripts --}}
@section('vendor-script')
    @stack('config-vendor-scripts')
    <script src="{{ asset('fonts/fontawesome/js/all.js') }}"></script>
    <script type="text/javascript" src="https://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
@endsection

{{-- page scripts --}}
@section('page-script')
@endsection
