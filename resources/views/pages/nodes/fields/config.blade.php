
    <form method="POST" action="{{action('Web\FieldController@destroy', ['node' => $Node->id, 'field' => $Field->id])}}">
        @csrf
        @method('DELETE')
        <button class="btn-floating mb-1 waves-effect waves-light mr-1 red right" type="submit" name="action">
            <i class="material-icons">clear</i>
        </button>
    </form>
    <form method="POST" action="{{action('Web\FieldController@update', ['node' => $Node->id, 'field' => $Field->id])}}">
    @csrf
    @method('PATCH')
    <div class="row">
        <div class="input-field col s12">
            <i class="material-icons prefix">sort</i>
            <input id="InputTitle_{{$Field->id}}" name="name" type="text" class="validate" value="{{$Field->name}}">
            <label for="InputTitle_{{$Field->id}}">Name</label>
        </div>
        <div class="input-field col s12">
            <i class="material-icons prefix">layers</i>
            <input id="InputDevEui_{{$Field->id}}" name="unit" type="text" class="validate" value="{{$Field->unit}}">
            <label for="InputDevEui_{{$Field->id}}">Unit</label>
        </div>
        <div class="input-field col s12">
            <i class="material-icons prefix">palette</i>
            <input id="primeColor_{{$Field->id}}" name="primarycolor" type="text" class="color validate" value="{{$Field->primarycolor}}">
            <label for="primeColor_{{$Field->id}}">Primary Color</label>
        </div>
        <div class="input-field col s12">
            <i class="material-icons prefix">palette</i>
            <input id="secondColor_{{$Field->id}}" name="secondarycolor" type="text" class="color validate" value="{{$Field->secondarycolor}}">
            <label for="secondColor_{{$Field->id}}">Secondary Color</label>
        </div>
        <div class="col s12 m6">
            <p>
                <label>
                    <input class="filled-in" name="filled" type="checkbox" @if ($Field->isfilled)
                    checked
                    @endif />
                    <span>Filled</span>
                </label>
            </p>
        </div>
        <div class="col s12 m6">
            <p>
                <label>
                    <input class="filled-in" name="dashed" type="checkbox" @if ($Field->isdashed)
                    checked
                    @endif />
                    <span>Dashed</span>
                </label>
            </p>
        </div>
        <div class="col s12">
            <label for="InputTitle">Visible</label>
            <div class="switch">
                <label>
                    Off
                <input type="checkbox" name="visible" @if ($Field->visible)
                    checked
                @endif />
                <span class="lever"></span>
                    On
                </label>
            </div>
        </div>
        <div class="input-field col s12">
            <button class="btn waves-effect waves-light right" type="submit" name="action">Save
              <i class="material-icons right">send</i>
            </button>
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
       (new CP(source[i])).on('change', function(r, g, b, a) {
           this.source.value = this.color(r, g, b, a);
           $(this.source).parent().find('i').css( "color", this.color(r, g, b, a) );
       });
   }
</script>
@endprepend

