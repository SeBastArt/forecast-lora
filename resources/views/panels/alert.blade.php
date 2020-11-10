@if (Session::has('message'))
<div class="card-alert card green">
    <div class="card-content white-text">
        <p>SUCCESS : {{ Session::get('message') }}.</p>
    </div>
    <button type="button" class="close white-text" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">×</span>
    </button>
</div>
@endif

@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
            <div class="card-alert card red">
                <div class="card-content white-text">
                    <p>Error : {{ $error }}</p>
                </div>
                <button type="button" class="close white-text" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
        @endforeach
    </ul>
</div>
@endif