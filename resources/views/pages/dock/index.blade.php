{{-- extend layout --}}
@extends('layouts.contentLayoutMaster')

{{-- page title --}}
@section('title','Dock Input')

{{-- page content --}}
@section('content')
<div class="section">
    @include('panels.alert')
    <div class="row">
        <div class="col s12">
            <div id="input-fields" class="card card-tabs">
                <div class="card-content">
                    <div class="card-title">
                        <div class="row">
                            <div class="col s12 m6 l10">
                                <h4 class="card-title">Admin Test Incoming Data</h4>
                            </div>
                        </div>
                    </div>
                    <div id="view-input-fields" class="active">
                        <div class="row">
                            <div class="col s12">
                                <p>This Page allows user with admin permissions, to test the TTN Incoming data.</p>
                                <p>Please keep in mind: You will put data to the database.</p>
                                <br>
                                <form action="{{action('Web\DockController@index')}}" method="POST" class="row">
                                    @csrf
                                    <div class="col s12">
                                        <div class="input-field col s12">
                                            <textarea id="textarea2" name="json" class="materialize-textarea"></textarea>
                                            <label for="textarea2">Json Input</label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="input-field col s12">
                                          <button class="btn cyan waves-effect waves-light right" type="submit" name="action">Submit
                                            <i class="material-icons right">send</i>
                                          </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>    
</div>
@endsection

{{-- vendor scripts --}}
@section('vendor-script')
@endsection

{{-- page scripts  --}}
@section('page-script')
@endsection