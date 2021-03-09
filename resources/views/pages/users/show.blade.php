{{-- layout --}}
@extends('layouts.contentLayoutMaster')

{{-- page title --}}
@section('title', 'Users View')

    {{-- page style --}}
@section('page-style')
    <link rel="stylesheet" type="text/css" href="{{ asset('css/pages/page-users.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/pages/page-companies.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/pages/dashboard.css') }}">
@endsection

{{-- page content --}}
@section('content')
    @include('panels.alert')
    <!-- users view start -->
    <div class="section users-view">
        <!-- users view media object start -->
        <div class="card-panel">
            <div class="row">
                <div class="col s12 m7">
                    <div class="display-flex media">
                       
                        <div class="media-body">
                            <h6 class="media-heading">
                                <span class="users-view-name">{{ $user->name }} </span>
                                <span class="users-view-username grey-text">{{ $user->email }}</span>
                            </h6>
                            <span>ID:</span>
                            <span class="users-view-id">{{ $user->id }}</span>
                        </div>
                    </div>
                </div>
                <div class="col s12 m5 quick-action-btns display-flex justify-content-end align-items-center pt-2">
                @can('view', $user)
                    <a href="{{ action('Web\UserController@edit', ['user' => $user->id]) }}" class="btn-small indigo">Edit</a>
                @endcan
                </div>
            </div>
        </div>
        <!-- users view media object ends -->
        <!-- users view card data start -->
        <div class="card">
            <div class="card-content">
                <div class="row">
                    <div class="col s12 m4">
                        <table class="striped">
                            <tbody>
                                <tr>
                                    <td>Registered:</td>
                                    <td>{{ Carbon\Carbon::parse($user->created_at)->format('d.m.Y') }}</td>
                                </tr>
                                <tr>
                                    <td>Email:</td>
                                    <td class="users-view-email">{{ $user->email }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>Phone:</td>
                                    <td class="users-view-phone">
                                        {{ $user->phone }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>Role:</td>
                                    <td class="users-view-role">
                                        @for ($i = 0; $i < count($user->getRoles()); $i++)
                                            {{ $i < count($user->getRoles()) - 1 ? \App\Helpers\UserRole::getRoleList()[$user->getRoles()[$i]] . ',' : \App\Helpers\UserRole::getRoleList()[$user->getRoles()[$i]] }}
                                        @endfor
                                    </td>
                                </tr>
                                <tr>
                                    <td>Status:</td>
                                    <td>
                                        @switch($user->status)
                                            @case('active')
                                            <span class="chip green lighten-5">
                                                <span class="green-text">Active</span>
                                            </span>
                                            @break
                                            @case('banned')
                                            <span class="chip red lighten-5">
                                                <span class="red-text">Banned</span>
                                            </span>
                                            @break
                                            @default
                                            <span class="chip orange lighten-5">
                                                <span class="orange-text">Close</span>
                                            </span>
                                            @endswitch
                                    </td>
                                </tr>
                                <tr>
                                    <td>Language:</td>
                                    <td class="users-view-language">
                                        @switch($user->language)
                                        @case(1)
                                            German
                                            @break
                                        @case(2)
                                            English
                                            @break
                                        @case(3)
                                            French
                                            @break
                                        @case(4)
                                            Portugese
                                            @break
                                        @default
                                            English
                                        @endswitch
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="col s12 m8 hide-on-small-only">
                        <h4 class="card-title">Generate Api-Token</h4>
                        <p class="mb-2">With this Tokens, you can user the API. Generated unhashed Tokens only displayed once for security reasons</p>
                    
                        <div class="col s12">   
                            <table class="responsive-table striped">
                                <thead>
                                <tr>
                                    <th data-field="id">Id</th>
                                    <th data-field="name">Name</th>
                                    <th data-field="price">Ability</th>
                                    <th data-field="delete">Delete</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tokens as $token)
                                    <tr>
                                        <td>{{$token->id}}</td>
                                        <td>{{$token->name}}</td>
                                        <td>{{$token->abilities[0]}}</td>
                                        <td>@can('update', $user)<a href="#" onclick="confirmDelete('{{ action('Web\TokenController@destroy', ['token' => $token->id, 'user' => $user->id]) }}')"><i class="material-icons">delete</i></a>@endcan</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            
                            @can('update', $user)
                            <form method="POST" action="{{ action('Web\TokenController@store', ['user' => $user->id])  }}">
                                @csrf
                                <div class="row">
                                    <div class="input-field col s12 m4">
                                        <input id="InputTokenName" name="token_name" type="text" class="validate">
                                        <label for="InputTokenName">Name</label>
                                    </div>
                                    <div class="input-field col s12 m4">
                                        <select name="token_ability">
                                            <option value="" disabled selected>Choose ability</option>
                                            <option value="write:input">write:input</option>
                                        </select>
                                        <label>Node Type Select</label>
                                    </div>
                                    <div class="input-field col s12 m4">
                                        <button class="btn waves-effect waves-light mr-1 col s12" type="submit">Add</button>
                                    </div>
                                </div>
                            </form>
                            @endcan
                        </div>
                    </div>
        
                    <div class="col s6 m8 hide-on-small-only mt-2">
                        <h4 class="card-title">Set Alert-EmailAddressses</h4>
                        <p class="mb-2">Every alert will send to all of this emailaddresses. If no address is present, main-emailaddress will be use</p>
                        <div class="col s12">   
                            <table class="responsive-table striped">
                                <thead>
                                <tr>
                                    <th data-field="id">Id</th>
                                    <th data-field="name">Email Address</th>
                                    <th data-field="delete"></th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach ($alertAddresses as $alertAddress)
                                    <tr>
                                        <td>{{$alertAddress->id}}</td>
                                        <td>{{$alertAddress->email}}</td>
                                        <td>@can('updateMeta', $user)<a href="#" onclick="confirmDelete('{{ action('Web\UserController@destroyAlertAddress', ['alertAddress' => $alertAddress->id, 'user' => $user->id]) }}')"><i class="material-icons">delete</i></a>@endcan</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="col s12 right">   
                            @can('updateMeta', $user)
                            <form method="POST" action="{{ action('Web\UserController@addAlertAddress', ['user' => $user->id])  }}">
                                @csrf
                                <div class="row">
                                    <div class="input-field col s8">
                                        <input id="InputMailLarge" name="email" type="text" class="validate">
                                        <label for="InputMailLarge">Email Address</label>
                                    </div>
                                    <div class="input-field col s4">
                                        <button class="btn waves-effect waves-light mr-1 col s12 right" type="submit">Add</button>
                                    </div>
                                </div>
                            </form>
                            @endcan
                        </div>
                    </div> 
                </div>
            </div>
        </div>
        <!-- users view card data ends -->
    </div>

    <div class="card s12 hide-on-med-and-up">
        <div class="card-content">
            <h4 class="card-title">Generate Api-Token</h4>
            <p class="mb-2">With this Tokens, you can user the API. Generated unhashed Tokens only displayed once for security reasons</p>
            <table class="responsive-table striped">
                <thead>
                <tr>
                    <th data-field="id">Id</th>
                    <th data-field="name">Name</th>
                    <th data-field="price">Ability</th>
                    <th data-field="delete">Delete</th>
                </tr>
                </thead>
                <tbody>
                    @foreach ($tokens as $token)
                    <tr>
                        <td>{{$token->id}}</td>
                        <td>{{$token->name}}</td>
                        <td>{{$token->abilities[0]}}</td>
                        <td>@can('update', $user)<a href="#" onclick="confirmDelete('{{ action('Web\TokenController@destroy', ['token' => $token->id, 'user' => $user->id]) }}')"><i class="material-icons">delete</i></a>@endcan</td>
                    </tr>
                    @endforeach
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
                
            @can('update', $user)
                <form method="POST" action="{{ action('Web\TokenController@store', ['user' => $user->id])  }}">
                    @csrf
                    <div class="card-content">
                        <div class="row">
                            <div class="input-field col s12 m4">
                                <input id="InputTitle" name="token_name" type="text" class="validate">
                                <label for="InputTitle">Name</label>
                            </div>
                            <div class="input-field col s12 m4">
                                <select name="token_ability">
                                    <option value="" disabled selected>Choose ability</option>
                                    <option value="write:input">write:input</option>
                                </select>
                                <label>Node Type Select</label>
                            </div>
                            <div class="input-field col s12 m4">
                                <button class="btn waves-effect waves-light mr-1 col s12" type="submit">Add</button>
                            </div>
                        </div>
                    </div>
                </form>
            @endcan
        </div>
    </div>

    <div class="card s12 hide-on-med-and-up">
        <div class="card-content">
            <h4 class="card-title">Set Alert-EmailAddressses</h4>
            <p class="mb-2">Every alert will send to all of this emailaddresses. If no address is present, main-emailaddress will be use</p>
            <table class="responsive-table striped">
                <thead>
                <tr>
                    <th data-field="id">Id</th>
                    <th data-field="name">Email Address</th>
                    <th data-field="delete"></th>
                </tr>
                </thead>
                <tbody>
                    @foreach ($alertAddresses as $alertAddress)
                    <tr>
                        <td>{{$alertAddress->id}}</td>
                        <td>{{$alertAddress->email}}</td>
                        <td>@can('updateMeta', $user)<a href="#" onclick="confirmDelete('{{ action('Web\UserController@destroyAlertAddress', ['alertAddress' => $alertAddress->id, 'user' => $user->id]) }}')"><i class="material-icons">delete</i></a>@endcan</td>
                    </tr>
                    @endforeach
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
                
            @can('updateMeta', $user)
                <form method="POST" action="{{ action('Web\UserController@addAlertAddress', ['user' => $user->id])  }}">
                    @csrf
                    <div class="row">
                        <div class="input-field col s8">
                            <input id="InputMailSmall" name="email" type="text" class="validate">
                            <label for="InputMailSmall">Email Address</label>
                        </div>
                        <div class="input-field col s4">
                            <button class="btn waves-effect waves-light mr-1 col s12 right" type="submit">Add</button>
                        </div>
                    </div>
                </form>
            @endcan
        </div>
    </div>

    <div class="section users-view">
        <!-- users view media object start -->
        <div class="card-panel">
            <div class="row">
                <div class="col s12 m7">
                    <div class="display-flex media">
                        <div class="media-body">
                            <h6 class="media-heading">
                                <span class="users-view-name">Companies </span>
                                <span class="users-view-username grey-text">supported by {{ $user->name }}</span>
                            </h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- users view media object ends -->
    </div>
    @include('panels.workCollection')
@endsection

{{-- vendor scripts --}}
@section('vendor-script')
    <script src="{{ asset('vendors/sweetalert/sweetalert.min.js') }}"></script>
@endsection


{{-- page script --}}
@section('page-script')
    <script src="{{ asset('js/scripts/page-users.js') }}"></script>
    <script src="{{ asset('js/scripts/page-companies.js') }}"></script>
    <script src="{{ asset('js/scripts/ajax-delete.js') }}"></script>
@endsection
