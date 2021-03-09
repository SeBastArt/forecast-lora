<!-- BEGIN: Footer-->
<footer
  class="{{$configData['mainFooterClass']}} @if($configData['isFooterFixed']=== true){{'footer-fixed'}}@else {{'footer-static'}} @endif @if($configData['isFooterDark']=== true) {{'footer-dark'}} @elseif($configData['isFooterDark']=== false) {{'footer-light'}} @else {{$configData['mainFooterColor']}} @endif">
  <div class="footer-copyright">
    <div class="container">
      <span>&copy; 2021 <a href="{{ config('app.owner_link') }}"
          target="_blank">{{ config('app.owner') }}</a> All rights reserved.
      </span>
      <span class="right hide-on-small-only">
        Design and Developed by <a href="www.sebastianschueler.de/">Sebastian Schüler</a>
      </span>
    </div>
  </div>
</footer>

<!-- END: Footer-->