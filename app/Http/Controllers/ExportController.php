<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use PDF;
use Elibyy\TCPDF\TCPDF;
use App\Models\Audit;
use Illuminate\Support\Facades\DB;

class ExportController extends Controller
{
    private $list;
    private $exportType; // [list|missing-data]
    private $pdf;

    private $pageSum;

    public function __construct()
    {
        $this->list = new Audit;
    }

    public function index($type)
    {
        $this->exportType = $type;

        $this->getProductList();
        $this->exportList();
    }

    private function getProductList()
    {
        $this->list = $this->list
            ->with(['product', 'product.brand', 'product.categories', 'distributor'])
            ->leftJoin('products', 'product_id', '=', 'products.id')
            ->groupBy('product_id', 'distributor_id', 'price_net')
            ->select(DB::raw(implode(', ', [
                'product_id',
                'distributor_id',
                'CAST(SUM(quantity) AS SIGNED) as quantity',
                'price_net',
                'CAST(SUM(price_net * quantity) AS SIGNED) as sum',
            ])))
        ;

        if ($this->exportType == 'missing-data') {
            $this->list = $this->list
                ->where('quantity', 0)
                ->orWhere('price_net', 0)
            ;
        } else {
            $this->list = $this->list
                ->where('price_net', '>', 0)
                ->where('quantity', '>', 0)
                ->whereRaw('LENGTH(products.name) > 0')
            ;
        }

        $this->list = $this->list
            ->orderBy('products.name')
            ->get()
            ->values()
        ;
    }

    private function exportList()
    {
        $this->pdfSettings();
        $this->pdfPageProducts();

        if ($this->exportType == 'list') {
            $this->pdfPageSum();
        }

        $this->pdfOutput();
    }

    private function pdfSettings()
    {
        $this->pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Egyéni fejléc
        $this->pdf->setHeaderCallback(function($pdf) {
            $logo = ''; // storage_path(sprintf('app/pdf/logo/%s', config('pdf.logo')));
            $pdf->SetFont('dejavusans', 'R', 7); // Set font
            $pdf->writeHTML( $this->htmlPageHeader($logo, $pdf->PageNo()) );
        });

        // Egyéni lábléc
        $this->pdf->setFooterCallback(function($pdf) {
            $pdf->SetY(-35); // Position at X mm from bottom
            $pdf->SetFont('dejavusans', 'R', 7); // Set font
            $pdf->writeHTML( $this->htmlPageFooter() );
        });

        // set document information
        $this->pdf->SetCreator(config('pdf.creator'));
        $this->pdf->SetAuthor(config('pdf.author'));
        $this->pdf->SetTitle(config('pdf.title'));
        $this->pdf->SetSubject(config('pdf.subject'));
        $this->pdf->SetKeywords(config('pdf.keywords'));

        // set default header data
        $this->pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 048', PDF_HEADER_STRING);

        // set header and footer fonts
        $this->pdf->setHeaderFont(Array('dejavusans', '', PDF_FONT_SIZE_MAIN));
        $this->pdf->setFooterFont(Array('dejavusans', '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $this->pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $this->pdf->SetMargins(14.5, 50, 14.5);
        $this->pdf->SetHeaderMargin(12.5);
        $this->pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks
        $this->pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // set image scale factor
        $this->pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__).'/lang/hun.php')) {
            require_once(dirname(__FILE__).'/lang/hun.php');
            $this->pdf->setLanguageArray($l);
        }

