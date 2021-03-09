{{-- extend layout --}}
@extends('layouts.contentLayoutMaster')

{{-- page title --}}
@section('title', 'Preset Setting')

    {{-- vendor styles --}}
@section('vendor-style')
    <link rel="stylesheet" type="text/css" href="{{ asset('vendors/animate-css/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('vendors/chartist-js/chartist.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('vendors/chartist-js/chartist-plugin-tooltip.css') }}">
@endsection

{{-- page styles --}}
@section('page-style')
    <link rel="stylesheet" type="text/css" href="{{ asset('css/pages/dashboard-modern.css') }}">
@endsection

{{-- page content --}}
@section('content')
    <div class="section">
        @include('panels.alert')
        <div class="col s12 card card-default hoverable">
            <div class="card-content">
                <span class="card-title">
                    <div class="col c10">
                        {{ $preset->name }}-Preset Settings
                    </div>
                </span>
                <form method="POST" action="{{ action('Web\PresetController@update', ['preset' => $preset->id]) }}">
                    @csrf
                    @method('PATCH')
                    @can('update', $preset)
                        <button class="btn-floating waves-effect waves-light lightrn-1 right" type="submit">
                            <i class="material-icons">save</i>
                        </button>
                    @endcan
                    <div id="inline-form" class="">
                        <div class="row">
                            <div class="input-field col s12">
                                <input id="InputTitle" name="name" type="text" class="validate" value="{{ $preset->name }}" 
                                @cannot('update', $preset)
                                  disabled 
                                @endcannot  >
                                <label for="InputTitle">Name</label>
                            </div>
                            <div class="input-field col s12">
                                <input id="InputDescription" name="description" type="text" class="validate"
                                    value="{{ $preset->description }}" 
                                    @cannot('update', $preset)
                                        disabled 
                                    @endcannot >
                                <label for="InputDescription">Description</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @can('update', $preset)
        <div class="col s12 card card-default hoverable">
            <div class="card-content ">
                <h4 class="card-title">{{ $preset->name }} Preset Settings</h4>
          
            <ul class="collapsible todo-collection">
                @foreach ($preset->fields->sortBy('created_at') as $field)
                    <li>
                        <div class="collapsible-header">
                            <i class="material-icons icon-move">more_vert</i>{{ $field->name }}
                            <span class="badge z-depth-2"
                                style="background-color: {{ $field->primary_color }} !important; color: {{ $field->secondary_color }}"
                                data-badge-caption="color-scheme"></span>
                        </div>
                        <div class="collapsible-body">
                            @include('pages.fields.config', ['field' => $field])
                        </div>
                    </li>
                @endforeach
            </ul>
            <ul class="collapsible">
                <li>
                    <div class="collapsible-header grey lighten-3">
                        <i class="material-icons">add</i>
                        new Field
                    </div>
                    <div class="collapsible-body">
                        
                        <form method="POST" action="{{ action('Web\FieldController@storePreset', ['preset' => $preset->id]) }}">
                            @csrf
                            <input type="hidden" value="{{$preset->id}}" name="preset_id">
                            <div id="inline-form" class="">
                                <div class="card-content">
                                    <h4 class="card-title">Add Field to {{ $preset->name }} Preset</h4>
                                    <div class="row">
                                        <div class="input-field col s12">
                                            <i class="material-icons prefix">create</i>
                                            <input id="InputFieldName" name="name" type="text" class="validate">
                                            <label for="InputFieldName">Name</label>
                                        </div>
                                        <div class="input-field col s12">
                                            <i class="material-icons prefix">fingerprint</i>
                                            <input id="InputUnit" name="unit" type="text" class="validate">
                                            <label for="InputUnit">Unit</label>
                                        </div>
                                        <div class="input-field col s12">
                                            <button class="btn waves-effect waves-light right" type="submit"
                                                name="action">Add Field
                                                <i class="material-icons right">send</i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </li>
            </ul>
         
            <button class="btn waves-effect waves-light mb-2 red right" onclick="confirmSpread('{{ action('Web\PresetController@spread', ['preset' => $preset->id]) }}')" type="button"><i class="material-icons left">publish</i>Publish Preset</button>
 
        </div>
        </div>
       
        @endcan
    </div>
@endsection

{{-- vendor scripts --}}
@section('vendor-script')
    @stack('config-vendor-scripts')
    <script src="{{ asset('fonts/fontawesome/js/all.js') }}"></script>
    <script type="text/javascript" src="https://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
    <script src="{{ asset('vendors/chartist-js/chartist.js') }}"></script>
    <script src="{{ asset('vendors/chartist-js/chartist-plugin-tooltip.js') }}"></script>
    <script src="{{ asset('vendors/chartist-js/chartist-plugin-fill-donut.min.js') }}"></script>
@endsection

{{-- page scripts --}}
@section('page-script')
    @stack('config-scripts')
    <script src="https://momentjs.com/downloads/moment.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.js"></script>
    <script type="module" src="{{ asset('js/scripts/charts.js') }}"></script>
    <script type="module" src="{{ asset('js/scripts/chartjs-plugin-annotation.js') }}"></script>

@endsection
