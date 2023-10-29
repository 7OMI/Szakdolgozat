<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ProductListController extends Controller
{
    private $products;
    private $pagination;

    public function __construct()
    {
        $this->products = new Product;
        $this->pagination = (object)[];
    }

    public function index(Request $request)
    {
        $this->validation($request);
        $this->filtering($request);
        $this->pagination($request);

        return $this->responseJson();
    }

    public function show(Request $request)
    {
        $this->validation($request);
        $this->filtering($request);
        $this->pagination($request);

        return $this->responseJson();
    }

    private function validation(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name'        => ['nullable'],
            'sku'         => ['nullable'],
            'distributor' => ['nullable'], // required
            'brand'       => ['nullable'],
            'category'    => ['nullable'],
            'barcode'     => ['nullable'],
            'quantity'    => ['nullable','integer'],
            'price'       => ['nullable','regex:/^(\*|)[0-9]+$/'],
        ]);
        if ($validation->fails()) {
            response()->json(['fail' => true, 'errors' => $validation->errors()], 422)->send(); exit;
        }
    }

    private function filtering(Request $request)
    {
        if ($request->name) {
            $this->products = $this->products->where('name', 'like', '%'.$request->name.'%');
        }
        if ($request->sku) {
            $this->products = $this->products->where('properties->sku', 'like', '%'.$request->sku.'%');
        }
        if ($request->distributor) {
            $this->products = $this->products->whereRelation('audits.distributor', 'name', 'like', '%'.$request->distributor.'%');
        }
        if ($request->brand) {
            $this->products = $this->products->whereRelation('brand', 'name', 'like', '%'.$request->brand.'%');
        }
        if ($request->category) {
            $this->products = $this->products->whereRelation('categories.category', 'name', 'like', '%'.$request->category.'%');
        }
        if ($request->barcode) {
            $this->products = $this->products->where('barcode', 'like', '%'.$request->barcode.'%');
        }
        if ($request->quantity) {
            $this->products = $this->products->where(DB::raw('(SELECT sum(`quantity`) FROM `audits` WHERE `products`.`id` = `audits`.`product_id`)'), $request->quantity);
        }
        if ($request->price) {
            $this->products = $this->products->whereRelation('audits', 'price_net', $request->price);
        }
    }

    private function pagination(Request $request) {
        $limit = 50;
        $current = abs((int)$request->page) == 0 ? 1 : abs((int)$request->page);
        $skip = ($current - 1) * $limit;
        $this->pagination = (object)[
            'max' => $this->products->count(),
            'current' => $current,
            'skip' => $skip,
            'limit' => $limit,
        ];
    }

    private function serializedProductsData()
    {
        return $this->products
        ->orderBy('name')
        ->skip($this->pagination->skip)
        ->limit($this->pagination->limit)
        ->get()->map->only([
            'id',
            'barcode',
            'sku',
            'name',
            'original_name',
            'brand',
            'manufacturer',
            'categories',
            'distributors',
            'quantity',
            'price',
        ])->map(function($product){ //dd($product);
            return array_merge($product, [
                'name'         => $product['name']         ?? sprintf('❗ Névtelen termék #%s', $product['id']),
                'brand'        => $product['brand']        ?->only(['id', 'code', 'name']),
                'manufacturer' => $product['manufacturer'] ?->only(['id', 'code', 'name']),
                'categories'   => $product['categories']   ?->map(fn($i)=>$i->category?->only(['id', 'code', 'name'])),
                'distributors' => $product['distributors'] ?->filter(fn($i)=>!is_null($i))->map(fn($i)=>$i?->only(['id', 'code', 'name'])) ?? null,
            ]);
        });
    }

    private function responseJson()
    {
        return response()->json([
            'products'   => $this->serializedProductsData(),
            'pagination' => $this->pagination,
        ])->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

}
