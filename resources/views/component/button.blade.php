@php

    $icon = $data[0];
    $text = $data[1];
    $link = $data[2];
    $o    = $data[3] ?? null;

    $o = json_decode($o, true);

    // szöveg
    $text_l = '';
    $text_c = '';
    $text_r = '';
    switch ($o['type']) {
      case 'left':   $text_l = ' '.$text; break;
      case 'leftx':  $text_l = ' '.$text; break;
      case 'right':  $text_r = $text.' '; break;
      case 'rightx': $text_r = $text.' '; break;
      default:       $text_c = $text;     break;
    }

    // class
    switch ($o['type']) {
      case 'left': $class = 'l'; break; // ikon + szöveg balra igazított
      case 'leftx': $class = 'lx'; break; // ikon + szöveg balra igazított
      case 'right': $class = 'r'; break; // ikon + szöveg jobbra igazított
      case 'rightx': $class = 'rx'; break; // ikon + szöveg jobbra igazított
      case 'page':  $class = 'p'; break; // csak szöveg (oldalszám)
      case 'icon':  $class = 's'; break; // csak ikon
      default: $class = false; break;
    }

    $btnTemplate = isset($o['a']) && $o['a'] == 'true' ? '<a class="button %s" %s>%s</a>' : '<button type="button" class="%s" %s>%s</button>';

    $btnClass = ($class ?? '') . ' ' . (isset($o['class']) ? $o['class'] : '');

    $btnAttribs = '';
    $btnAttribs .= ' data-doit="'.$link.'"';							// data-doit
    $btnAttribs .= (isset($o['title']) && $o['title']!=null?' title="'.$o['title'].'"':'');	// title
    $btnAttribs .= (isset($o['form']) && $o['form']!=null?' form="'.$o['form'].'"':'');		// form
    $btnAttribs .= (isset($o['href']) && $o['href']!=null?' href="'.$o['href'].'"':'');		// href
    $btnAttribs .= (isset($o['target']) && $o['target']!=null?' target="'.$o['target'].'"':'');		// target
    $btnAttribs .= (isset($o['aleft']) && $o['aleft']!=null?' style="left:'.$o['aleft'].'px;"':'');		// target

    $btnContent = $text_r.($icon!=null?'<i class="fa fa-'.$icon.'"></i>':$text_c).$text_l;

    echo sprintf($btnTemplate, $btnClass, $btnAttribs, $btnContent);

@endphp