        $this->pdf->setViewerPreferences(array(
            'FitWindow' => true
        ));
    }

    private function pdfPageProducts()
    {
        $termek_max = $this->list->count();
        $lista_elemek_szam_egy_oldalon = 30;

        $ossz_oldalszam = ceil($termek_max / $lista_elemek_szam_egy_oldalon); //$ossz_oldalszam = 5;
        $this->pageSum = array(); // Oldal összesen ["price":"12055", "id":"2018001"]
        $sorszam = 0;

        if (true /*is_array($termek)*/) {
          for ($i=1; $i < $ossz_oldalszam+1; $i++) {

            $this->pdf->AddPage(); // add a page
            $this->pdf->SetFont('dejavusans', '', 7);
            $elem_kezd = (($i-1)*$lista_elemek_szam_egy_oldalon);
            $elem_vege = (($i)*$lista_elemek_szam_egy_oldalon)-1;
            $this->pageSum[$i] = array('price' => 0, 'pid' => $this->textPageID($this->pdf->PageNo()) );
            $tbl = $this->htmlTableHeader(); // Táblázat fejéc

            // Táblázat sorok
            for ($j=$elem_kezd; $j < $elem_vege+1; $j++) {
                if (!isset($this->list[$j])) { continue; }
                $priceNet  = $this->list[$j]->price_net > 0 ? number_format($this->list[$j]->price_net,0,',',' ').'' : '─' ;
                $sumNet    = $this->list[$j]->sum > 0 ? number_format($this->list[$j]->sum,0,',',' ') : '─' ;
                $problem   = $this->list[$j]->sum == 0 ? ' style="background-color:#ffcccc;"' : '';
                $distrCode = $this->list[$j]->distributor->code ?? false ? '÷'.$this->list[$j]->distributor->code : '';
                $brandCode = $this->list[$j]->product->brand->code ?? false ? '*'.$this->list[$j]->product->brand->code : '';
                $categCode = $this->list[$j]->product->categories->count() > 0 ? '¤'.implode(',', $this->list[$j]->product->categories->pluck('code')->toArray()) : '';
                $disVScat  = ($distrCode!=''?$distrCode:$categCode);
                $itemNoo   = $this->list[$j]->product->properties->sku ?? ''; //($this->list[$j]['ItemNo']!=''?'[#'.$this->list[$j]['ItemNo'].']':'');
                $this->pageSum[$i]['price'] += $this->list[$j]->sum;
                $tbl.= $this->htmlTableContent(
                    ++$sorszam,
                    $itemNoo != '' ? sprintf('%s [%s]', $this->list[$j]->product->name, $itemNoo) :  $this->list[$j]->product->name,
                    'db',
                    $this->list[$j]->quantity,
                    $priceNet,
                    $sumNet,
                    $disVScat.' '.$brandCode,
                    $problem
                );
            }

            $tbl.= $this->htmlTableFooter( number_format($this->pageSum[$i]['price'],0,',',' ') ); // Táblázat lábléc (összegző)
            $this->pdf->writeHTML($tbl, true, false, false, false, '');
            //$this->pdf->writeHTML('<span fill="true" color="#FF0000" style="text-align:center;font-family:dejavusans;font-size:16pt;">PISZKOZAT</span>', true, 0, true, 0);

          }
        }
    }

    private function pdfPageSum()
    {
            $lista_elemek_szam_egy_oldalon = 30;

            $ossz_oldalszam2 = ceil(count($this->pageSum) / $lista_elemek_szam_egy_oldalon);
            $pageSum2 = array(); // Oldal összesen ["price":"12055", "id":"2018001"]

            if (is_array($this->pageSum)) {
                for ($i=1; $i < $ossz_oldalszam2+1; $i++) {

                    $this->pdf->AddPage(); // add a page
                    $this->pdf->SetFont('dejavusans', '', 7);
                    $elem_kezd = (($i-1)*$lista_elemek_szam_egy_oldalon)+1;
                    $elem_vege = (($i)*$lista_elemek_szam_egy_oldalon);
                    $pageSum2[$i] = array('price' => 0, 'pid' => $this->textPageID($this->pdf->PageNo()) );
                    $tbl = $this->htmlTableHeader(); // Táblázat fejéc

                    // Táblázat sorok
                    for ($j=$elem_kezd; $j < $elem_vege+1; $j++) {
                        if (!isset($this->pageSum[$j])) { continue; }
                    $pageSum2[$i]['price'] += $this->pageSum[$j]['price'];
                    $tbl.= $this->htmlTableContent('', $this->pageSum[$j]['pid'], '', '', '', number_format($this->pageSum[$j]['price'],0,',',' '), '');
                    }

                    $tbl.= $this->htmlTableFooter( number_format($pageSum2[$i]['price'],0,',',' ') ); // Táblázat lábléc (összegző)
                    $this->pdf->writeHTML($tbl, true, false, false, false, '');
                    //$this->pdf->writeHTML('<span fill="true" color="#FF0000" style="text-align:center;font-family:dejavusans;font-size:16pt;">PISZKOZAT</span>', true, 0, true, 0);

                }
            }


            $ossz_oldalszam3 = ceil(count($pageSum2) / $lista_elemek_szam_egy_oldalon);
            $pageSum3 = array(); // Oldal összesen ["price":"12055", "id":"2018001"]

            for ($i=1; $i < $ossz_oldalszam3+1; $i++) {

                $this->pdf->AddPage(); // add a page
                $this->pdf->SetFont('dejavusans', '', 7);
                $elem_kezd = (($i-1)*$lista_elemek_szam_egy_oldalon)+1;
                $elem_vege = (($i)*$lista_elemek_szam_egy_oldalon);
                $pageSum3[$i] = array('price' => 0, 'pid' => $this->textPageID($this->pdf->PageNo()) );
                $tbl = $this->htmlTableHeader(); // Táblázat fejéc

                // Táblázat sorok
                for ($j=$elem_kezd; $j < $elem_vege+1; $j++) {
                    if (!isset($pageSum2[$j])) { continue; }
                  $pageSum3[$i]['price'] += $pageSum2[$j]['price'];
                  $tbl.= $this->htmlTableContent('', $pageSum2[$j]['pid'], '', '', '', number_format($pageSum2[$j]['price'],0,',',' '), '');
                }

                $tbl.= $this->htmlTableFooter( number_format($pageSum3[$i]['price'],0,',',' ') ); // Táblázat lábléc (összegző)
                $this->pdf->writeHTML($tbl, true, false, false, false, '');
                //$this->pdf->writeHTML('<span fill="true" color="#FF0000" style="text-align:center;font-family:dejavusans;font-size:16pt;">PISZKOZAT</span>', true, 0, true, 0);

            }

    }

    private function pdfOutput()
    {
        $exportType = (object)[ 'list' => 'vegleges', 'missing-data' => 'hianyzok' ];
        $filename = sprintf('Leltar2020_%s__[%s].pdf', $exportType->{$this->exportType}, date('Ymd-His'));
        $destination = 'I'; // I=Megjelenít, D=Letöltés

        $this->pdf->Output($filename, $destination);
    }

    private function htmlPageHeader($imagePath, $pageNo, $year=null)
    {
        $title = $this->exportType == 'missing-data' ? 'Hiányzó adatok' : 'Leltárfelvételi ív';
        $pageId = $this->textPageID($pageNo, $year);
        $company = config('pdf.company');
        $stock = (object)[
            'reporting' => (object)[
                'year'  => config('pdf.stock.reporting')->format('Y'),
                'month' => config('pdf.stock.reporting')->format('m'),
                'day'   => config('pdf.stock.reporting')->format('d'),
            ],
            'taking' => (object)[
                'year'  => config('pdf.stock.taking')->format('Y'),
                'month' => config('pdf.stock.taking')->format('m'),
                'day'   => config('pdf.stock.taking')->format('d'),
            ],
        ];
        return <<<HTML
            <table width="100%" cellspacing="0" cellpadding="2" border="0">
                <tr>
                <td align="center">
                    <table width="100%" cellspacing="0" cellpadding="0" border="0">
                    <tr>
                        <td align="center" style="width:153px;height:55px;"><img src="$imagePath" style="border:1px solid #fff;"></td>
                    </tr>
                    </table>
                </td>
                <td align="center" style="font-size:14pt;font-weight:bold;">$title</td>
                <td align="center">
                    <table width="100%" cellspacing="0" cellpadding="2" border="0">
                    <tr>
                        <td width="20%"></td>
                        <td width="40%" align="left">Sorszám: <b style="font-family:courier;font-size:12pt;">&nbsp;</b></td>
                        <td width="40%" align="right" style="font-family:courier;font-size:12pt;">$pageId</td>
                    </tr>
                    <tr>
                        <td width="20%"></td>
                        <td width="40%" align="left">Oldalszám: <b style="font-size:8pt;">&nbsp;</b></td>
                        <td width="40%" align="right"><b style="font-size:8pt;">$pageNo</b>. oldal</td>
                    </tr>
                    </table>
                </td>
                </tr>
            </table>
            <br><br>
            <table width="100%" cellspacing="0" cellpadding="2" border="0">
                <tr>
                <td align="center">
                    <table width="70%" cellspacing="0" cellpadding="2" border="0">
                    <tr>
                        <td align="center" style="font-size:9pt;font-weight:bold;border-bottom:0.1px solid #000;">$company</td>
                    </tr>
                    <tr>
                        <td align="center">leltározási egység megnevezése, száma</td>
                    </tr>
                    </table>
                </td>
                <td align="center">
                    <table width="100%" cellspacing="0" cellpadding="2" border="0">
                    <tr>
                        <td width="20%"></td>
                        <td width="40%" align="left">A leltár fordulónapja: <b style="font-size:8pt;">&nbsp;</b></td>
                        <td width="40%" align="right"><b style="font-size:8pt;">{$stock->reporting->year}</b>. év <b style="font-size:8pt;">{$stock->reporting->month}</b>. hó <b style="font-size:8pt;">{$stock->reporting->day}</b>. nap</td>
                    </tr>
                    <tr>
                        <td width="20%"></td>
                        <td width="40%" align="left" style="vertical-align:center;">A leltárfelvétel időpontja: <b style="font-size:8pt;">&nbsp;</b></td>
                        <td width="40%" align="right"><b style="font-size:8pt;">{$stock->taking->year}</b>. év <b style="font-size:8pt;">{$stock->taking->month}</b>. hó <b style="font-size:8pt;">{$stock->taking->day}</b>. nap</td>
                    </tr>
                    </table>
                </td>
                </tr>
            </table>
        HTML;
      }
    private function htmlPageFooter()
    {
        return <<<HTML
            <table width="100%" cellspacing="0" cellpadding="2" border="0">
                <tr>
                    <td width="3%"></td>
                    <td width="21.25%" style="border-bottom:0.1 solid #000;"></td>
                    <td width="3%"></td>
                    <td width="21.25%" style="border-bottom:0.1 solid #000;"></td>
                    <td width="3%"></td>
                    <td width="21.25%" style="border-bottom:0.1 solid #000;"></td>
                    <td width="3%"></td>
                    <td width="21.25%" style="border-bottom:0.1 solid #000;"></td>
                    <td width="3%"></td>
                </tr>
            </table>
            <table width="100%" cellspacing="0" cellpadding="2" border="0">
                <tr>
                    <td width="15%" height="50"></td>
                    <td width="22.5%" style="border-bottom:0.1 solid #000;"></td>
                    <td width="25%"></td>
                    <td width="22.5%" style="border-bottom:0.1 solid #000;"></td>
                    <td width="15%"></td>
                </tr>
                <tr>
                    <td></td>
                    <td align="center">egységvezető aláírása</td>
                    <td></td>
                    <td align="center">ellenőr aláírása</td>
                    <td></td>
                </tr>
            </table>
        HTML;
    }
    private function htmlTableHeader()
    {
        return <<<HTML
            <table width="100%" cellspacing="0" cellpadding="3" border="0.1" nobr="true">
                <tr>
                    <td width="6%"  align="center"><b style="font-size:3.5pt;"><br></b>Sorsz.</td>
                    <td width="50%" align="center">Megnevezés<br>(szám, kód)</td>
                    <td width="7%"  align="center">Menny.<br>egység</td>
                    <td width="7%"  align="center"><b style="font-size:3.5pt;"><br></b>Menny.</td>
                    <td width="10%" align="center">Nyilv.tart.<br>egységár</td>
                    <td width="10%" align="center"><b style="font-size:3.5pt;"><br></b>Érték</td>
                    <td width="10%" align="center"><b style="font-size:3.5pt;"><br></b>Megjegyzés</td>
                </tr>
        HTML;
    }
    private function htmlTableContent($sorsz, $nev, $megyes, $menny, $netto, $nettoOssz, $megj, $hiba='')
    {
        $noRow = (trim($nev)==''&&$menny==''&&$netto=='─'?true:false);
        $nev = $this->textTruncate(text: $nev, font: ['name' => 'dejavusans', 'size' => 7]);
        return '
            <tr'.($noRow?'':$hiba).'>
              <td style="height:22px;"><b style="font-size:2pt;"><br></b> '.($noRow?'':($sorsz!=''?$sorsz.'.':'')).' </td>
              <td style="height:22px;"><b style="font-size:2pt;"><br></b> '.($noRow?'':$nev).' </td>
              <td style="height:22px;" align="center"><b style="font-size:2pt;"><br></b> '.($noRow?'':$megyes).' </td>
              <td style="height:22px;" align="center"><b style="font-size:2pt;"><br></b> '.($noRow?'':$menny).' </td>
              <td style="height:22px;" align="right"><b style="font-size:2pt;"><br></b> '.($noRow?'':$netto).' </td>
              <td style="height:22px;" align="right"><b style="font-size:2pt;"><br></b> '.($noRow?'':($nettoOssz!='0'?$nettoOssz:'')).' </td>
              <td style="height:22px;"><b style="font-size:2pt;"><br></b> '.($noRow?'':$megj).'</td>
            </tr>
        ';
      }
    private function htmlTableFooter($pageSumNet)
    {
        return <<<HTML
                <tr>
                    <td style="border-left-color:#fff;border-bottom-color:#fff;"></td>
                    <td style="border-left-color:#fff;border-bottom-color:#fff;"></td>
                    <td style="border-left-color:#fff;border-bottom-color:#fff;"></td>
                    <td style="border-left-color:#fff;border-bottom-color:#fff;"></td>
                    <td style="border-left-color:#fff;border-bottom-color:#fff;"></td>
                    <td style="height:22px;" align="right"><b style="font-size:2pt;"><br></b>$pageSumNet</td>
                    <td style="border-right-color:#fff;border-bottom-color:#fff;"></td>
                </tr>
            </table>
        HTML;
    }
    private function textPageID($pageNo, $year=null)
    {
        return sprintf("%s%03d", $year ?? config('pdf.stock.taking')->format('Y'), $pageNo);
    }

    // https://hooks.wbcomdesigns.com/reference/classes/tcpdf/getstringwidth/
    private function textTruncate(?string $text, ?array $font, bool $dots = true, float $maxWidth = 88)
    {
        $maxWidth = $dots ? $maxWidth - 2.5 : $maxWidth; // Three dots ("…") width = 2.5
        $textWidth = $this->pdf->GetStringWidth($text, $font['name'] ?? '', $font['style'] ?? '', $font['size'] ?? null);

        if ($textWidth > $maxWidth) {
            return $this->textTruncate(mb_substr($text, 0, -1), $font, false, $maxWidth) . ($dots?'…':'');
        }

        return $text;
    }

}
