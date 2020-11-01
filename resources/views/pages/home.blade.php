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
               <div class="green" style="display: flex; justify-content:space-around; height: 60px; margin: 0px !important">
               @foreach ($userNode['forecasts'] as $forecast)
                  <div>
                     <h5 class="green-text text-lighten-5 mdi {{$forecast['icon']}}" style="text-align: center; line-height: 100% !important; margin: 4px !important"></<h5> 
                        <p class="card-stats-compare"  style="text-align: center; line-height: 100% !important; margin: 0px !important">
                           {{$forecast['day']}}
                        </p>
                        <p class="card-stats-compare"  style="text-align: center; line-height: 100% !important; margin: 4px !important">
                           {{$forecast['minTemp']}}/{{$forecast['maxTemp']}}
                        </p>
                  </div> 
               @endforeach 
               </div>  
            @else    
               <div class="card-action green" style="height: 60px; margin: 0px !important">
                  <div id="minichart-{{$userNode['Node']['id']}}" class="center-align"><canvas width="379" height="50" style="display: inline-block; width: 379.175px; height: 50px; vertical-align: top;"></canvas></div>
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
