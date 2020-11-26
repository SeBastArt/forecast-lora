{{-- layout --}}
@extends('layouts.contentLayoutMaster')

{{-- page title --}}
@section('title','Users list')

{{-- vendors styles --}}
@section('vendor-style')
<link rel="stylesheet" type="text/css" href="{{asset('vendors/data-tables/css/jquery.dataTables.min.css')}}">
<link rel="stylesheet" type="text/css"
  href="{{asset('vendors/data-tables/extensions/responsive/css/responsive.dataTables.min.css')}}">
@endsection

{{-- page styles --}}
@section('page-style')
<link rel="stylesheet" type="text/css" href="{{asset('css/pages/page-users.css')}}">
@endsection

{{-- page content --}}
@section('content')
<!-- users list start -->
<section class="users-list-wrapper section">
  <div class="users-list-filter">
    <div class="card-panel">
      <div class="row">
        <form>
          <div class="col s12 m6 l3">
            <label for="users-list-verified">Verified</label>
            <div class="input-field">
              <select class="form-control" id="users-list-verified">
                <option value="">Any</option>
                <option value="Yes">Yes</option>
                <option value="No">No</option>
              </select>
            </div>
          </div>
          <div class="col s12 m6 l3">
            <label for="users-list-role">Role</label>
            <div class="input-field">
              <select class="form-control" id="users-list-role">
                <option value="">Any</option>
                <option value="Admin">Admin</option>
                <option value="Management">Management</option>
                <option value="Account Manager">Account Manager</option>
                <option value="Support">Support</option>
                <option value="Finance">Finance</option>
              </select>
            </div>
          </div>
          <div class="col s12 m6 l3">
            <label for="users-list-status">Status</label>
            <div class="input-field">
              <select class="form-control" id="users-list-status">
                <option value="">Any</option>
                <option value="Active">Active</option>
                <option value="Close">Close</option>
                <option value="Banned">Banned</option>
              </select>
            </div>
          </div>
          <div class="col s12 m6 l3 display-flex align-items-center show-btn">
            <button type="submit" class="btn btn-block indigo waves-effect waves-light">Show</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div class="users-list-table">
    <div class="card">
      <div class="card-content">
        <!-- datatable start -->
        <div class="responsive-table">
          <table id="users-list-datatable" class="table responsive">
            <thead>
              <tr>
                <th></th>
                <th>Id</th>
                <th>Name</th>
                <th>E-Mail</th>
                <th>Last Activity</th>
                <th>Verified</th>
                <th>Role</th>
                <th>Status</th>
                <th>Edit</th>
                <th>View</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($users as $user)
              <tr>
                <td></td>
                <td>{{$user->id}}</td>
                <td><a href="{{ action('Web\UserController@show', ['user' => $user->id]) }}">{{$user->name}}</a></td>
                <td>{{$user->email}}</td>
                <td>{{Carbon\Carbon::parse($user->updated_at)->format('d.m.Y')}}</td>
                <td>{{($user->email_verified_at != null) ? 'Yes' : 'No'}}</td>
                <td>
                  @for ($i = 0; $i < count($user->getRoles()); $i++)
                      {{($i < count($user->getRoles()) - 1) ? \App\Helpers\UserRole::getRoleList()[$user->getRoles()[$i]].',' : \App\Helpers\UserRole::getRoleList()[$user->getRoles()[$i]]}}
                  @endfor
                </td>
                <td>
                @switch($user->status)
                    @case('active')
                        <span class="chip green lighten-5"><span class="green-text">Active</span> 
                        @break
                    @case('banned')
                        <span class="chip red lighten-5"><span class="red-text">Banned</span></span>
                        @break
                    @default
                        <span class="chip orange lighten-5"><span class="orange-text">Close</span></span> 
                @endswitch
                </td>
                <td><a href="{{ action('Web\UserController@edit', ['user' => $user->id]) }}"><i class="material-icons">edit</i></a></td>
                <td><a href="{{ action('Web\UserController@show', ['user' => $user->id]) }}"><i class="material-icons">remove_red_eye</i></a></td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <!-- datatable ends -->
      </div>
    </div>
  </div>
</section>
<!-- users list ends -->
@endsection

{{-- vendor scripts --}}
@section('vendor-script')
<script src="{{asset('vendors/data-tables/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('vendors/data-tables/extensions/responsive/js/dataTables.responsive.min.js')}}"></script>
@endsection

{{-- page script --}}
@section('page-script')
<script src="{{asset('js/scripts/page-users.js')}}"></script>
@endsection