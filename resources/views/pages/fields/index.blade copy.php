{{-- extend layout --}}
@extends('layouts.contentLayoutMaster')

{{-- page title --}}
@section('title', 'Node Overview')

    {{-- vendor styles --}}
@section('vendor-style')
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/3.6.95/css/materialdesignicons.css"
        crossorigin="anonymous">
@endsection

{{-- page content --}}
@section('content')
    <div class="section">
        <div class="row vertical-modern-dashboard">
            @foreach ($myNodes as $myNode)
                @if (!isset($myNode['primField']))
                    @continue;
                @endif
                <div class="col s12 m12 l6 animate fadeLeft">
                    <div id="chartjs3" class="card pt-0 pb-0 animate fadeLeft">
                        <div class="dashboard-revenue-wrapper padding-2 ml-2">
                            <p class="mt-2 mb-0 font-weight-600 float-right">max:
                                {{ $myNode['primField']['max'] . $myNode['primField']['unit'] }}<br>min:
                                {{ $myNode['primField']['min'] . $myNode['primField']['unit'] }}
                            </p>
                            <p class="mt-2 mb-0 font-weight-600">{{ $myNode['Node']->name }}</p>
                            <p class="no-margin grey-text lighten-3">last update:
                                {{ $myNode['primField']['last']['timestamp'] }}
                            </p>
                            <h5 class="grey-text lighten-1">
                                {{ $myNode['primField']['last']['value'] . $myNode['primField']['unit'] }}
                            </h5>
                            <h6 class="grey-text lighten-3">
                                @if (isset($myNode['secondaryField']))
                                    {{ $myNode['secField']['last'] . $myNode['secField']['unit'] }}
                                @endif
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
                            <canvas id="simpleLineChart-{{ $myNode['Node']['id'] }}" class="center chartjs-render-monitor"
                                style="display: block; height: 272px; width: 422px; margin-top: 60px;" width="474"
                                height="300"></canvas>
                        </div>
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
@endsection
