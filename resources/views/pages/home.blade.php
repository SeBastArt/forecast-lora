{{-- extend layout --}}
@extends('layouts.contentLayoutMaster')

{{-- page title --}}
@section('title', 'Temperature Overview')

{{-- vendor styles --}}
@section('vendor-style')
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/3.6.95/css/materialdesignicons.css" crossorigin="anonymous">
@endsection

{{-- page content --}}
@section('content')
<div class="section">

   <div id="card-stats" class="row">
      @foreach ($userNodeCollection as $userNode)
      <div class="col s12 m6 l3">
         <div class="card animate fadeRight">
            <div class="card-content green lighten-1 white-text">
               <div style="display: flex; justify-content:space-between; height: 1.2rem;">
                  <div>
                     <h2 class="green-text text-lighten-5 mdi {{$userNode['weatherIconClass']}}" style="position: absolute; left: 10px; top: -25px"></h2> 
                  </div>
                  <div>
                     <p class=" card-stats-compare right">
                        max: {{ $userNode['primaryField']['max'] . $userNode['primaryField']['unit'] }}
                        <br>
                        min: {{ $userNode['primaryField']['min'] . $userNode['primaryField']['unit'] }}
                     </p>
                  </div>
               </div>
               
               <h4 class="card-stats-number white-text">{{number_format($userNode['primaryField']['last']['value'], 1).$userNode['primaryField']['unit']}}</h4>
               <p class="card-stats-title"><i class="material-icons">settings_input_antenna</i> {{$userNode['Node']->name}}</p>
               <p class="card-stats-compare">
                  <i class="material-icons">update</i> last update:
                  <span class="green-text text-lighten-5"> {{$userNode['primaryField']['last']['timestamp']}}</span>
               </p>
            </div>
            @if ($userNode['weatherIconClass'] !== '')
               <div class="green" style="display: flex; justify-content:space-around; height: 40px; margin: 0px !important">
               @foreach ($userNode['forecasts'] as $forecast)
                  <div>
                     <h5 class="green-text text-lighten-5 mdi {{$forecast['icon']}}" style=" margin: 0px !important"></<h5> 
                        <p class="card-stats-compare"  style="text-align: center; line-height: 100% !important; margin: 0px !important">
                           {{$forecast['day']}}
                        </p>
                  </div> 
               @endforeach 
               </div>  
            @else    
               <div class="card-action green">
                  <div id="minichart-{{$userNode['Node']['id']}}" class="center-align"><canvas width="379" height="25" style="display: inline-block; width: 379.175px; height: 25px; vertical-align: top;"></canvas></div>
               </div>
            @endif
         </div>
      </div>
      @endforeach
   </div>

    <div class="row vertical-modern-dashboard">
        @foreach ($userNodeCollection as $userNode)
        <div class="col s12 m6 l6 animate fadeLeft">
           <div id="chartjs3" class="card pt-0 pb-0 animate fadeLeft">
              <div class="dashboard-revenue-wrapper padding-2 ml-2">
                 <p class="mt-2 mb-0 font-weight-600 float-right">max: {{$userNode['primaryField']['max'].$userNode['primaryField']['unit']}}<br>min: {{$userNode['primaryField']['min'].$userNode['primaryField']['unit']}}</p>
                 <p class="mt-2 mb-0 font-weight-600">{{$userNode['Node']->name}}</p>
                 <p class="no-margin grey-text lighten-3">last update: {{$userNode['primaryField']['last']['timestamp']}}</p>
                 <h5 class="grey-text lighten-1">{{$userNode['primaryField']['last']['value'].$userNode['primaryField']['unit']}}</h5>
                 <h6 class="grey-text lighten-3">{{$userNode['secondaryField']['last'].$userNode['secondaryField']['unit']}}</h6>
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
                 <canvas id="simpleLineChart-{{$userNode['Node']['id']}}" class="center chartjs-render-monitor" style="display: block; height: 272px; width: 422px;" width="474" height="300"></canvas>
              </div>
           </div>
        </div>
        @endforeach
    </div>
</div>
@endsection

{{-- vendor scripts --}}
@section('vendor-script')
<script src="{{asset('fonts/fontawesome/js/all.js')}}"></script>
<script type="text/javascript" src="https://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script src="{{asset('vendors/chartist-js/chartist.js')}}"></script>
<script src="{{asset('vendors/chartist-js/chartist-plugin-tooltip.js')}}"></script>
<script src="{{asset('vendors/chartist-js/chartist-plugin-fill-donut.min.js')}}"></script>
<script src="{{asset('vendors/sparkline/jquery.sparkline.min.js')}}"></script>
@endsection

{{-- page scripts  --}}
@section('page-script')
<script src="https://momentjs.com/downloads/moment.js"></script>
<script type="module" src="{{asset('js/scripts/charts.js')}}"></script>
<script type="module" src="{{asset('js/scripts/chartjs-plugin-annotation.js')}}"></script>
<script src="{{asset('js/scripts/sort.js')}}"></script>
@endsection
