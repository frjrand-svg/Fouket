<?php

namespace App\Http\Controllers;

use App\Http\Requests\StockTransferRequest;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockTransfer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StockTransferController extends Controller
{
    public function create(): View
    {
        $productsQuery = Product::orderBy('name');

        if (auth()->user()?->role?->slug === 'caissier') {
            $productsQuery->where('type', 'boisson');
        }

        $products = $productsQuery->get();

        return view('transfers.create', compact('products'));
    }

    public function store(StockTransferRequest $request): RedirectResponse
    {
        $data = $request->validated();

        try {
            DB::transaction(function () use ($data, $request) {
                $from = $data['from_location'] ?? null;
                $to = $data['to_location'];
                $qty = (int) $data['quantity'];
                $productId = $data['product_id'];

                if ($from) {
                    $fromStock = Stock::firstOrCreate(
                        ['product_id' => $productId, 'location' => $from],
                        ['quantity' => 0]
                    );

                    if ($fromStock->quantity < $qty) {
                        throw new \RuntimeException('Stock insuffisant dans la source.');
                    }

                    $fromStock->decrement('quantity', $qty);
                }

                $toStock = Stock::firstOrCreate(
                    ['product_id' => $productId, 'location' => $to],
                    ['quantity' => 0]
                );
                $toStock->increment('quantity', $qty);

                StockTransfer::create([
                    'product_id' => $productId,
                    'user_id' => $request->user()->id,
                    'from_location' => $from,
                    'to_location' => $to,
                    'quantity' => $qty,
                    'status' => 'valide',
                    'justification' => $data['justification'] ?? null,
                ]);
            });
        } catch (\RuntimeException $e) {
            return back()->withErrors(['quantity' => $e->getMessage()])->withInput();
        }

        return redirect()->route('stocks.index')->with('status', 'Transfert enregistré.');
    }
}
