{{-- layout --}}
@extends('layouts.contentLayoutMaster')

{{-- page title --}}
@section('title', 'Node Presets')

    {{-- vendors styles --}}
@section('vendor-style')
    <link rel="stylesheet" type="text/css" href="{{ asset('vendors/data-tables/css/jquery.dataTables.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('vendors/data-tables/extensions/responsive/css/responsive.dataTables.min.css') }}">
@endsection

{{-- page styles --}}
@section('page-style')
    <link rel="stylesheet" type="text/css" href="{{ asset('css/pages/page-presets.css') }}">
@endsection

{{-- page content --}}
@section('content')
    @include('panels.alert')
    <!-- presets list start -->
    <section class="presets-list-wrapper section">
        <div class="col s12 m12 l12">
            <div class="presets-list-table">
                <div class="card">
                    <div class="card-content">
                        <!-- datatable start -->
                        <div class="responsive-table">
                            <table id="presets-list-datatable" class="highlight centered table responsive">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Id</th>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Owner</th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($presets as $preset)
                                        <tr>
                                            <td></td>
                                            <td>{{ $preset->id }}</td>
                                            <td>{{ $preset->name }}</td>
                                            <td>{{ $preset->description }}</td>
                                            <td><a href="{{ action('Web\UserController@show', ['user' => $preset->user->id]) }}">{{ $preset->user->id == Auth::user()->id ? 'You' :  $preset->user->name }}</a></td>
                                            <td>
                                                @can('update', $preset)
                                                    <a href="{{ action('Web\PresetController@edit', ['preset' => $preset->id]) }}"><i class="material-icons">edit</i></a>
                                                @endcan 
                                            </td>
                                            <td>
                                                @can('delete', $preset)
                                                    <a href="#"  onclick="confirmDelete('{{ action('Web\PresetController@destroy', ['preset' => $preset->id]) }}')"><i class="material-icons">delete</i></a>
                                                @endcan
                                            </td>
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
        @can('create', App\Models\Preset::class)
        <div class="col s12 m12 l12">
            <form method="POST" action="{{ action('Web\PresetController@store') }}">
                @csrf
                <div id="inline-form" class="card card-default hoverable ">
                    <div class="card-content">
                        <h4 class="card-title">Create a new Preset</h4>
                        <div class="row">
                            <div class="input-field col s12 m4">
                                <i class="material-icons prefix">create</i>
                                <input id="InputTitle" name="name" type="text" class="validate">
                                <label for="InputTitle">Name</label>
                            </div>
                            <div class="input-field col s12 m4">
                                <i class="material-icons prefix">fingerprint</i>
                                <input id="InputDevEui" name="description" type="text" class="validate">
                                <label for="InputDevEui">Description</label>
                            </div>
                            <div class="input-field col s12">
                                <button class="btn waves-effect waves-light mr-1 col s12" type="submit">Add</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        @endcan
    </section>
    <!-- presets list ends -->
@endsection

{{-- vendor scripts --}}
@section('vendor-script')
    <script src="{{ asset('vendors/data-tables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendors/data-tables/extensions/responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('vendors/sweetalert/sweetalert.min.js') }}"></script>
@endsection

{{-- page script --}}
@section('page-script')
    <script src="{{ asset('js/scripts/page-presets.js') }}"></script>
    <script src="{{ asset('js/scripts/ajax-delete.js') }}"></script>
@endsection
