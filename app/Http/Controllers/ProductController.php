<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        $products = Product::orderBy('name')->get();
        $nourritures = $products->where('type', Product::TYPE_NOURRITURE)->values();
        $boissons = $products->where('type', Product::TYPE_BOISSON)->values();

        return view('products.index', compact('nourritures', 'boissons'));
    }

    public function create(): View
    {
        $type = request()->get('type');
        return view('products.create', compact('type'));
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if (($data['type'] ?? null) === Product::TYPE_NOURRITURE) {
            $data['unit'] = Product::UNIT_PLAT;
        }
        if (($data['type'] ?? null) === Product::TYPE_BOISSON) {
            $data['unit'] = Product::UNIT_BOUTEILLE;
        }
        $data['low_stock_threshold'] = (int) ($data['low_stock_threshold'] ?? 0);

        $product = Product::create($data + ['is_active' => $request->boolean('is_active')]);

        $initialLocation = $data['type'] === Product::TYPE_NOURRITURE
            ? 'central_nourriture'
            : 'central_boisson';

        if (!empty($data['initial_quantity'])) {
            Stock::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'location' => $initialLocation,
                ],
                []
            )->increment('quantity', $data['initial_quantity']);
        }

        return redirect()->route('products.index')->with('status', 'Produit cree.');
    }

    public function edit(Product $product): View
    {
        return view('products.edit', compact('product'));
    }

    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        $data = $request->validated();

        if (($data['type'] ?? null) === Product::TYPE_NOURRITURE) {
            $data['unit'] = Product::UNIT_PLAT;
        }
        if (($data['type'] ?? null) === Product::TYPE_BOISSON) {
            $data['unit'] = Product::UNIT_BOUTEILLE;
        }
        $data['low_stock_threshold'] = (int) ($data['low_stock_threshold'] ?? 0);

        $product->update($data + ['is_active' => $request->boolean('is_active')]);

        if (!empty($data['initial_quantity'])) {
            $initialLocation = $product->type === Product::TYPE_NOURRITURE
                ? 'central_nourriture'
                : 'central_boisson';

            Stock::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'location' => $initialLocation,
                ],
                []
            )->increment('quantity', $data['initial_quantity']);
        }

        return redirect()->route('products.index')->with('status', 'Produit mis a jour.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        DB::transaction(function () use ($product) {
            DB::table('sale_items')->where('product_id', $product->id)->delete();
            DB::table('stock_transfers')->where('product_id', $product->id)->delete();
            DB::table('cook_reports')->where('product_id', $product->id)->delete();
            DB::table('stocks')->where('product_id', $product->id)->delete();
            $product->delete();
        });

        return redirect()->route('products.index')->with('status', 'Produit supprime.');
    }
}
