@component('mail::message')
    You received this mail, because you registered for Kerberos alert system.
    A limit was exceeded in one of your observed objects.

@component('mail::panel')
    The alarm was triggered at the 
    <br>
    <a style="text-decoration: none;" href="{{ 
            action('Web\NodeController@show',
            ['node' => $mailAlert->field->nodes()->first()->id]) }}">
            <b style="color: red">{{ $mailAlert->field->name}}</b>
    </a> field of node
    <a style="text-decoration: none;" href="{{ 
            action('Web\NodeController@show',
            ['node' => $mailAlert->field->nodes()->first()->id]) }}">
            <b style="color: red">{{ $mailAlert->field->nodes()->first()->name}}</b>
    </a>
    <br> in
    <a style="text-decoration: none;" href="{{ 
            action('Web\FacilityController@dashboard', 
            ['facility' => $mailAlert->field->nodes()->first()->facility->id]) }}">
            <b style="color: red">{{ $mailAlert->field->nodes()->first()->facility->name}}</b>
    </a> facility
    <br>at company 
    <a style="text-decoration: none;" href="{{ 
        action('Web\CompanyController@dashboard')}}">
        <b style="color: red">{{ $mailAlert->field->nodes()->first()->facility->company->name}}</b>
    </a>
@endcomponent
@component('mail::button', ['url' => action('Web\NodeController@show', ['node' => $mailAlert->field->nodes()->first()->id, 'timestamp' =>  \Carbon\Carbon::parse($mailAlert->alert->exceed_timestamp)->isoFormat('D.MM.YYYY HH:mm')])])
Show Alert
@endcomponent



Thanks,<br>
{{ config('app.name') }}

@endcomponent

