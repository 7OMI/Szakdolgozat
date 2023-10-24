<footer>

  <a class="button l" href="{{ route('export', ['type'=>'list']) }}" target="_blank">
    <i class="fa fa-download"></i>
    <span>Leltárfelvételi ív</span>
  </a>

  <a class="button l" href="{{ route('export', ['type'=>'missing-data']) }}" target="_blank" style="left:210px;">
    <i class="fa fa-exclamation-triangle"></i>
    <span>Hiányzók</span>
  </a>

  <a class="button r" href="{{ route('logout') }}">
    <span>Kilépés</span>
    <i class="fa fa-sign-out"></i>
  </a>

</footer>
