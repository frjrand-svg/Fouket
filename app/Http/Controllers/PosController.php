<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\CashSession;
use App\Models\SaleItem;
use App\Models\Stock;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class PosController extends Controller
{
    public function index()
    {
        $openSession = CashSession::where('user_id', auth()->id())
            ->whereNull('closed_at')
            ->latest('opened_at')
            ->first();

        if (!$openSession) {
            return redirect()->route('cash.open.form');
        }

        $products = Product::where('is_active', true)
            ->with('stocks')
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        return view('pos.index', compact('products', 'openSession'));
    }

    public function salesToday()
    {
        $openSession = CashSession::where('user_id', auth()->id())
            ->whereNull('closed_at')
            ->latest('opened_at')
            ->first();

        if (!$openSession) {
            return redirect()->route('cash.open.form');
        }

        $salesToday = Sale::with('items.product')
            ->where('cash_session_id', $openSession->id)
            ->orderByDesc('created_at')
            ->get();

        return view('pos.sales-today', compact('salesToday'));
    }

    public function store(Request $request): RedirectResponse
    {
        $openSession = CashSession::where('user_id', $request->user()->id)
            ->whereNull('closed_at')
            ->latest('opened_at')
            ->first();

        if (!$openSession) {
            return back()->withErrors(['cash' => 'Caisse fermee. Ouvrez la caisse avant de vendre.']);
        }

        $status = $request->input('status', 'valide');
        $request->merge(['status' => $status]);

        $rules = [
            'status' => ['required', 'in:valide,brouillon'],
        ];

        if ($status === 'valide') {
            $rules['payment_method'] = ['required', 'in:cash,mobile,orange_money'];
            $rules['cash_amount'] = ['nullable', 'integer', 'min:0'];
            $rules['mobile_amount'] = ['nullable', 'integer', 'min:0'];
        }

        $request->validate($rules);

        $rawItems = $request->input('items', []);
        $itemsInput = collect($rawItems)
            ->filter(fn ($item) => (int) ($item['quantity'] ?? 0) > 0)
            ->values();

        if ($itemsInput->isEmpty()) {
            return back()->withErrors(['items' => 'Aucun article sÃ©lectionnÃ©.']);
        }

        Validator::make(
            ['items' => $itemsInput->all()],
            [
                'items' => ['required', 'array'],
                'items.*.product_id' => ['required', 'exists:products,id'],
                'items.*.quantity' => ['required', 'integer', 'min:1'],
                'items.*.stock_location' => ['required', 'in:frigo_vente,frigo_cuisine,serveur'],
            ]
        )->validate();

        $paymentMethod = $status === 'valide' ? $request->payment_method : 'cash';
        $cash = (float) ($request->cash_amount ?? 0);
        $mobile = (float) ($request->mobile_amount ?? 0);

        DB::transaction(function () use ($itemsInput, $paymentMethod, $cash, $mobile, $request, $status, $openSession) {
            $user = $request->user();

            $sale = Sale::create([
                'user_id' => $user->id,
                'cash_session_id' => $openSession->id,
                'reference' => $this->generateReference(),
                'total_amount' => 0,
                'cash_amount' => 0,
                'mobile_amount' => 0,
                'payment_method' => $paymentMethod,
                'status' => $status,
                'paid_at' => $status === 'valide' ? now() : null,
            ]);

            $total = 0;

            foreach ($itemsInput as $row) {
                $product = Product::find($row['product_id']);
                $qty = (int) $row['quantity'];
                $stockLocation = $row['stock_location'];

                $stock = Stock::firstOrCreate(
                    ['product_id' => $product->id, 'location' => $stockLocation],
                    ['quantity' => 0]
                );

                if ($stock->quantity < $qty) {
                    throw new \RuntimeException("Stock insuffisant pour {$product->name} ({$stockLocation}).");
                }

                $lineTotal = $product->sale_price * $qty;
                $total += $lineTotal;

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unit_price' => $product->sale_price,
                    'line_total' => $lineTotal,
                    'stock_source' => $stockLocation,
                ]);

                $stock->decrement('quantity', $qty);
            }

            if ($status === 'valide') {
                // Payment split rules
                if ($paymentMethod === 'cash') {
                    $cash = $total;
                    $mobile = 0;
                } elseif (in_array($paymentMethod, ['mobile', 'orange_money'], true)) {
                    $mobile = $total;
                    $cash = 0;
                } else {
                    $cash = $total;
                    $mobile = 0;
                }
            } else {
                $cash = 0;
                $mobile = 0;
            }

            $sale->update([
                'total_amount' => $total,
                'cash_amount' => $cash,
                'mobile_amount' => $mobile,
            ]);
        });

        return redirect()->route('pos.index')->with('status', 'Vente enregistrÃƒÂ©e.');
    }

    protected function generateReference(): string
    {
        $prefix = 'SAL-' . now()->format('Ymd');
        $increment = Sale::whereDate('created_at', now()->toDateString())->count() + 1;
        return sprintf('%s-%04d', $prefix, $increment);
    }
}
