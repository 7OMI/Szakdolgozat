<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\Audit;
use App\Models\Tag;
use App\Models\Company;
use App\Models\Brand;
use App\Models\Category;

class ProductController extends Controller
{
    public function create()
    {
        return view('page.interface', [
            'mode' => 'empty',
            'list' => $this->getLists(),
        ]);
    }

    public function store(Request $request)
    {
        $barcode = trim($request->barcode);

        // Vonalkód létezésének ellenőrzése
        $product = Product::where('barcode', $barcode)->first();
        $barcodeIsExist = $product != null;

        // Ha még nem létezik, létrehozzuk a terméket (névtelenül, vonalkóddal)
        if (!$barcodeIsExist) {
            Product::create(['barcode' => $barcode]);
            $product = Product::where('barcode', $barcode)->first();
        }

        // Audit létrehozása
        $beforeAudit = Audit::where('product_id', $product->id)->orderBy('id', 'desc')->first();
        Audit::create([
            'product_id'     => $product->id,
            'distributor_id' => $beforeAudit->distributor_id ?? null,
            'price_gross'    => $beforeAudit->price_gross ?? 0,
            'price_net'      => $beforeAudit->price_net ?? 0,
            'user_id'        => auth()->user()->id,
        ]);

        // Ha új termék, akkor az adatlap szerkesztő form-ra irányítjuk
        if (!$barcodeIsExist) {
            return redirect()->route('product.edit', ['id' => $product->id, 'mode' => 'new']);
        }

        // Ha már létezik, akkor a tétel szerkesztő form-ra irányítjuk
        $audit = Audit::where('product_id', $product->id)->orderBy('id', 'desc')->first();
        return redirect()->route('audit.edit', ['id' => $audit->id]);

    }

    public function edit(Request $request, string $id)
    {
        $isFirstEdit = $request->mode == 'new';
        $product = Product::where('id', $id)->first();

        if ($product == null) {
            return redirect()->route('interface');
        }

        $audit = null;
        $audits = null;

        if ($isFirstEdit) {
            $audit = $product->audits->first();
        } else {
            $audits = $product->audits;
        }

        return view('page.interface', [
            'mode' => $request->mode,
            'list' => $this->getLists(),
            'data' => (object)[
                'audit' => $audit,
                'audits' => $audits,
                'product' => $product,
            ],
            'isFirstEdit' => $isFirstEdit,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'product.barcode'       => ['sometimes', 'required', 'numeric', 'max:50'],
            'product.brand'         => ['nullable', 'numeric'],
            'product.manufacturer'  => ['nullable', 'numeric'],
            'product.name'          => ['nullable', 'max:250'],
            'product.sku'           => ['nullable', 'alpha_dash:ascii'],
            'product.original_name' => ['nullable', 'max:250'],
            'product.note'          => ['nullable', 'max:500'],
            'product.categories.*'  => ['nullable', 'numeric'],
            'audit.quantity'        => ['nullable', 'numeric'],
            'audit.price_gross'     => ['nullable', 'numeric'],
            'audit.price_net'       => ['nullable', 'numeric'],
            'audit.distributor'     => ['nullable', 'numeric'],
            'audit.note'            => ['nullable', 'max:500'],
            'audit.tags.*'          => ['nullable', 'required', 'numeric'],
        ]);

        $product = Product::where('id', $id)->first();
        $isFirstEdit = !isset($product->name);

        // ha a tulajdonságok még üres
        if ($product->properties == null) {
            Product::where('id', $id)->first()->update([
                'properties' => (object)[],
            ]);
        }

        // termék adatok
        $product->update([
            'barcode' => $request->product['barcode'] ?? $product->barcode,
            'brand_id' => $request->product['brand'],
            'manufacturer_id' => $request->product['manufacturer'],
            'name' => $request->product['name'],
            'properties->sku' => $request->product['sku'],
            'properties->original_name' => $request->product['original_name'],
            'properties->note' => $request->product['note'],
        ]);

        // kategória
        if ($request->product['categories'] ?? null != null) {
            $product->categories()->sync($request->product['categories']);
        }

        // tétel
        if ($request->audit) {
            $audit = Audit::where('product_id', $id)->first();
            $audit->update([
                'quantity' => $request->audit['quantity'],
                'price_gross' => $request->audit['price_gross'],
                'price_net' => $request->audit['price_net'],
                'distributor_id' => $request->audit['distributor'],
                'properties->note' => $request->audit['note'],
            ]);

            // tags
            if ($request->audit['tags'] ?? null != null) {
                $audit->tags()->sync($request->audit['tags']);
            }
        }

        return redirect()->route('product.edit', ['id' => $product->id, 'mode' => 'edit']);
    }

    public function destroy(string $id)
    {
        $product = Product::where('id', $id)->first();
        $product->categories()->delete();
        $product->audits()->delete();
        $product->delete();

        return redirect()->route('interface');
    }
}
