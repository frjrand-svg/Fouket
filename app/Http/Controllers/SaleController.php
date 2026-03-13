<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Stock;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function show(Request $request, Sale $sale)
    {
        $this->authorize('view', $sale);
        $sale->load(['items.product', 'user', 'cashSession']);

        return view('sales.show', compact('sale'));
    }

    public function detail(Request $request, Sale $sale): JsonResponse
    {
        $this->authorize('view', $sale);

        $sale->load(['items.product', 'user']);

        $html = view('cash.partials.sale-detail', compact('sale'))->render();

        return response()->json([
            'html' => $html,
        ]);
    }

    public function validateSale(Request $request, Sale $sale): RedirectResponse
    {
        $this->authorize('cancel', $sale);

        if ($sale->status !== 'brouillon') {
            return back()->withErrors(['sale' => 'La vente nâ€™est pas en attente.']);
        }
        if ($sale->cashSession && $sale->cashSession->closed_at) {
            return back()->withErrors(['sale' => 'La caisse est fermee.']);
        }

        $paymentMethod = $sale->payment_method ?? 'cash';
        $total = (float) $sale->total_amount;
        $cash = 0;
        $mobile = 0;

        if ($paymentMethod === 'cash') {
            $cash = $total;
        } elseif (in_array($paymentMethod, ['mobile', 'orange_money'], true)) {
            $mobile = $total;
        } else {
            $cash = $total;
            $mobile = 0;
        }

        $sale->update([
            'status' => 'valide',
            'paid_at' => now(),
            'cash_amount' => $cash,
            'mobile_amount' => $mobile,
            'payment_method' => $paymentMethod,
        ]);

        return back()->with('status', 'Commande validÃ©e.');
    }

    public function cancel(Request $request, Sale $sale): RedirectResponse
    {
        $request->validate([
            'reason' => ['required', 'string', 'min:3'],
        ]);

        $this->authorize('cancel', $sale);

        if ($sale->status !== 'brouillon') {
            return back()->withErrors(['sale' => 'La vente nâ€™est pas annulable.']);
        }
        if ($sale->cashSession && $sale->cashSession->closed_at) {
            return back()->withErrors(['sale' => 'La caisse est fermee.']);
        }

        DB::transaction(function () use ($sale, $request) {
            /** @var Sale $sale */
            foreach ($sale->items as $item) {
                $stock = Stock::firstOrCreate(
                    ['product_id' => $item->product_id, 'location' => $item->stock_source],
                    ['quantity' => 0]
                );
                $stock->increment('quantity', $item->quantity);
            }

            $sale->update([
                'status' => 'annule',
                'cancellation_reason' => $request->reason,
            ]);
        });

        return back()->with('status', 'Vente annulÃ©e et stock rÃ©tabli.');
    }
}
