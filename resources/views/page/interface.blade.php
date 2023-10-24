@extends('layout.right-sidebar')

@section('sidebar')
  @include('component.product-list')
@endsection

@section('content')

  {{-- Vonalkód --}}
  @include('component.barcode')

  <div data-id="prod_data" style="flex: 1; padding: 10px; overflow: auto;">

    {{-- Termék --}}
    @if(in_array($mode, ['new', 'add', 'edit']))
    <form id="form2" action="{{ $mode == 'add' ? route('audit.update', ['id'=>$data->audit->id]) : route('product.update', ['id'=>$data->product->id]) }}" method="post">
      @csrf

    <div class="accordion">


      {{-- Termék adatlap --}}
      @if(in_array($mode, ['new', 'edit']))
        <div class="accordion_in">
          <div class="acc_head">Termék adatlap <small>A termék általános információi</small></div>
          <div class="acc_content">

            <p>Ez egy új termék a listán! Hogy később könnyen beazonosítható legyen, töltsd ki az alább látható adatlapot, majd kattints a "Mentés" gombra. (Ha valamit nem tudsz, akkor hagyd üresen!)</p>

            <div class="input" data-label="Vonalkód" data-label2="Vonalkód" style="width:calc(20% - 10px)">
              <input type="checkbox" name="xBarcode" title="Csak akkor pipáld be, ha valóban szükséges a Vonlakódot módosítani!" class="inputLock ILi">
              <input type="text" name="product[barcode]" placeholder="Vonalkód" value="{{ $data->product->barcode }}" disabled="">
            </div>

            <div class="input" data-label="Termék megnevezés" data-label2="Termék megnevezés" style="width:calc(40% - 10px)">
              <input type="text" name="product[name]" placeholder="Termék megnevezés" value="{{ $data->product->name }}">
            </div>
            <div class="input" data-label="Eredeti termék megnevezés" data-label2="Eredeti termék megnevezés" style="width:calc(40% - 10px)">
              <input type="text" name="product[original_name]" placeholder="Eredeti termék megnevezés" value="{{ $data->product->properties->original_name ?? '' }}">
            </div>
            <div class="input" data-label="Cikkszám (gyártói)" data-label2="Cikkszám" style="width:calc(25% - 10px)">
              <input type="text" name="product[sku]" placeholder="Cikkszám (gyártói)" value="{{ $data->product->properties->sku ?? '' }}">
            </div>
            <div class="input" data-label="Márka" data-label2="Márka" style="width:calc(25% - 10px)">
              <select name="product[brand]">
                <option value="">── Márka ──</option>
                  @foreach($list->brands as $brand)
                    @php $brandSelected = $data->product->brand_id != null ? 'selected' : ''; @endphp
                    <option value="{{ $brand['id'] }}" {{ $brandSelected }}>[{{ $brand['code'] }}] {{ $brand['name'] }}</option>
                  @endforeach
              </select>
            </div>
            <div class="input" data-label="Gyártó" data-label2="Gyártó" style="width:calc(25% - 10px)">
              <select name="product[manufacturer]">
                <option value="">── Gyártó ──</option>
                  @foreach($list->manufacturers as $manufacturer) {
                    @php $manufacturerSelected = $data->product->manufacturer_id != null ? 'selected' : ''; @endphp
                    <option value="{{ $manufacturer['id'] }}" {{ $manufacturerSelected }}>[{{ $manufacturer['code'] }}] {{ $manufacturer['name'] }}</option>
                  @endforeach
              </select>
            </div>
            <div class="input" data-label="Kategória" data-label2="Kateg." style="width:calc(25% - 10px)">
              <select name="product[categories][]" id="categories" multiple="multiple">
                  @php $categoryIds = $data->product->categories->pluck('id')->toArray() ?? []; @endphp
                  @foreach($list->categories as $category)
                    @php $categorySelected = in_array($category['id'], $categoryIds) ? 'selected' : ''; @endphp
                    <option value="{{ $category['id'] }}" {{ $categorySelected }}>{{ $category['name'] }}</option>
                  @endforeach
              </select>
            </div>
            <div class="input" data-label="Megjegyzés" data-label2="Megjegyzés">
              <textarea name="product[note]" placeholder="Megjegyzés">{{ $data->product->properties->note ?? '' }}</textarea>
            </div>

          </div>
        </div>
      @endif



      {{-- Tétel információ --}}
      @if($isFirstEdit ?? null || in_array($mode, ['new', 'add']))
        <div class="accordion_in">
          <div class="acc_head">Tétel információ <small>A most felvitt tétel kiegészítő információi</small></div>
          <div class="acc_content">
            @include('component.audit-new')
          </div>
        </div>
      @endif
      @if(in_array($mode, ['edit']))
        <div class="accordion_in">
          <div class="acc_head">Tétel információk <small>Az eddig felvitt tételek kiegészítő információi</small></div>
          <div class="acc_content">
            @include('component.audit-list')
          </div>
        </div>
      @endif


    </div>


    <div style="display: flex; justify-content: space-between;">

      <div>
        @if(in_array($mode, ['edit']))
          <a class="button lx" href="{{ route('product.delete', ['id'=>$data->product->id]) }}"><i class="fa fa-trash"></i> TÖRLÉS</a>
        @endif
      </div>

      <div>
        @if(in_array($mode, ['new', 'add', 'edit']))
          <button type="submit" class="rx">MENTÉS <i class="fa fa-floppy-o"></i></button>
        @endif
     </div>

    </div>

    </form>
    @endif
    {{-- /Termék --}}

  </div>


  <script type="text/javascript">

    $(function(){

      $('#categories').SumoSelect({
        placeholder: '── Kategória ──',
        captionFormat:'{0} kiválasztva',
        captionFormatAllSelected:'{0} kiválasztva',
        locale :  ['OK', 'Mégse', 'Összes'],
      });

      $('#tags').SumoSelect({
        placeholder: '── Besorolás ──',
        captionFormat:'{0} kiválasztva',
        captionFormatAllSelected:'{0} kiválasztva',
        locale :  ['OK', 'Mégse', 'Összes'],
      });

      /* accordion  */
      var termekMod = <?php echo (isset($_GET['datamod'])&&$_GET['datamod']=='1'?'true':'false'); ?>;

      $(".accordion").accordionjs({
        activeIndex: (termekMod?1:false),     // The section open on first init.
        closeAble:   true,  // Closeable section.
        closeOther:  false,  // Close other sections.
        slideSpeed:  200    // the speed of slide animation.
      });


    });
  </script>

@endsection
