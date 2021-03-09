{{-- extend layout --}}
@extends('layouts.contentLayoutMaster')

{{-- page title --}}
@section('title', 'Node Setting')

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
                        {{ $facility->name }}-Facility Settings
                    </div>
                </span>
                <form method="POST" action="{{ action('Web\FacilityController@update', ['facility' => $facility->id]) }}">
                    @csrf
                    @method('PATCH')
                    @can('update', $facility)
                    <button class="btn-floating waves-effect waves-light lightrn-1 right" type="submit">
                        <i class="material-icons">save</i>
                    </button>
                    @endcan
                    <div id="inline-form" class="">
                        <div class="row">
                            <div class="input-field col s12">
                                <input id="InputTitle" name="name" type="text" class="validate" value="{{ $facility->name }}"
                                @cannot('update', $facility)
                                    disabled
                                @endcannot>
                                <label for="InputTitle">Name</label>
                            </div>
                            <div class="input-field col s12">
                                <input id="InputDevEui" name="location" type="text" class="validate" value="{{ $facility->location }}"
                                @cannot('update', $facility)
                                    disabled
                                @endcannot>
                                <label for="InputDevEui">Location</label>
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
