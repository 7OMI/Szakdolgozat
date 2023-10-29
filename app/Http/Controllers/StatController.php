<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatController extends Controller
{
    public function index()
    {
        return (object)[
            'price' => (object)[
                'gross' => $this->priceGross(),
                'net' => $this->priceNet(),
            ],
            'product' => (object)[
                'type' => $this->productType(),
                'quantity' => $this->productQuantity(),
            ],
        ];
    }

    private function priceGross()  {
        $sumPerProduct = Audit::groupBy('product_id')->select(DB::raw('SUM(price_gross * quantity) as sum'));
        return (int)DB::query()->fromSub($sumPerProduct, 'sumPerProduct')->sum('sum');
    }

    private function priceNet()  {
        $sumPerProduct = Audit::groupBy('product_id')->select(DB::raw('SUM(price_net * quantity) as sum'));
        return (int)DB::query()->fromSub($sumPerProduct, 'sumPerProduct')->sum('sum');
    }

    private function productType()  {
        return Product::count('id');
    }

    private function productQuantity()  {
        return (int)Audit::sum('quantity');
    }

}
