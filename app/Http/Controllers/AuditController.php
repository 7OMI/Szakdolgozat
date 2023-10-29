<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Audit;

class AuditController extends Controller
{
    public function edit(string $id)
    {
        $audit = Audit::where('id', $id)->first();

        return view('page.interface', [
            'mode' => 'add',
            'list' => $this->getLists(),
            'data' => (object)[
                'audit' => $audit,
            ],
        ]);
    }

    public function update(Request $request, string $id)
    {
        $audit = Audit::where('id', $id)->first()->update([
            'distributor_id'   => $request->audit['distributor'],
            'quantity'         => $request->audit['quantity'],
            'price_gross'      => $request->audit['price_gross'],
            'price_net'        => $request->audit['price_net'],
            'properties->note' => $request->audit['note'],
        ]);

        if ($request->audit['tags'] ?? false) {
            Audit::where('id', $id)->first()->tags()->sync($request->audit['tags']);
        }

        // redirect
        return redirect()->route('interface');
    }

    public function destroy(string $id)
    {
        $audit = Audit::where('id', $id)->first();
        $productId = $audit->product_id;
        $audit->delete();

        return redirect()->route('product.edit', ['id' => $productId, 'mode' => 'edit']);
    }
}
