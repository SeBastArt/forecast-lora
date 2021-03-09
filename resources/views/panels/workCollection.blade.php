<!--work collections start-->
<div id="work-collections">
    @if(isset($companies))
    <div class="row">
        @foreach ($companies as $company)
            <div class="col s12 m6 l4">
                <ul id="issues-collection" class="collection z-depth-1 animate fadeRight ">
                    <li class="collection-item avatar">
                        @switch($company->getErrorLevel())
                        @case('1')
                        <i class="material-icons yellow darken-1 circle">error_outline</i>
                        @break
                        @case('2')
                        <i class="material-icons deep-orange accent-2 circle">close</i>
                        @break
                        @default
                        <i class="material-icons green darken-1 circle">check</i>
                        @endswitch
                        <h6 class="collection-header m-0">{{ $company->name }}</h6>
                        <p>{{ $company->city }}</p>
                    </li>
                    @foreach ($company->facilities as $facility)
                        <li class="collection-item">
                            <div class="row">
                                <div class="col s7">
                                    <p class="collections-title"><strong>#{{ $facility->id }}</strong>
                                        <a href="{{ action('Web\FacilityController@dashboard', ['facility' => $facility->id]) }}">{{ $facility->name }}</a>
                                    </p>
                                    <p class="collections-content">{{ $facility->place }}</p>
                                </div>
                                <div class="col s2">
                                @switch($facility->getErrorLevel())
                                @case('1')
                                <span class="task-cat yellow darken-1">Warning</span> 
                                    @break
                                @case('2')
                                    <span class="task-cat deep-orange accent-2">Error</span> 
                                    @break
                                @default
                                    <span class="task-cat green darken-1">Ready</span> 
                                @endswitch
                                </div>
                                <div class="col s3">
                                    <div class="progress">
                                    <div class="determinate grey tooltipped" data-position="left" data-tooltip='rssi: {{$facility->getWorstRSSI()}} / snr: 6,78' style="width: {{$facility->getWorstRSSI() + 120}}%"></div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </div>    
    @endif
</div>  
<!--work collections ends-->