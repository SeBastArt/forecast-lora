{{-- extend layout --}}
@extends('layouts.contentLayoutMaster')

{{-- page title --}}
@section('title', 'Node Setting')

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
                        {{ $Node->name }}-Node Settings
                    </div>
                </span>
                <form method="POST" action="{{ action('Web\NodeController@update', ['node' => $Node->id]) }}">
                    @csrf
                    @method('PATCH')
                    <button class="btn-floating waves-effect waves-light lightrn-1 right" type="submit">
                        <i class="material-icons">save</i>
                    </button>
                    <div id="inline-form" class="">
                        <div class="row">
                            <div class="input-field col s12">
                                <input id="InputTitle" name="name" type="text" class="validate" value="{{ $Node->name }}">
                                <label for="InputTitle">Name</label>
                            </div>
                            <div class="input-field col s12">
                                <input id="InputDevEui" name="dev_eui" type="text" class="validate"
                                    value="{{ $Node->dev_eui }}">
                                <label for="InputDevEui">DevEui</label>
                            </div>
                            <div class="input-field col s12">
                                <select name="nodetype">
                                    <option value="" disabled selected>Choose node type</option>
                                    <option value="1" {{ $Node->node_type_id === 1 ? 'selected="selected"' : '' }}>Decentlab
                                    </option>
                                    <option value="2" {{ $Node->node_type_id === 2 ? 'selected="selected"' : '' }}>Cayenne
                                    </option>
                                    <option value="3" {{ $Node->node_type_id === 3 ? 'selected="selected"' : '' }}>Dragino
                                    </option>
                                    <option value="4" {{ $Node->node_type_id === 4 ? 'selected="selected"' : '' }}>Zane
                                    </option>
                                </select>
                                <label>Node Type Select</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="col s12 card card-default hoverable">
            <div class="card-content ">
                <span class="card-title">
                    <div class="col s10">
                        {{ $Node->name }} Field Settings
                    </div>
                </span>
            </div>
            <ul class="collapsible todo-collection" id="sortable">
                @foreach ($Node->fields->sortBy('position') as $field)
                    <li id="{{ $field->position }}">
                        <div class="collapsible-header">
                            <i class="material-icons icon-move">more_vert</i>{{ $field->name }}
                            <span class="badge z-depth-2"
                                style="background-color: {{ $field->primarycolor }} !important; color: {{ $field->secondarycolor }}"
                                data-badge-caption="color-scheme"></span>
                        </div>
                        <div class="collapsible-body">
                            @include('pages.fields.config', ['Field' => $field])
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
                        <form method="POST" action="{{ action('Web\FieldController@store', ['node' => $Node->id]) }}">
                            @csrf
                            <div id="inline-form" class="">
                                <div class="card-content">
                                    <h4 class="card-title">Add Field to {{ $Node->name }} Node</h4>
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
        </div>

        @if (isset($Fields['primField']))
            <div class="row vertical-modern-dashboard">
                <div class="col s12 m6 l6 animate fadeLeft">
                    <div id="chartjs3" class="card pt-0 pb-0 animate fadeLeft">
                        <div class="dashboard-revenue-wrapper padding-2 ml-2">
                            <p class="mt-2 mb-0 font-weight-600 float-right">max:
                                {{ $Fields['primField']['max'] . $Fields['primField']['unit'] }}<br>min:
                                {{ $Fields['primField']['min'] . $Fields['primField']['unit'] }}
                            </p>
                            <p class="mt-2 mb-0 font-weight-600">{{ $Node->name }}</p>
                            <p class="no-margin grey-text lighten-3">last update:
                                {{ $Fields['primField']['last']['timestamp'] }}
                            </p>
                            <h5 class="grey-text lighten-1">
                                {{ $Fields['primField']['last']['value'] . $Fields['primField']['unit'] }}
                            </h5>
                            @if ($Fields['secField'] != null)
                                <h6 class="grey-text lighten-3">
                                    {{ $Fields['secField']['last'] . $Fields['secField']['unit'] }}
                                </h6>
                            @endif
                        </div>
                        <div class="sample-chart-wrapper card-gradient-chart">
                            <div class="chartjs-size-monitor">
                                <div class="chartjs-size-monitor-expand">
                                    <div class="">
                                    </div>
                                </div>
                                <div class="chartjs-size-monitor-shrink">
                                    <div class="">
                                    </div>
                                </div>
                            </div>
                            <canvas id="simpleLineChart-{{ $Node->id }}" class="center chartjs-render-monitor"
                                style="display: block; height: 272px; width: 422px; margin-top: 60px;" width="474"
                                height="300"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col s12 m6 l6 animate fadeRight">
                    <!-- Total Transaction -->
                    <div class="card">
                        <div class="card-content">
                            <p class="mt-2 mb-0 font-weight-600 float-right">
                                {{ $Fields['primField']['last']['value'] . $Fields['primField']['unit'] }}
                                <br>
                                @if ($Fields['secField'] != null)
                                    {{ $Fields['secField']['last'] . $Fields['secField']['unit'] }}
                                @endif
                            </p>
                            <p class="mt-2 mb-0 font-weight-600">{{ $Node->name }}</p>
                            <p class="no-margin grey-text lighten-3">last update:
                                {{ $Fields['primField']['last']['timestamp'] }}
                            </p>
                            <div class="total-transaction-container">
                                <div id="shadowLineChart-{{ $Node->id }}"
                                    class="total-transaction-line-chart total-transaction-shadow"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
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
    <script src="{{ asset('js/scripts/sort.js') }}"></script>

@endsection
