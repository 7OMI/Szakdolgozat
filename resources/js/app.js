import './bootstrap';

$(function(){ // oldal betöltés után

    // Másolás gomb
    $('#copy').click(function(e) {
        copyTable();
    });

    // Frissítés gomb
    $('#refresh').click(function() {
        $.post('/api/list', $("#prod_filter").serialize(), function(data, status){
          console.log(data, status);
          refreshTable(data.products, data.pagination);
        });
    });

    // Legördülőbe katt, input focus vissza az inputba
    $('select#textFilterType').on('change', function() {
        let input = $(this).parent().find('input');
        let smallPlaceholder = $(this).parent().find('u');
        let text = $(this).children(":selected").text().substring(3);
        let value = $(this).children(":selected").attr('value');

        input.focus();
        input.attr('placeholder', text);
        input.attr('name', value);

        if (input.val().trim().length>0) {
            $(this).parent().addClass('active');
            smallPlaceholder.html(text);
        }
    });

    // ha entert ütünk, triggerelődjön a megfelelő gomb
    $('#prod_filter input[type="text"], #prod_filter input[type="number"]').keyup(function(event) {
        if (event.keyCode === 13) {
            $('button#filter').click();
        }
    });


    // Adatlap szerkesztés, CheckInfo név váltó
    $('input[name="CheckInfo[_][Name]"]').click(function(e){

        if ($(this).attr('value') == 'part') {
            $('#CheckInfo tr[data-chiName="part"]').show('fast');
            $('#CheckInfo tr[data-chiName="full"]').hide('fast');
        } else if ($(this).attr('value') == 'full') {
            $('#CheckInfo tr[data-chiName="part"]').hide('fast');
            $('#CheckInfo tr[data-chiName="full"]').show('fast');
        }

    });

    // input kli keresés felirat
    $('table#clist tr.zz input').each(function(e){
        inputSearchLabel($(this));
    });
    $('table#clist tr.zz input').on('keyup', function(e){
        inputSearchLabel($(this));
    });

    $("table#clist tr.zz th > span > span > select").on("focusin", function() {
       $(this).parent().addClass('active'); // csak ha rá katt
    });

    $("table#clist tr.zz th > span > span > select").on("focusout", function() {
        if ($(this).parent().find('input')[0].value == '') {
            $(this).parent().removeClass('active');
        }
    });



    // ----------

    $('#barcode').focus();

    // Input lock
    $('input.inputLock').click(function(e){

      let th = $(this);
      if (th.hasClass('ILi')) {
          th.parent().find('input[type!="checkbox"], select, textarea').prop("disabled", false);
          th.removeClass('ILi').addClass('ILa');
      } else if (th.hasClass('ILa')) {
          th.parent().find('input[type!="checkbox"], select, textarea').prop("disabled", true);
          th.removeClass('ILa').addClass('ILi');
      }

    });


    // --
    $('#filter').click(function() {
      $.post('/api/list', $("#prod_filter").serializeArray(), function(data, status){
        console.log(data, status);
        refreshTable(data.products, data.pagination);
      });
    });

    // Statisztika
    $.get('/api/stat', {}, function(data, status){
      console.log(data, status);
      $('#sumPriceNet'  ).html( data.price.net        .toLocaleString('hu-HU') );
      $('#sumPriceGross').html( data.price.gross      .toLocaleString('hu-HU') );
      $('#sumProdType'  ).html( data.product.type     .toLocaleString('hu-HU') );
      $('#sumProdQty'   ).html( data.product.quantity .toLocaleString('hu-HU') );
    });

    // Termékek listája
    $.post('/api/list', {}, function(data, status){
      console.log(data, status);
      refreshTable(data.products, data.pagination);
    });

    // Mobil
    $('#tabMain').click(function(e) {
        $('#tabMain').addClass('tabActive');
        $('#tabList').removeClass('tabActive');
        $('.frameMain').removeClass('frameHide');
        $('.frameList').addClass('frameHide');
    });
    $('#tabList').click(function(e) {
        $('#tabList').addClass('tabActive');
        $('#tabMain').removeClass('tabActive');
        $('.frameList').removeClass('frameHide');
        $('.frameMain').addClass('frameHide');
    });

});




function inputSearchLabel(_t) {

    var _length = _t.val().trim().length;
    var _active = _t.parent().hasClass('active');

    if (_length > 0 && _active == false) {
        _t.parent().find('u').text(_t.attr('placeholder'));
        _t.parent().addClass('active');
    } else if (_length < 1) {
        _t.parent().find('u').text('');
        _t.parent().removeClass('active');
    }
}


