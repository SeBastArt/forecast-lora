{{-- layout --}}
@extends('layouts.contentLayoutMaster')

{{-- page title --}}
@section('title', ' Nodes of '.$facility->name. ' Facilitiy')

    {{-- vendors styles --}}
@section('vendor-style')
    <link rel="stylesheet" type="text/css" href="{{ asset('vendors/data-tables/css/jquery.dataTables.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('vendors/data-tables/extensions/responsive/css/responsive.dataTables.min.css') }}">
@endsection

{{-- page styles --}}
@section('page-style')
    <link rel="stylesheet" type="text/css" href="{{ asset('css/pages/page-nodes.css') }}">
@endsection

{{-- page content --}}
@section('content')
    @include('panels.alert')
    <!-- nodes list start -->
    <section class="nodes-list-wrapper section">
        @include('panels.search')
        <div class="col s12 m12 l12">
            <div class="nodes-list-table">
                <div class="card">
                    <div class="card-content">
                        <!-- datatable start -->
                        <div class="responsive-table">
                            <table id="nodes-list-datatable" class="highlight centered table responsive">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Id</th>
                                        <th>Name</th>
                                        <th>DevEUI</th>
                                        <th>Type</th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($nodes as $node)
                                        <tr>
                                            <td></td>
                                            <td>{{ $node->id }}</td>
                                            <td>{{ $node->name }}
                                            </td>
                                            <td>{{ $node->dev_eui }}</td>
                                            <td>{{ App\NodeType::find($node->node_type_id)->name }}</td>
                                            <td><a href="{{ action('Web\NodeController@show', ['node' => $node->id]) }}"><i class="material-icons">edit</i></a></td>
                                            <td><a href="#"  onclick="confirmDelete('{{ action('Web\NodeController@destroy', ['node' => $node->id]) }}')"><i class="material-icons">delete</i></a></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- datatable ends -->
                    </div>
                </div>
            </div>
        </div>
        <div class="col s12 m12 l12">
            <form method="POST" action="{{ action('Web\NodeController@store', ['facility' => $facility->id]) }}">
                @csrf
                <div id="inline-form" class="card card-default hoverable ">
                    <div class="card-content">
                        <h4 class="card-title">Create a new Node</h4>
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
                                <select name="node_type_id">
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
                    </div>
                </div>
            </form>
        </div>
    </section>
    <!-- nodes list ends -->
@endsection

{{-- vendor scripts --}}
@section('vendor-script')
    <script src="{{ asset('vendors/data-tables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendors/data-tables/extensions/responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('vendors/sweetalert/sweetalert.min.js') }}"></script>
@endsection

{{-- page script --}}
@section('page-script')
    <script src="{{ asset('js/scripts/page-nodes.js') }}"></script>
    <script src="{{ asset('js/scripts/ajax-delete.js') }}"></script>
@endsection
