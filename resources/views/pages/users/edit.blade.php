{{-- layout --}}
@extends('layouts.contentLayoutMaster')

{{-- page title --}}
@section('title','Users edit')

{{-- vendor styles --}}
@section('vendor-style')
<link rel="stylesheet" type="text/css" href="{{asset('vendors/select2/select2.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('vendors/select2/select2-materialize.css')}}">
@endsection

{{-- page style --}}
@section('page-style')
<link rel="stylesheet" type="text/css" href="{{asset('css/pages/page-users.css')}}">
@endsection

{{-- page content --}}
@section('content')
@include('panels.alert')
<!-- users edit start -->
<div class="section users-edit">
  <div class="card">
    <div class="card-content">
      <!-- <div class="card-body"> -->
      <ul class="tabs mb-2 row">
        <li class="tab">
          <a class="display-flex align-items-center active" id="account-tab" href="#account">
            <i class="material-icons mr-1">person_outline</i><span>Account</span>
          </a>
        </li>
        <li class="tab">
          <a class="display-flex align-items-center" id="information-tab" href="#information">
            <i class="material-icons mr-2">error_outline</i><span>Information</span>
          </a>
        </li>
      </ul>
      <div class="divider mb-3"></div>
      <div class="row">
        <div class="col s12" id="account">
          <!-- users edit media object start -->
          
          <!-- users edit media object ends -->
          <!-- users edit account form start -->
          <form id="accountForm" action="{{action('Web\UserController@update', ['user' => $user->id]) }}" method="POST" >
            @csrf
            @method('PATCH')
            <div class="row">
              <div class="col s12 m6">
                <div class="row">
                  <div class="col s12 input-field">
                    <input id="username" name="username" type="text" class="validate" value="{{$user->username}}"
                      data-error=".errorTxt1" @cannot('view', $user) disabled @endcannot>
                    <label for="username">Username</label>
                    <small class="errorTxt1"></small>
                  </div>
                  <div class="col s12 input-field">
                    <input id="name" name="name" type="text" class="validate" value="{{$user->name}}"
                      data-error=".errorTxt2" @cannot('view', $user) disabled @endcannot>
                    <label for="name">Name</label>
                    <small class="errorTxt2"></small>
                  </div>
                  <div class="col s12 input-field">
                    <input id="email" name="email" type="email" class="validate" value="{{$user->email}}"
                      data-error=".errorTxt3" @cannot('view', $user) disabled @endcannot>
                    <label for="email">E-mail</label>
                    <small class="errorTxt3"></small>
                  </div>
                  <div class="input-field col s12">
                    <select name="dashboard_view">
                        <option value="0">company overview</option>
                        @foreach ($facilities as $facility)
                        <option value="{{$facility['id']}}" @if($facility['selected']) selected @endif >{{$facility['name']}}</option>
                        @endforeach
                    </select>
                    <label>Dashboard view</label>
                </div>
                </div>
              </div>
              <div class="col s12 m6">
                <div class="row">
                  <div class="col s12 input-field">
                    <select name='roles[]' @cannot('update', $user) disabled @endcannot>
                      @foreach ($userRoles as $key => $userRole)
                        <option value='{{$key}}' 
                        @If($user->hasRole($key)) 
                          selected
                        @endIf
                        >{{$userRole}}</option>  
                      @endforeach          
                    </select>
                    <label>Role</label>
                  </div>
                  <div class="col s12 input-field">
                    <select name='status' @cannot('view', $user ) disabled @endcannot>
                      <option value='1' 
                      @If($user->status == 'active')
                        selected
                      @endIf>Active</option>
                      <option value='0' 
                      @If($user->status == 'close')
                        selected
                      @endIf>Close</option>
                    </select>
                    <label>Status</label>
                  </div>
                  <div class="col s12">
                    <label>Companies</label>
                    <input type="hidden" name="companies[]" value="[]">
                    <select name="companies[]" class="browser-default" id="users-language-select2" multiple="multiple" @cannot('update', $user ) disabled @endcannot>
                      @foreach ($companies as $company)
                      <option value="{{$company['id']}}" {{$company['selected'] ? 'selected' : ''}} >{{$company['name']}}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
              </div>
              <div class="col s12 display-flex justify-content-end mt-3">
                <button type="submit" class="btn indigo">
                  Save changes</button>
                  <a href="{{action('Web\UserController@update', ['user' => $user->id]) }}" class="btn btn-light">Back</a>
              </div>
            </div>
          </form>
          <!-- users edit account form ends -->
        </div>
        <div class="col s12" id="information">
          <!-- users edit Info form start -->
          <form id="infotabForm" action="{{action('Web\UserController@update', ['user' => $user->id]) }}" method="POST" >
            @csrf
            @method('PATCH')
            <input name="user_id" type="hidden" value="{{$user->id}}">
            <div class="row">
              <div class="col s12 m6">
                <div class="row">
                  <div class="col s12">
                    <h6 class="mb-4"><i class="material-icons mr-1">person_outline</i>Personal Info</h6>
                  </div>
                  <div class="col s12 input-field">
                    <select name='language' @cannot('view', $user) disabled @endcannot>
                      <option value='1' @If($user->language == 1) selected @endIf>German</option>
                      <option value='2' @If($user->language == 2) selected @endIf>English</option>
                      <option value='3' @If($user->language == 3) selected @endIf>French</option>
                      <option value='4' @If($user->language == 4) selected @endIf>Portuguese</option>
                    </select>
                    <label>Language</label>
                  </div>
                  <div class="col s12 input-field">
                    <input id="phonenumber" name='phone' type="text" class="validate" value="{{$user->phone}}" @cannot('view', $user) disabled @endcannot>
                    <label for="phonenumber">Phone</label>
                  </div>
                  <div class="col s12 input-field">
                    <input id="address" name="address" type="text" class="validate" value="{{$user->address}}" @cannot('view', $user) disabled @endcannot>
                    <label for="address">Address</label>
                    <small class="errorTxt5"></small>
                  </div>
                  <div class="col s12 input-field">
                    <input id="country" name="country" type="text" class="validate" value="{{$user->country}}" @cannot('view', $user) disabled @endcannot>
                    <label for="country">Country</label>
                    <small class="errorTxt6"></small>
                  </div>
                </div>
              </div>
              <div class="col s12 display-flex justify-content-end mt-1">
                <button type="submit" class="btn indigo" @cannot('view', $user) disabled @endcannot> Save changes</button>
                  <a href="{{action('Web\UserController@update', ['user' => $user->id]) }}" class="btn btn-light">Back</a>
              </div>
            </div>
          </form>
          <!-- users edit Info form ends -->
        </div>
      </div>
      <!-- </div> -->
    </div>
  </div>
</div>
<!-- users edit ends -->
@endsection

{{-- vendor scripts --}}
@section('vendor-script')
<script src="{{asset('vendors/select2/select2.full.min.js')}}"></script>
<script src="{{asset('vendors/jquery-validation/jquery.validate.min.js')}}"></script>
@endsection

{{-- page scripts --}}
@section('page-script')
<script src="{{asset('js/scripts/page-users.js')}}"></script>
@endsection