// táblázat másolása
function copyTable() {

  var i = 0;
  var text = [];

  $('table#clist > tbody > tr').each(function() {

      var tr = $(this);

      if (!tr.hasClass('rowSpc')) {

          if (tr.hasClass('rowTop')) { text[i] = []; }

        $(this).children('td').each(function() {

          var td = $(this);

          if (tr.hasClass('rowTop')&&td.hasClass('qty')) { // db
            text[i][2] = td.text() + "\t";
          } else if (tr.hasClass('rowTop')&&td.hasClass('prN')) { // nettó
            text[i][4] = td.text().replace(/([0-9 \u00A0]{1,})Ft/g,'$1').replace(/[ \u00A0]/g,'') + "\n";
          } else if (tr.hasClass('rowTop')&&td.attr('title')!==undefined) { // név
            text[i][1] = td.attr('title') + "\t";
          } else if (tr.hasClass('rowBtm')&&td.hasClass('prG')) { // bruttó
            text[i][3] = td.text().replace(/\(br\. ([0-9 \u00A0]{1,}) Ft\)/g,'$1').replace(/[ \u00A0]/g,'') + "\t";
          } else if (tr.hasClass('rowBtm')) { // kieginfó
            text[i][0] = td.find('span.subinfo > span[title="Cikkszám"]').text() + "\t";
          }

      });
    } else {
      i++;
    }

  });

  // Tömb szöveggé alakítása
  text = text.toString().replace(/,/g,'');

  // Vágólapra küldés
  var textArea = document.createElement("textarea");
  textArea.value = text;
  document.body.appendChild(textArea);
  textArea.select();
  document.execCommand("Copy");
  textArea.remove();

}

  /**
  * Oldalszámozás
  *  - $current_page	// Aktuális oldalszám
  *  - $maxelem 		// Elemek száma (maximum)
  *  - $limit 		// Egy oldalon megjelenő elemek száma
  **/
  function pagination_(current_page, maxelem, limit) {

    // Bejövő adatok
    let page = current_page != null ? Math.abs(current_page) : 1; // Oldalszám

    // Beállítások
    let linklimitZ = 3; // Ennyi link (oldalszám) lesz kint egyszerre

    // Kiszámolósdi magától
    let maxpage = Math.ceil(maxelem / limit); // Oldalak száma (maximum)
    //$maxpage = ;


    // Hibák elkerülése
    if (page <= 0) {
        page = 1; // Nem lehet 0, vagy negatív az oldal. Bár utóbbit már kiszűrtük.
    } else if (page >= maxpage) {
        page = maxpage; // És ne legyen nagyobb se az oldalszám, mint ami van.
    }



    //lapozó linkek
    let linklimit = ( page < Math.ceil(linklimitZ/2)+2|| page > maxpage-(Math.ceil(linklimitZ/2)+1) ? linklimitZ + 2 : linklimitZ);
        // $linklimit = ( $page < 6 || $page > $maxpage-5 ? $linklimitZ + 2 : $linklimitZ);


    // ha maxpage annyi mint a limit vagy több (de max 3-mmal, utána ok)
    let kell_vagy_nem = (maxpage >= linklimitZ && maxpage <= linklimitZ+2? false : true);  // nem-kell // kell


    let linklimit2 = Math.floor(linklimit / 2); // ennyi link legyen az aktuális előtt és után ( vagy amennyi van )
    let linkoffset = (page > linklimit2) ? (page - linklimit2)-1 : 0; // átugrott linkek száma
    let linkend = linkoffset+linklimit; // az utolsó oldalszám.

    // Ha az utolsó oldalaknál vagyunk, és nincs már linklimit2 oldal az aktuális után
    if (maxpage - linklimit2 < page) {
        linkoffset = maxpage - linklimit;
        if (linkoffset < 0) {
            linkoffset = 0;
        }
        linkend = maxpage;
    }

    //let ret = '';
    let buttonContainer = document.createElement('div');

    //előző oldal
    buttonContainer.appendChild(createPageButton(page > 1 ? (page-1) : '#', 'prev'));

    // baloldali pontok
    if (page > linklimit-linklimit2 && kell_vagy_nem) {
        buttonContainer.appendChild(createPageButton(1));
        if (!(page == (linklimit-linklimit2)+1 || maxpage==linklimitZ+3)) {
          buttonContainer.appendChild(createPageButton(null, 'dots'));
        }
    }

    //a linkek megjelenítése
    for (let i = 1+linkoffset; i <= linkend && i <= maxpage; i++) {
        buttonContainer.appendChild(createPageButton(i, null, i == page));
    }

    // jobb oldali pontok
    if (linkend < maxpage && kell_vagy_nem) {
        if (!(linkend == maxpage-1)) {
          buttonContainer.appendChild(createPageButton(null, 'dots'));
        }

        buttonContainer.appendChild(createPageButton(maxpage));
    }

    //következő oldal
    buttonContainer.appendChild(createPageButton(page < maxpage ? (page+1) : '#', 'next'));

    return buttonContainer;

  }

  function createPageButton(page, type=null, active=false) {
    let labels = {
      'prev': '<i class="fa fa-caret-left"></i></button>',
      'next': '<i class="fa fa-caret-right"></i>',
      'dots': '…'
    };
    let label = type != null ? labels[type] : page;

    let button = null;

    if (page != null) {
      button = document.createElement('button');
      button.setAttribute('type', 'button');
      button.setAttribute('class', 'p' + (active ? ' active' : ''));
      button.setAttribute('data-page', page);
      button.innerHTML = label;
      if (page != '#') {
        button.addEventListener("click", function () {
          let postData = $("#prod_filter").serializeArray();
          postData.push({name: 'page', value: $(this).attr('data-page')});
          $.post('/api/list', postData, function(data, status){
            console.log(data, status);
            refreshTable(data.products, data.pagination);
          });
        });
      }
    }

    if (page == null) {
      button = document.createElement('span');
      button.setAttribute('class', 'fbutton p');
      button.innerHTML = label;
    }

    return button;
    // buttonContainer.appendChild(eltTooltip);
  }

  function refreshTable(products, pagination) {

    let sorsz = 0;

    $('#prodlist').html('');
    $('#pagination').html('');

    $.each(products, function(index, product) {

      sorsz++;
      let rowPar = sorsz % 2 > 0 ? 'rowPar' : ''; // páros sor
      let hlProd = ''; // $_GET['hl'] == product.id ? ' hl' : ''; // kiemelt termék

      if ($.isNumeric(product.barcode)) {
        if (product.barcode.length==13) { product.barcode = product.barcode.replace(/([\d]{1})([\d]{6})([\d]{6})/gmi, '$1&nbsp;$2&nbsp;$3'); } // EAN-13
        else if (product.barcode.length==12) { product.barcode = product.barcode.replace(/([\d]{1})([\d]{5})([\d]{5})([\d]{1})/gmi, '$1&nbsp;$2&nbsp;$3&nbsp;$4'); } // UPC
      }

      let disList = { 'name': [], 'code': [] };
      $.each(product.distributors, function(i, dis) { disList.name.push(dis.name); disList.code.push(dis.code); })

      let priceNet   = product.price.net.min > 0 ? product.price.net.min : product.price.net.max;
      let priceGross = product.price.gross.min > 0 ? product.price.gross.min : product.price.gross.max;


      $('#prodlist').append(`
        <tr id="CID_${product.id}" class="rowTop ${rowPar} ${hlProd}">
          <td title="${product.name}">${product.name}</td>
          <td class="qty">${product.quantity}</td>
          <td class="prN">${ priceNet != null ? priceNet.toLocaleString('hu-HU') + ' Ft' : '─' }</td>
          <td rowspan="2" style="font-size:0;"><a class="button s" href="/product/${product.id}/edit" title="Szerkesztés"><i class="fa fa-pencil"></i></a></td>
        </tr>
        <tr class="rowBtm ${rowPar} ${hlProd}">
          <td colspan="2">
            <span class="subinfo">
              ${ disList.name.length > 0 ? `<span title="Forgalmazó: ${disList.name.join(', ')}"><i class="fa fa-sitemap"></i>${disList.code.join(', ')}</span>` : '' }
              ${ product.barcode.length > 0 ? ` <span title="Vonalkód"><i class="fa fa-barcode"></i><code>${product.barcode}</code></span>` : '' }
              ${ product.brand != null ? ` <span title="Márka: ${product.brand.name}"><i class="fa fa-yelp"></i>${product.brand.code}</span>` : '' }
              ${ product.sku != null ? ` <span title="Cikkszám"><i class="fa fa-hashtag"></i>${product.sku}</span>` : '' }
            </span>
          </td>
          <td class="prG"><small>${ priceGross != null ? '(br. ' + priceGross.toLocaleString('hu-HU') + ' Ft)' : '&nbsp;' }</small></td>
        </tr>
        <tr class="rowSpc"></tr>
      `);

    });

    // oldalszámok
    //$('#pagination').html(pagination_(pagination.current, pagination.max, pagination.limit));
    $('#pagination')
      .append(pagination_(pagination.current, pagination.max, pagination.limit))
      .append(`<span class="maxRes" title="${ pagination.max } találat"><i class="fa fa-filter"></i>${ pagination.max }</span>`)
    ;

    // götgetés a lista tetejére
    $('.frameList > section').scrollTop(0);
  }

