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
    <!-- users view start -->
    <div class="section users-view">
        <!-- users view media object start -->
        <div class="card-panel">
            <div class="row">
                <div class="col s12 m7">
                    <div class="display-flex media">
                        <a href="#" class="avatar">
                            <img src="{{ asset('images/avatar/avatar-15.png') }}" alt="users view avatar"
                                class="z-depth-4 circle" height="64" width="64">
                        </a>
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
                    <a href="{{ asset('app-email') }}" class="btn-small btn-light-indigo"><i
                            class="material-icons">mail_outline</i></a>
                    <a href="{{ asset('user-profile-page') }}" class="btn-small btn-light-indigo">Profile</a>
                    <a href="{{ action('Web\UserController@edit', ['user' => $user->id]) }}"
                        class="btn-small indigo">Edit</a>
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
                                    <td>Latest Activity:</td>
                                    <td class="users-view-latest-activity">
                                        {{ Carbon\Carbon::parse($user->updated_at)->format('d.m.Y') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>Verified:</td>
                                    <td class="users-view-verified">{{ $user->email_verified_at != null ? 'Yes' : 'No' }}
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
                                            <span class="chip green lighten-5"><span class="green-text">Active</span>
                                                @break
                                                @case('banned')
                                                <span class="chip red lighten-5"><span class="red-text">Banned</span></span>
                                                @break
                                                @default
                                                <span class="chip orange lighten-5">
                                                  <span class="orange-text">Close</span>
                                                </span>
                                            @endswitch
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col s12 m8">
                        <table class="responsive-table">
                            <thead>
                                <tr>
                                    <th>Module Permission</th>
                                    <th>Read</th>
                                    <th>Write</th>
                                    <th>Create</th>
                                    <th>Delete</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Users</td>
                                    <td>Yes</td>
                                    <td>No</td>
                                    <td>No</td>
                                    <td>Yes</td>
                                </tr>
                                <tr>
                                    <td>Articles</td>
                                    <td>No</td>
                                    <td>Yes</td>
                                    <td>No</td>
                                    <td>Yes</td>
                                </tr>
                                <tr>
                                    <td>Staff</td>
                                    <td>Yes</td>
                                    <td>Yes</td>
                                    <td>No</td>
                                    <td>No</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- users view card data ends -->
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

<!--work collections start-->
<div id="work-collections">
    <div class="row">
        @foreach ($companies as $company)
            <div class="col s12 m6 l4">
                <ul id="issues-collection" class="collection z-depth-1 animate fadeRight ">
                    <li class="collection-item avatar">
                        @switch($company->getErrorLevel())
                        @case('2')
                          <i class="material-icons yellow darken-1 circle">error_outline</i>
                          @break
                        @case('3')
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
                                        {{ $facility->name }}
                                    </p>
                                    <p class="collections-content">{{ $facility->place }}</p>
                                </div>
                                <div class="col s2">
                                  @switch($facility->getErrorLevel())
                                  @case('2')
                                  <span class="task-cat yellow darken-1">Warning</span> 
                                    @break
                                  @case('3')
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
</div>  
<!--work collections ends-->
@endsection

{{-- page script --}}
@section('page-script')
    <script src="{{ asset('js/scripts/page-users.js') }}"></script>
    <script src="{{ asset('js/scripts/page-companies.js') }}"></script>
@endsection
