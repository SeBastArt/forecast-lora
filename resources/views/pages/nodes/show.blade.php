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

        @if ( $node->getErrorLevel() > 0)
        <div class="col s12 card card-default hoverable">
            <div class="card-content">
                <span class="card-title">
                    <div class="col s12">
                        Limit Exeeded on Field <b class="red-text lighten-2 h5">{{$alert['field_name']}}</b>
                    </div>
                </span>
                <div class="input-field col s12">
                    <form method="Get" action="{{ action('Web\NodeController@alert_reset', ['node' => $node->id]) }}">
                        <button class="btn red lighten-2 waves-effect waves-light col s12 mb-1 z-depth-2" type="submit" name="action">
                        Reset Alert</button>
                    </form>
                    <form method="Get" action="{{ action('Web\NodeController@show', ['node' => $node->id]) }}">
                        <input type="hidden" name="timestamp" value="{{$alert['alertTimestamp']}}">
                        <input type="hidden" id="upper_limit" value="{{$alert['upper_limit']}}">
                        <input type="hidden" id="lower_limit" value="{{$alert['lower_limit']}}">
                        <button class="btn blue lighten-2 waves-effect waves-light col s12 mb-2 z-depth-2" type="submit" name="action">
                        Show Alert</button>
                    </form>
                </div>
            </div>
        </div>
        @endif
        <div class="col s12 card card-default hoverable">
            <div id="form-with-validation" >
              <div class="card-content">
                <h4 class="card-title">Select Timepspan</h4>
                <form method="Get" action="{{ action('Web\NodeController@show', ['node' => $node->id]) }}">
                    @if (isset($time['timestamp']))
                        <input type="hidden" id="timestamp" value="{{$time['timestamp']}}"> 
                    @endif
                  <div class="row">
                    <div class="hide-on-small-only input-field col m1">
                    </div>
                    <div class="input-field col m2 s12">
                      <i class="material-icons prefix">date_range</i>
                      <input id="start_date" type="text" class="datepicker" name="startDate" value="{{$time['startDate']}}">
                      <label for="start_date">Start Date</label>
                    </div>
                    <div class="input-field col m2 s12">
                      <i class="material-icons prefix">access_time</i>
                      <input id="start_time" type="text" class="timepicker" name="startTime" value="{{$time['startTime']}}">
                      <label for="start_time">Start Time</label>
                    </div>
                    <div class="input-field col m2 s12 hide-on-small-only center-align">
                        <button class="btn cyan waves-effect waves-light  z-depth-2" type="submit" name="action">
                        <i class="material-icons left">refresh</i>Refresh</button>
                    </div>
                    <div class="input-field col m2 s12">
                        <i class="material-icons prefix">date_range</i>
                        <input id="end_date" type="text" class="datepicker" name="endDate" value="{{$time['endDate']}}">
                        <label for="end_date">End Date</label>
                      </div>
                      <div class="input-field col m2 s12">
                        <i class="material-icons prefix">access_time</i>
                        <input id="end_time" type="text" class="timepicker" name="endTime" value="{{$time['endTime']}}">
                        <label for="end_time">End Time</label>
                      </div>
                        <div class="input-field col s12 hide-on-med-and-up">
                            <button class="btn cyan waves-effect waves-light z-depth-2" type="submit" name="action">
                            <i class="material-icons left">refresh</i> Refresh</button>
                        </div>
                        <div class="hide-on-small-only input-field col m1">
                        </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
            <div class="row vertical-modern-dashboard">
                <div class="col s12 animate fadeLeft">
                    <div id="chartjs3" class="card animate fadeLeft">
                        <div class="dashboard-revenue-wrapper padding-2 ml-2">
                            <div class="mt-0 mb-0 font-weight-600 float-right">
                              
                                <div class="right" id='max_{{$node->id}}'>max: --</div>
                                <br>
                                <div class="right" id='min_{{$node->id}}'>min: --</div>
                                <div class="hide-on-small-only">
                                    <form method="Get" action="{{ action('Api\NodeDataApiController@csvdata', ['node' => $node->id]) }}">
                                        <input type="hidden" name="startDate" value="{{$time['startDate'].' '.$time['startTime']}}"> 
                                        <input type="hidden" name="endDate" value="{{$time['endDate'].' '.$time['endTime']}}"> 
                                        <button class="btn blue lighten-2 waves-effect waves-light col s12 z-depth-2" type="submit" name="action">
                                            <i class="material-icons left">file_download</i>CSV
                                        </button>
                                    </form>
                                </div>
                            </div>
                            
                            <p class="mt-0 mb-0 font-weight-600">{{ $node->name }}</p>
                            <p class="no-margin grey-text lighten-3"  id='lastupdate_{{$node->id}}'>
                                last update: --
                            </p>
                            <h5 class="grey-text lighten-1"  id='lastValuePrime_{{$node->id}}'>
                                --
                            </h5>
                                <h6 class="grey-text lighten-3"  id='lastValueSec_{{$node->id}}'>
                                </h6>
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
                            <canvas id="simpleLineChart-{{ $node->id }}" class="center chartjs-render-monitor"
                                style="display: block; height: 272px; width: 422px; margin-top: 60px;" width="474"
                                height="300"></canvas>
                        </div>
                    </div>
                    <div class="float-right hide-on-med-and-up">
                    <form method="Get" action="{{ action('Api\NodeDataApiController@csvdata', ['node' => $node->id]) }}">
                        <input type="hidden" name="startDate" value="{{$time['startDate'].' '.$time['startTime']}}"> 
                        <input type="hidden" name="endDate" value="{{$time['endDate'].' '.$time['endTime']}}"> 
                        <button class="btn blue lighten-2 waves-effect waves-light col s12 z-depth-2" type="submit" name="action">
                            <i class="material-icons left">file_download</i>CSV
                        </button>
                    </form>
                </div>
                </div>

               
            </div>

            <div class="col s12 card card-default hoverable">
                <div class="card-content">
                    <span class="card-title">
                        <div class="col c10">
                            {{ $node->name }}-Node Settings
                        </div>
                    </span>
                    <form method="POST" action="{{ action('Web\NodeController@update', ['node' => $node->id]) }}">
                        @csrf
                        @method('PATCH')
                        @can('update', $node)
                            <button class="btn-floating waves-effect waves-light lightrn-1 right" type="submit">
                                <i class="material-icons">save</i>
                            </button>
                        @endcan
                        <div id="inline-form" class="">
                            <div class="row">
                                <div class="input-field col s12">
                                    <input id="InputTitle" name="name" type="text" class="validate" value="{{ $node->name }}" 
                                    @cannot('update', $node)
                                      disabled 
                                    @endcannot  >
                                    <label for="InputTitle">Name</label>
                                </div>
                                <div class="input-field col s12">
                                    <input id="InputDevEui" name="dev_eui" type="text" class="validate"
                                        value="{{ $node->dev_eui }}" 
                                        @cannot('update', $node)
                                            disabled 
                                        @endcannot >
                                    <label for="InputDevEui">DevEui</label>
                                </div>
                                <div class="input-field col s12">
                                    <select name="nodetype" 
                                    @cannot('update', $node)
                                        disabled 
                                    @endcannot >
                                        <option value="" disabled selected>Choose node type</option>
                                        <option value="1" {{ $node->node_type_id === 1 ? 'selected="selected"' : '' }}>Decentlab
                                        </option>
                                        <option value="2" {{ $node->node_type_id === 2 ? 'selected="selected"' : '' }}>Cayenne
                                        </option>
                                        <option value="3" {{ $node->node_type_id === 3 ? 'selected="selected"' : '' }}>Dragino
                                        </option>
                                        <option value="4" {{ $node->node_type_id === 4 ? 'selected="selected"' : '' }}>Zane
                                        </option>
                                    </select>
                                    <label>Node Type Select</label>
                                </div>
                                <div class="col s12 ml-0">
                                    <p>
                                        <label>
                                            <input class="filled-in" name="show_forecast" type="checkbox" @if ($node->show_forecast)
                                            checked
                                            @endif />
                                            <span>Show Forecast</span>
                                        </label>
                                    </p>
                                </div>
                                @if ($node->preset()->first() !== null)
                                <div class="col s12">
                                    <h4 class="card-title">Active Preset-Connection:</h4>
                                  </div>
                                  <div class="col s12">
                                    <p>This Node is connected with a preset. Change fields in preset, will change fields in this node.</p>
                                    @can('update', $node)
                                        <p> Delete preset and node will be independent - forever</p>
                                    @endcan
                                    <div class="section">
                                        @can('update', $node)
                                        <button onclick="confirmDelete('{{ action('Web\NodeController@deletepreset', ['node' => $node->id]) }}')" type="button" class="btn btn-floating btn-small waves-effect waves-light red lighten-3">
                                            <i class="material-icons">clear</i>
                                        </button>
                                        @endcan
                                      <div class="chip cyan white-text">
                                        {{ $node->preset()->first()->name}}
                                      </div>
                                    </div>
                                </div>   
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            @can('update', $node)
            <div class="col s12 card card-default hoverable">
                <div class="card-content ">
    
                        <h4 class="card-title">{{ $node->name }} Field Settings</h4>
                  
               
                <ul class="collapsible todo-collection">
                    @foreach ($node->fields->sortBy('created_at') as $field)
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
                            
                            <form method="POST" action="{{ action('Web\FieldController@storeNode', ['node' => $node->id]) }}">
                                @csrf
                                <input type="hidden" value="{{$node->id}}" name="node_id">
                                <div id="inline-form" class="">
                                    <div class="card-content">
                                        <h4 class="card-title">Add Field to {{ $node->name }} Node</h4>
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
            </div>
            @endcan
    </div>
@endsection

{{-- vendor scripts --}}
@section('vendor-script')
    @stack('config-vendor-scripts')
    <script src="{{ asset('fonts/fontawesome/js/all.js') }}"></script>
    <script src="{{ asset('vendors/chartist-js/chartist.js') }}"></script>
    <script src="{{ asset('vendors/chartist-js/chartist-plugin-tooltip.js') }}"></script>
    <script src="{{ asset('vendors/chartist-js/chartist-plugin-fill-donut.min.js') }}"></script>
@endsection

{{-- page scripts --}}
@section('page-script')
    @stack('config-scripts')
    <script src="{{ asset('vendors/moment/moment.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.js"></script>
    <script type="module" src="{{ asset('js/scripts/charts.js') }}"></script>
    <script type="module" src="{{ asset('js/scripts/chartjs-plugin-annotation.js') }}"></script>
    <script>  
      $('.timepicker').timepicker({
      formatSubmit: 'hh:mm',
      format: 'hh:i',
      twelveHour: false,
    })  
   </script>
@endsection
