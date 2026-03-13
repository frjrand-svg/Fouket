<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use Illuminate\View\View;

class StockController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $stocksQuery = Stock::with('product');

        if ($user?->role?->slug === 'caissier') {
            $stocksQuery
                ->whereHas('product', fn ($q) => $q->where('type', 'boisson'))
                ->whereIn('location', [
                    'serveur',
                    'frigo_vente',
                ]);
        }

        $stocksCollection = $stocksQuery
            ->get()
            ->sortBy(fn ($s) => $s->product?->name);

        $stocksByLocation = $stocksCollection->groupBy('location');

        $totalsByLocation = $stocksCollection->groupBy('location')->map(function ($items) {
            $qty = $items->sum('quantity');
            $value = $items->sum(fn ($s) => ($s->product?->sale_price ?? 0) * (float) $s->quantity);
            return ['qty' => $qty, 'value' => $value];
        });

        $locationOrder = [
            'central_nourriture',
            'central_boisson',
            'frigo_cuisine',
            'frigo_vente',
            'serveur',
        ];

        $orderedKeys = collect($locationOrder)
            ->filter(fn ($location) => $stocksByLocation->has($location))
            ->concat(
                $stocksByLocation->keys()->reject(fn ($location) => in_array($location, $locationOrder, true))
            )
            ->values();

        $stocks = $orderedKeys->mapWithKeys(fn ($location) => [$location => $stocksByLocation->get($location)]);
        $totals = $orderedKeys->mapWithKeys(fn ($location) => [$location => $totalsByLocation->get($location)]);

        return view('stocks.index', compact('stocks', 'totals'));
    }
}
