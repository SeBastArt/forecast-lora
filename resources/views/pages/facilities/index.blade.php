{{-- layout --}}
@extends('layouts.contentLayoutMaster')

{{-- page title --}}
@section('title', ' Facilities of '.$company->name)

    {{-- vendors styles --}}
@section('vendor-style')
    <link rel="stylesheet" type="text/css" href="{{ asset('vendors/data-tables/css/jquery.dataTables.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('vendors/data-tables/extensions/responsive/css/responsive.dataTables.min.css') }}">
@endsection

{{-- page styles --}}
@section('page-style')
    <link rel="stylesheet" type="text/css" href="{{ asset('css/pages/page-facilities.css') }}">
@endsection

{{-- page content --}}
@section('content')
    @include('panels.alert')
    <!-- facilities list start -->
    <section class="facilities-list-wrapper section">
        @include('panels.search')
        <div class="col s12 m12 l12">
            <div class="facilities-list-table">
                <div class="card">
                    <div class="card-content">
                        <!-- datatable start -->
                        <div class="responsive-table">
                            <table id="facilities-list-datatable" class="highlight table responsive">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Id</th>
                                        <th>Name</th>
                                        <th>Location</th>
                                        <th>Nodes</th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($facilities as $facility)
                                        <tr>
                                            <td></td>
                                            <td>{{ $facility->id }}</td>
                                            <td><a href="{{ action('Web\NodeController@index', ['facility' => $facility->id]) }}">{{ $facility->name }}</a></td>
                                            <td>{{ $facility->location }}</td>
                                            <td>{{ $facility->nodes->count() }}</td>
                                            <td>
                                                @can('update', $facility)
                                                    <a class="" href="{{ action('Web\FacilityController@edit', ['facility' => $facility->id]) }}"><i class="material-icons">edit</i></a>
                                                @endcan
                                            </td>
                                            <td>
                                                @can('delete', $facility)
                                                    <a href="#"  onclick="confirmDelete('{{ action('Web\FacilityController@destroy', ['facility' => $facility->id]) }}')"><i class="material-icons">delete</i></a>
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
        @can('create', App\Models\Facility::class)
        <div class="col s12 m12 l12">
            <form method="POST" action="{{ action('Web\FacilityController@store', ['company' => $company->id]) }}">
                @csrf
                <div id="inline-form" class="card card-default hoverable ">
                    <div class="card-content">
                        <h4 class="card-title">Create a new Facility</h4>
                        <div class="row">
                            <div class="input-field col s12 m4">
                                <i class="material-icons prefix">create</i>
                                <input id="InputTitle" name="name" type="text" class="validate">
                                <label for="InputTitle">Name</label>
                            </div>
                            <div class="input-field col s12 m4">
                                <i class="material-icons prefix">location_city</i>
                                <input id="InputDevEui" name="location" type="text" class="validate">
                                <label for="InputDevEui">Location</label>
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
    <!-- facilities list ends -->
@endsection

{{-- vendor scripts --}}
@section('vendor-script')
    <script src="{{ asset('vendors/data-tables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendors/data-tables/extensions/responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('vendors/sweetalert/sweetalert.min.js') }}"></script>
@endsection

{{-- page script --}}
@section('page-script')
    <script src="{{ asset('js/scripts/page-facilities.js') }}"></script>
    <script src="{{ asset('js/scripts/ajax-delete.js') }}"></script>
@endsection
