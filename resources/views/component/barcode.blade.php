<div data-id="barcode">
  <form id="form" action="{{ route('product.create') }}" method="post">
    @csrf
    <div class="barcode-bg"><div class="hole">
      <div class="input" style="height:40px;width:calc(100% - 55px);margin-top:0;"><input type="text" name="barcode" id="barcode" placeholder="Vonalkód" minlength="1" pattern=".{1,}" required></div>
      <button type="submit" class="s"><i class="fa fa-arrow-circle-right"></i></button>
    </div></div>
  </form>
</div>
