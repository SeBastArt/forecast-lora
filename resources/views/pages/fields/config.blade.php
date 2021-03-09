
<form method="POST" action="{{action('Web\FieldController@update', ['field' => $field->id])}}">
    @csrf
    @method('PATCH')
    <div class="row mb-3 right">
        <label for="InputTitle"><h6>Visible</h4></label>
        <div class="switch">
            <label>
                Off
            <input type="checkbox" name="visible" @if ($field->visible)
                checked
            @endif />
            <span class="lever"></span>
                On
            </label>
        </div>        
    </div>
    
    <div class="row">
        <div class="input-field col s12 m8">
            <i class="material-icons prefix">title</i>
            <input id="InputTitle_{{$field->id}}" name="name" type="text" class="validate" value="{{$field->name}}">
            <label for="InputTitle_{{$field->id}}">Name</label>
        </div>
        <div class="input-field col s12 m8">
            <i class="material-icons prefix">timeline</i>
            <input id="InputDevEui_{{$field->id}}" name="unit" type="text" class="validate" value="{{$field->unit}}">
            <label for="InputDevEui_{{$field->id}}">Unit</label>
        </div>
    </div>
    
    <div class="row col s12 mt-1">
        <h4 class="card-title">Appereance</h4>
        <div class="input-field col s12 m4">
            <i class="material-icons prefix">lens</i>
            <input id="primeColor_{{$field->id}}" name="primary_color" type="text" class="color validate" value="{{$field->primary_color}}">
            <label for="primeColor_{{$field->id}}">Primary Color</label>
        </div>
        <div class="input-field col s12 m4">
            <i class="material-icons prefix">lens</i>
            <input id="secondColor_{{$field->id}}" name="secondary_color" type="text" class="color validate" value="{{$field->secondary_color}}">
            <label for="secondColor_{{$field->id}}">Secondary Color</label>
        </div>
    </div>
   
    <div class="row">
        <div class="col s12 mb-1 mt-1 ml-2">
            <p>
                <label>
                    <input class="filled-in" name="filled" type="checkbox" @if ($field->is_filled)
                    checked
                    @endif />
                    <span>Filled</span>
                </label>
            </p>
        </div>
        <div class="col s12 ml-2">
            <p>
                <label>
                    <input class="filled-in" name="dashed" type="checkbox" @if ($field->is_dashed)
                    checked
                    @endif />
                    <span>Dashed</span>
                </label>
            </p>
        </div>
    </div>
    <div class="row col s12 mt-3">
        <h4 class="card-title">Alert Limits</h4>
        <div class="input-field col s12 m2">
            <label>
                <input class="filled-in" name="check_lower_limit" type="checkbox" @if ($field->check_lower_limit)
                checked
                @endif />
                <span>Lower Limit</span>
            </label>
            <input id="lower_limit_{{$field->id}}" name="lower_limit" type="number" step="0.1" placeholder="{{$field->lower_limit}}" value="{{$field->lower_limit}}">
            <label for="lower_limit_{{$field->id}}"></label>
        </div>
        <div class="input-field col s12 m2">
            <label>
                <input class="filled-in" name="check_upper_limit" type="checkbox" @if ($field->check_upper_limit)
                checked
                @endif />
                <span>Upper Limit</span>
            </label>
            <input id="upper_limit_{{$field->id}}" name="upper_limit" type="number" step="0.1" placeholder="{{$field->lower_limit}}" value="{{$field->upper_limit}}">
            <label for="upper_limit_{{$field->id}}"></label>
        </div>    
    </div>
    <div class="row ">
        <div class="input-field col s12 mt-2">
            <button class="btn waves-effect waves-light border-round" type="submit" name="action">Save
              <i class="material-icons right">save</i>
            </button>
            <a href="#" class="red-text right" onclick="event.preventDefault(); confirmDelete('{{ action('Web\FieldController@destroy', ['field' => $field->id]) }}')">
                delete
            </a>
          </div>
     
    </div>
</form>

@prepend('config-vendor-scripts')
<link href="{{asset('vendors/color-picker/color-picker.min.css')}}" rel="stylesheet">
<script src="{{asset('vendors/color-picker/color-picker.min.js')}}"></script>
<script src="{{asset('vendors/sweetalert/sweetalert.min.js')}}"></script>
@endprepend

@prepend('config-scripts')
<script src="{{asset('js/scripts/ajax-delete.js')}}"></script>
<script>
   var source = document.querySelectorAll('.color');
 
   // Set hooks for each source element
   for (var i = 0, j = source.length; i < j; ++i) {
       (CP(source[i])).on('change', function(r, g, b, a) {
           this.source.value = this.color(r, g, b, a);
           $(this.source).parent().find('i').css( "color", this.color(r, g, b, a) );
       });
   }
</script>
@endprepend

