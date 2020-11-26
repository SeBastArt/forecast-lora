{{-- layout --}}
@extends('layouts.contentLayoutMaster')

{{-- page title --}}
@section('title', 'Companies List')

    {{-- vendors styles --}}
@section('vendor-style')
    <link rel="stylesheet" type="text/css" href="{{ asset('vendors/data-tables/css/jquery.dataTables.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('vendors/data-tables/extensions/responsive/css/responsive.dataTables.min.css') }}">
@endsection

{{-- page styles --}}
@section('page-style')
    <link rel="stylesheet" type="text/css" href="{{ asset('css/pages/page-companies.css') }}">
@endsection

{{-- page content --}}
@section('content')
    @include('panels.alert')
    <!-- companies list start -->
    <section class="companies-list-wrapper section" >
        @include('panels.search')
        <div class="col s12 m12 l12">
            <div class="companies-list-table">
                <div class="card card-default">
                  <div class="card-content">
                    <!-- datatable start -->
                    <div class="responsive-table">
                      <table id="companies-list-datatable" class="highlight centered table responsive">
                        <thead>
                                    <tr>
                                        <th></th>
                                        <th>Id</th>
                                        <th>Name</th>
                                        <th>City</th>
                                        <th>Country</th>
                                        <th>Facilities</th>
                                        <th>Owner</th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($companies as $company)
                                        <tr>
                                            <td></td>
                                            <td>{{ $company->id }}</td>
                                            <td><a href="{{ action('Web\FacilityController@index', ['company' => $company->id]) }}">{{ $company->name }}</a></td>
                                            <td>{{ $company->city }}</td>
                                            <td>{{ $company->country }}</td>
                                            <td>{{ $company->facilities->count() }}</td>
                                            <td>{{ $company->user->name == Auth::user()->name ? 'You' : $company->user->name }}</td>
                                            <td><a href="{{ action('Web\CompanyController@show', ['company' => $company->id]) }}"><i class="material-icons">edit</i></a></td>
                                            <td><a href="#"  onclick="confirmDelete('{{ action('Web\CompanyController@destroy', ['company' => $company->id]) }}')"><i class="material-icons">delete</i></a></td>

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
        </section>
        <div class="col s12 m12 l12">
            <form method="POST" action="{{ action('Web\CompanyController@store') }}">
                @csrf
                <div id="inline-form" class="card card-default hoverable ">
                    <div class="card-content">
                        <h4 class="card-title">Create a new Company</h4>
                        <div class="row">
                            <div class="input-field col s12 m4">
                                <i class="material-icons prefix">group</i>
                                <input id="InputName" name="name" type="text" class="validate">
                                <label for="InputName">Name</label>
                            </div>
                            <div class="input-field col s12 m4">
                                <i class="material-icons prefix">location_city</i>
                                <input id="InputCity" name="city" type="text" class="validate">
                                <label for="InputCity">City</label>
                            </div>
                            <div class="input-field col s12 m4">
                                <i class="material-icons prefix">terrain</i>
                                <input id="InputCountry" name="country" type="text" class="validate">
                                <label for="InputCountry">Country</label>
                            </div>
                            <div class="input-field col s12">
                                <button class="btn waves-effect waves-light mr-1 col s12" type="submit">Add</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    
    <!-- companies list ends -->
@endsection

{{-- vendor scripts --}}
@section('vendor-script')
    <script src="{{ asset('vendors/data-tables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendors/data-tables/extensions/responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('vendors/sweetalert/sweetalert.min.js') }}"></script>
@endsection

{{-- page script --}}
@section('page-script')
    <script src="{{ asset('js/scripts/page-companies.js') }}"></script>
    <script src="{{ asset('js/scripts/ajax-delete.js') }}"></script>
@endsection

