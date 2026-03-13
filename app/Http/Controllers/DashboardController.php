<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Sale;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockTransfer;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $salesToday = Sale::whereDate('created_at', $today)
            ->where('status', 'valide')
            ->sum('total_amount');

        $productsCount = Product::count();

        $stocks = Stock::with('product')->get();
        $user = Auth::user();

        if ($user?->hasRole('caissier')) {
            $stockValueNourriture = $stocks
                ->filter(fn ($s) => $s->location === 'frigo_cuisine')
                ->sum(fn ($s) => ($s->product?->sale_price ?? 0) * (float) $s->quantity);

            $stockValueBoisson = $stocks
                ->filter(fn ($s) => in_array($s->location, ['serveur', 'frigo_vente'], true))
                ->sum(fn ($s) => ($s->product?->sale_price ?? 0) * (float) $s->quantity);
        } else {
            $stockValueNourriture = $stocks
                ->filter(fn ($s) => $s->product?->type === Product::TYPE_NOURRITURE)
                ->sum(fn ($s) => ($s->product?->sale_price ?? 0) * (float) $s->quantity);

            $stockValueBoisson = $stocks
                ->filter(fn ($s) => $s->product?->type === Product::TYPE_BOISSON)
                ->sum(fn ($s) => ($s->product?->sale_price ?? 0) * (float) $s->quantity);
        }

        $stockValueTotal = $stockValueNourriture + $stockValueBoisson;

        $lowStocksQuery = Stock::with('product')
            ->join('products', 'products.id', '=', 'stocks.product_id')
            ->where('products.is_active', true)
            ->where('products.low_stock_threshold', '>', 0)
            ->whereColumn('stocks.quantity', '<=', 'products.low_stock_threshold')
            ->select('stocks.*')
            ->orderBy('stocks.quantity');

        if ($user?->hasRole('caissier')) {
            $lowStocksQuery->whereIn('stocks.location', ['serveur', 'frigo_cuisine', 'frigo_vente']);
        }

        $lowStocks = $lowStocksQuery->get();

        $lastTransfersQuery = StockTransfer::with('product')
            ->latest();

        if ($user?->hasRole('caissier')) {
            $lastTransfersQuery
                ->where('from_location', 'serveur')
                ->where('to_location', 'frigo_vente');
        }

        $lastTransfers = $lastTransfersQuery
            ->take(5)
            ->get();

        return view('dashboard', [
            'user' => Auth::user(),
            'salesToday' => $salesToday,
            'productsCount' => $productsCount,
            'stockValueNourriture' => $stockValueNourriture,
            'stockValueBoisson' => $stockValueBoisson,
            'stockValueTotal' => $stockValueTotal,
            'lowStocks' => $lowStocks,
            'lastTransfers' => $lastTransfers,
        ]);
    }
}
