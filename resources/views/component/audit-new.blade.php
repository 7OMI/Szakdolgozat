  <p>A <b>Mennyiség</b> mezőt csak abban az esetben módosítsuk, ha az előbb bevitt vonalkóddal nem egy, hanem több darabot szeretnénk felvinni.</p>
  <p>A <b>többi mezőt</b> csak abban az esetben töltsük ki, ha a most felvitt (egy vagy több darabos) tétel különbözik az előzőleg felvitt tétel információktól. (Pl.: "régebbi, ezért még más volt az ára", "bontott termék", "ez még/már máshonnan lett beszerezve", stb.)</p>

  @php
    $price_gross = $data?->audit?->price_gross > 0 ? $data->audit->price_gross : $data->beforeAudit->price_gross ?? 0;
    $price_net   = $data?->audit?->price_net > 0 ? $data->audit->price_net : $data->beforeAudit->price_net ?? 0;
  @endphp

  <div class="input" data-label="Mennyiség" data-label2="Mennyiség" style="width:calc(14% - 10px)">
    <input type="number" name="audit[quantity]" placeholder="Darab" value="{{ $data->audit->quantity ?? '' }}" min="1" max="9999">
  </div>
  <div class="input" data-label="Bruttó ár" data-label2="Bruttó ár" style="width:calc(21% - 10px)">
    <input type="number" name="audit[price_gross]" placeholder="Bruttó ár" value="{{ $price_gross }}" min="0"> <u>Ft</u>
  </div>
  <div class="input" data-label="Nettó ár" data-label2="Nettó ár" style="width:calc(21% - 10px)">
    <input type="number" name="audit[price_net]" placeholder="Nettó ár" value="{{ $price_net }}" min="0"> <u>Ft</u>
  </div>
  <div class="input" data-label="Forgalmazó" data-label2="Forg." style="width:calc(22% - 10px)">
    <select name="audit[distributor]">
      <option value="">── Forgalmazó ──</option>
        @foreach($list->distributors as $distributor) {
          @php
            $selected = in_array($distributor['id'], [$data->audit->distributor_id??null,$data->beforeAudit->distributor_id??null]) ? 'selected' : '';
          @endphp
          <option value="{{ $distributor['id'] }}" {{ $selected }}>[{{ $distributor['code'] }}] {{ $distributor['name'] }}</option>
        @endforeach
    </select>
  </div>
  <div class="input" data-label="Besorolás" data-label2="Besor." style="width:calc(22% - 10px)">
    <select name="audit[tags][]" id="tags" multiple="multiple">
      @php $tagsIds = $data->audit->tags->pluck('id')->toArray() ?? []; @endphp
      @foreach($list->tags as $tag)
        @php $tagSelected = in_array($tag['id'], $tagsIds) ? 'selected' : ''; @endphp
        <option value="{{ $tag['id'] }}" {{ $tagSelected }}>{{ $tag['name'] }}</option>
      @endforeach
    </select>
  </div>
  <div class="input" data-label="Megjegyzés" data-label2="Megjegyzés">
    <textarea name="audit[note]" placeholder="Megjegyzés">{{ $data->audit->properties->note ?? '' }}</textarea>
  </div>
