{{-- extend layout --}}
@extends('layouts.contentLayoutMaster')

{{-- page title --}}
@section('title', 'Temperature Overview')

    {{-- vendor styles --}}
@section('vendor-style')
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/3.6.95/css/materialdesignicons.css"
        crossorigin="anonymous">
@endsection

{{-- page content --}}
@section('content')
    <div class="section">
        <div id="card-stats" class="row">
            @foreach ($Nodes as $Node)
                <div class="col s12 m6 l3">
                    <div class="card animate fadeRight">
                        <div class="card-content green lighten-1 white-text">
                            <div style="display: flex; justify-content:space-between; height: 1.2rem;">
                                <div>
                                    @if (isset($Node['mainWeatherIcon']))
                                        <h2 class="green-text text-lighten-5 mdi {{ $Node['mainWeatherIcon'] }}"
                                            style="position: absolute; left: 10px; top: -25px"></h2>
                                    @endif
                                </div>
                                <div>
                                    <p class=" card-stats-compare right">
                                        max:
                                        @if (isset($Node['mainField']['max']))
                                            {{ $Node['mainField']['max'] . $Node['mainField']['unit'] }}
                                        @else
                                            --
                                        @endif
                                        <br>
                                        min:
                                        @if (isset($Node['mainField']['min']))
                                            {{ $Node['mainField']['min'] . $Node['mainField']['unit'] }}
                                        @else
                                            --
                                        @endif
                                    </p>
                                </div>
                            </div>
                            @if (isset($Node['mainField']['last']))
                                @if (isset($Node['cityForecast']))
                                    {{$Node['cityForecast']['city']['name']}}
                                @endif
                                <h4 class="card-stats-number white-text">
                                    {{ number_format($Node['mainField']['last']['value'], 1) . $Node['mainField']['unit'] }}
                                </h4>
                            @else
                                <h4 class="card-stats-number white-text">no data</h4>
                            @endif
                            <p class="card-stats-title"><i class="material-icons">settings_input_antenna</i>
                                {{ $Node['userNode']->name }}
                            </p>

                            <p class="card-stats-compare">
                                <i class="material-icons">update</i> last update:
                                <span class="green-text text-lighten-5">
                                    @if (isset($Node['mainField']['last']))
                                        {{ $Node['mainField']['last']['timestamp'] }}
                                    @else
                                        --
                                    @endif
                                </span>
                            </p>

                        </div>
                        @if (isset($Node['cityForecast']))
                            <div class="green"
                                style="display: flex; justify-content:space-around; height: 60px; margin: 0px !important">
                                @foreach ($Node['cityForecast']['forecast'] as $forecast)
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
                            <div class="card-action green" style="height: 60px; margin: 0px !important">
                                <div id="minichart-{{ $Node['userNode']['id'] }}" class="center-align"><canvas width="379"
                                        height="50"
                                        style="display: inline-block; width: 379.175px; height: 50px; vertical-align: top;"></canvas>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
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
    <script src="{{ asset('js/scripts/sort.js') }}"></script>
    <script src="{{ asset('js/scripts/ui-alerts.js') }}"></script>
@endsection
