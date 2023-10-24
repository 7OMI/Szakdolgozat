{{-- Fejléc --}}
<header>

  <div class="tSUM">
    <small>Összesen:</small><br>
    <b><span id="sumPriceNet">─</span> Ft</b><br>
    <small>(br. <span id="sumPriceGross">─</span> Ft)</small>
  </div>
  <div class="tSUM">
    <small>Termék:</small><br>
    <b><span id="sumProdType">─</span> féle</b><br>
    <small>(<span id="sumProdQty">─</span> db)</small>
  </div>

  <button type="button" class="s" id="copy" title="Lista másolása (cikksz., név, db, br., net.)"><i class="fa fa-copy"></i></button>
  <button type="button" class="r" id="refresh"><span>Frissítés</span> <i class="fa fa-refresh"></i></button>

</header>

<section style="overflow: auto; padding: 10px; flex: 1;">

  <form id="prod_filter" action="list.php" method="get">

      <table id="clist">
        <thead>
        <tr class="zz">
          <th class="prodListFirstCol"><span style="padding:0"><span>
            <input type="text" name="name" id="textFilter" placeholder="Termék megnevezés" value="">
            <select id="textFilterType">
              <option value="name">&#xf0f6;&nbsp; Termék megnevezés</option>
              <option value="sku">&#xf292;&nbsp; Cikkszám</option>
              <option value="distributor">&#xf0e8;&nbsp; Forgalmazó</option>
              <option value="brand">&#xf1e9;&nbsp; Márka</option>
              <option value="category">&#xf115;&nbsp; Kategória</option>
              <option value="barcode">&#xf02a;&nbsp; Vonalkód</option>
            </select>
            <u></u></span></span>&nbsp;</th>
          <th style="width:50px;"><span style="padding:0"><span>
            <input type="number" name="quantity" placeholder="Db" value="" min="0">
            <u></u></span></span>&nbsp;</th>
          <th style="width:90px;"><span style="padding:0"><span>
            <input type="number" name="price" placeholder="Nettó ár" value="" min="0">
            <u></u></span></span>&nbsp;</th>
          <th style="width:42px;"><span><span>
              <button type="button" class="s" id="filter" title="Keresés"><i class="fa fa-search"></i></button>
            </span></span>&nbsp;</th>
        </tr>
        <tr class="bg">
          <th colspan="5"><span>&nbsp;</span></th>
        </tr>
        </thead>
        <tbody id="prodlist">

        </tbody>
      </table>

  </form>

</section>

<script>



</script>


{{-- Lábléc --}}
<footer id="pagination">
</footer>
