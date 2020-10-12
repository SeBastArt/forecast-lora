{{-- extend layout --}}
@extends('layouts.contentLayoutMaster')

{{-- page title --}}
@section('title','Nodes Page')

{{-- page content --}}
@section('content')
<div class="section">
    <div class="col s12 m12 l12">
        <div id="highlight-table" class="card card card-default scrollspy">
          <div class="card-content">
            <h4 class="card-title">Node List</h4>
            <p class="mb-2">An overview of all nodes you add to your account. You can edit the filds by clicking in the list</p>
            <div class="row">
              <div class="col s12">
              </div>
              <div class="col s12">
                <table class="highlight">
                  <thead>
                    <tr>
                      <th>Name</th>
                      <th>DevEUI</th>
                      <th>Type</th>
                      <th></th>
                  
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($Nodes as $node)
                        <tr>
                          <td>{{$node->name}}</td>
                          <td>{{$node->dev_eui}}</td>
                          <td>{{App\NodeType::find($node->node_type_id)->name}}</td>
                          <td>
                            <a class="btn-floating mb-1 btn-medium waves-effect waves-light mr-1 right" onclick="confirmDelete('{{action('Web\NodeController@destroy', ['node' => $node->id])}}')"><i class="material-icons">delete</i></a>
                            <a class="btn-floating mb-1 btn-medium waves-effect waves-light mr-1 right" href="nodes/{{$node->id}}"><i class="material-icons">search</i></a>
                          </td>     
                        </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
      @if ($errors->any())
      <div class="alert alert-danger">
          <ul>
              @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
              @endforeach
          </ul>
      </div>
      @endif
      <div class="col s12 m12 l12">
        <form method="POST" action="{{action('Web\NodeController@index')}}">
            @csrf
            <div id="inline-form" class="card card card-default scrollspy">
            <div class="card-content">
                <h4 class="card-title">Create a new Node</h4>
                <form>
                <div class="row">
                    <div class="input-field col s12 m4">
                    <i class="material-icons prefix">create</i>
                    <input id="InputTitle" name="name" type="text" class="validate">
                    <label for="InputTitle">Name</label>
                    </div>
                    <div class="input-field col s12 m4">
                    <i class="material-icons prefix">fingerprint</i>
                    <input id="InputDevEui" name="dev_eui" type="text" class="validate">
                    <label for="InputDevEui">DevEUI</label>
                    </div>
                    <div class="input-field col s12 m4">
                        <select name="nodetype">
                        <option value="" disabled selected>Choose node type</option>
                        <option value="1">Decentlab</option>
                        <option value="2">Cayenne</option>
                        <option value="3">Dragino</option>
                        <option value="4">Zane</option>
                        </select>
                        <label>Node Type Select</label>
                    </div>
                    <div class="input-field col s12">
                        <button class="btn waves-effect waves-light mr-1 col s12" type="submit">Add</button>
                    </div>
                </div>
                </form>
            </div>
            </div>
        </form>
      </div>
</div>
@endsection

{{-- vendor scripts --}}
@section('vendor-script')
<script src="{{asset('vendors/sweetalert/sweetalert.min.js')}}"></script>
@endsection

{{-- page scripts  --}}
@section('page-script')
<script src="{{asset('js/scripts/ajax-delete.js')}}"></script>
@endsection