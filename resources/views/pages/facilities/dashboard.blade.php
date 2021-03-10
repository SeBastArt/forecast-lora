{{-- extend layout --}}
@extends('layouts.contentLayoutMaster')

{{-- page title --}}
@section('title', 'Facility Dashboard')

    {{-- vendor styles --}}
@section('vendor-style')
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/3.6.95/css/materialdesignicons.css"
        crossorigin="anonymous">
@endsection

{{-- page content --}}
@section('content')
    @include('panels.alert')
    <div class="section">
        @if ($facility->file != null)
        <div class="col s12">
            <a class="btn waves-effect waves-light" 
                href="{{ route('nodes.fileDownload', ['facility' => $facility->id]) }}" 
                type="submit">
                <i class="material-icons left">file_download</i>LocationMap
            </a> 
        </div>  
        @endif 
        <div id="card-stats" class="row">
            @if (!isset($nodes))
                there are no nodes to display
            @else
            @foreach ($nodes as $node)
                <div class="col s12 m6 l3">
                    <div class="card animate fadeRight">

                        @switch($node['userNode']->getErrorLevel())
                        @case('1')
                        <div class="card-content yellow darken-1 white-text">
                            @break
                        @case('2')
                            <div class="card-content deep-orange accent-2 white-text">
                            @break
                        @default
                            <div class="card-content green lighten-1 white-text">
                        @endswitch
                            <div style="display: flex; justify-content:space-between; height: 1.2rem;">
                                <div>
                                    @if (isset($node['mainWeatherIcon']))
                                        <h2 class="green-text text-lighten-5 mdi {{ $node['mainWeatherIcon'] }}"
                                            style="position: absolute; left: 10px; top: -25px"></h2>
                                    @endif
                                </div>
                                <div>
                                    <div id='max_{{$node['userNode']->id}}'>max: @if (isset($node['cityForecast'])) {{$node['meta']['max'].$node['meta']['unit']}} @endif</div>
                                    <div id='min_{{$node['userNode']->id}}'>min: @if (isset($node['cityForecast'])) {{$node['meta']['min'].$node['meta']['unit']}} @endif</div>
                                   
                                </div>
                            </div>
                                <h5 class="card-stats-number white-text" id='lastvalue_{{$node['userNode']->id}}'>
                                    @if (isset($node['cityForecast'])) 
                                        {{$node['meta']['now'].$node['meta']['unit']}}
                                    @else
                                        no data
                                    @endif
                                </h5>  
                            <p class="card-stats-title"><i class="material-icons">settings_input_antenna</i>
                                <a class="white-text" href="{{ action('Web\NodeController@show', ['node' => $node['userNode']->id]) }}">{{$node['userNode']->name}}</a>
                            </p>

                            <p class="card-stats-compare">
                                <i class="material-icons">update</i> last update: 
                                <span class="green-text text-lighten-5" id='lastupdate_{{$node['userNode']->id}}'>
                                    @if (isset($node['cityForecast'])) {{$node['meta']['lastUpdate']}} @else -- @endif
                                </span>
                            </p>

                        </div>
                        @if (isset($node['cityForecast']))
                            <div class="green"
                                style="display: flex; justify-content:space-around; height: 60px; margin: 0px !important">
                                @foreach ($node['cityForecast']['forecast'] as $forecast)
                                    <div>
                                        <h5 class="green-text text-lighten-5 mdi {{ $forecast['icon'] }}"
                                            style="text-align: center; line-height: 100% !important; margin: 4px !important">
                                            </<h5>
                                            <p class="card-stats-compare"
                                                style="text-align: center; line-height: 100% !important; margin: 0px !important">
                                                {{ $forecast['day'] }}
                                            </p>
                                            <p class="card-stats-compare"
                                                style="text-align: center; line-height: 100% !important; margin: 4px !important">
                                                {{ $forecast['minTemp'] }}/{{ $forecast['maxTemp'] }}
                                            </p>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            @switch($node['userNode']->getErrorLevel())
                            @case('1')
                                <div class="card-action yellow darken-2" style="height: 60px; margin: 0px !important">
                                @break
                            @case('2')
                                <div class="card-action deep-orange accent-3" style="height: 60px; margin: 0px !important">
                                @break
                            @default
                                <div class="card-action green" style="height: 60px; margin: 0px !important">
                            @endswitch
                                <div id="minichart-{{ $node['userNode']->id }}" class="center-align"><canvas width="379"
                                        height="50"
                                        style="display: inline-block; width: 379.175px; height: 50px; vertical-align: top;"></canvas>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
            @endif
        </div>  
        
    </div>
@endsection

{{-- vendor scripts --}}
@section('vendor-script')
    <script src="{{ asset('fonts/fontawesome/js/all.js') }}"></script>
    <script type="text/javascript" src="https://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
    <script src="{{ asset('vendors/chartist-js/chartist.js') }}"></script>
    <script src="{{ asset('vendors/chartist-js/chartist-plugin-tooltip.js') }}"></script>
    <script src="{{ asset('vendors/chartist-js/chartist-plugin-fill-donut.min.js') }}"></script>
    <script src="{{ asset('vendors/sparkline/jquery.sparkline.min.js') }}"></script>
@endsection

{{-- page scripts --}}
@section('page-script')
    <script src="https://momentjs.com/downloads/moment.js"></script>
    <script type="module" src="{{ asset('js/scripts/charts.js') }}"></script>
    <script type="module" src="{{ asset('js/scripts/chartjs-plugin-annotation.js') }}"></script>
    <script src="{{ asset('js/scripts/ui-alerts.js') }}"></script>
@endsection
