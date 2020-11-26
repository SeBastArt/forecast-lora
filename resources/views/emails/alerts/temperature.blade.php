@component('mail::message')
# Introduction

{{ $alert->text}}
The body of your message.

@component('mail::button', ['url' => action('Web\NodeController@show', ['node' => $alert->nodeId])])
Go To Node
@endcomponent

Thanks,<br>
{{ config('app.name') }}

@endcomponent
