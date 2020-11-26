@if (isset($searchCollection))
<div class="{{$searchCollection['table']}}-list-filter">
        <div class="card-panel">
            <div class="row">
                <form>
                    @foreach ($searchCollection['data'] as $key => $valueArray)
                    <div class="col s12 m6 l3">
                        <label for="{{$searchCollection['table']}}-list-{{Str::lower($key)}}">{{$key}}</label>
                        <div class="input-field">
                            <select class="form-control" id="{{$searchCollection['table']}}-list-{{Str::lower($key)}}">
                                <option value="">Any</option>
                                @foreach ($valueArray as $value)
                                    <option value="{{$value}}">{{$value}}</option>   
                                @endforeach                                    
                            </select>
                        </div>
                    </div>
                    @endforeach
                    <div class="col s12 m6 l3 display-flex align-items-center show-btn">
                        <button type="submit" class="btn btn-block indigo waves-effect waves-light">Reset</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif