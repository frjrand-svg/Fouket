<?php

namespace App\Http\Controllers;

use App\Models\CashSession;
use App\Models\Sale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Carbon\Carbon;

class CashSessionController extends Controller
{
    public function showOpenForm(Request $request)
    {
        $session = CashSession::where('user_id', $request->user()->id)
            ->whereNull('closed_at')
            ->latest('opened_at')
            ->first();

        if ($session) {
            return redirect()->route('pos.index');
        }

        return view('cash.open');
    }

    public function open(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = $request->user();

        if ($user->email !== $request->email) {
            return back()->withErrors(['email' => 'Identifiants invalides.']);
        }

        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Identifiants invalides.']);
        }

        $existing = CashSession::where('user_id', $user->id)
            ->whereNull('closed_at')
            ->first();

        if ($existing) {
            return back()->withErrors(['cash' => 'La caisse est deja ouverte.']);
        }

        CashSession::create([
            'user_id' => $user->id,
            'opened_at' => now(),
            'opening_cash' => 0,
        ]);

        return redirect()->route('pos.index')->with('status', 'Caisse ouverte.');
    }

    public function showCloseForm(Request $request)
    {
        $session = CashSession::where('user_id', $request->user()->id)
            ->whereNull('closed_at')
            ->latest('opened_at')
            ->first();

        if (!$session) {
            return redirect()->route('cash.open.form')->withErrors(['cash' => 'Aucune caisse ouverte.']);
        }

        return view('cash.close', compact('session'));
    }

    public function close(Request $request): RedirectResponse
    {
        $request->validate(
            [
                'comment' => ['required', 'string', 'min:5', 'max:1000'],
            ],
            [
                'comment.required' => 'Le rapport de la journee est obligatoire.',
                'comment.string' => 'Le rapport de la journee doit etre un texte.',
                'comment.min' => 'Le rapport de la journee doit contenir au moins 5 caracteres.',
                'comment.max' => 'Le rapport de la journee ne doit pas depasser 1000 caracteres.',
            ]
        );

        $user = $request->user();
        $session = CashSession::where('user_id', $user->id)
            ->whereNull('closed_at')
            ->latest('opened_at')
            ->first();

        if (!$session) {
            return back()->withErrors(['cash' => 'Aucune caisse ouverte.']);
        }

        $totals = Sale::where('cash_session_id', $session->id)
            ->where('status', 'valide')
            ->selectRaw('COALESCE(SUM(total_amount),0) as total_sales')
            ->selectRaw('COALESCE(SUM(cash_amount),0) as total_cash')
            ->selectRaw('COALESCE(SUM(mobile_amount),0) as total_mobile')
            ->first();

        $totalSales = (float) ($totals->total_sales ?? 0);
        $totalCash = (float) ($totals->total_cash ?? 0);
        $totalMobile = (float) ($totals->total_mobile ?? 0);
        $expectedCash = (float) $session->opening_cash + $totalCash;
        $closingCash = $expectedCash;
        $difference = 0;

        $session->update([
            'closed_at' => now(),
            'closing_cash' => $closingCash,
            'total_sales' => $totalSales,
            'total_cash' => $totalCash,
            'total_mobile' => $totalMobile,
            'difference' => $difference,
            'comment' => $request->comment,
        ]);

        return redirect()->route('cash.open.form')->with('status', 'Caisse fermee.');
    }

    public function history(Request $request): View
    {
        $sessions = CashSession::with([
            'user',
            'sales' => function ($query) {
                $query->orderBy('created_at');
            },
        ])
            ->whereNotNull('closed_at')
            ->orderByDesc('opened_at')
            ->limit(60)
            ->get();

        $sessionsByDay = $sessions->groupBy(function ($session) {
            return $session->closed_at
                ? $session->closed_at->timezone(config('app.timezone'))->format('Y-m-d')
                : '—';
        });

        return view('cash.history', compact('sessionsByDay'));
    }

    public function historyDay(Request $request, string $date): View
    {
        try {
            $day = Carbon::createFromFormat('Y-m-d', $date, config('app.timezone'))->startOfDay();
        } catch (\Throwable $e) {
            abort(404);
        }

        $start = $day->copy();
        $end = $day->copy()->endOfDay();

        $sessions = CashSession::with([
            'user',
            'sales' => function ($query) {
                $query->orderBy('created_at');
            },
        ])
            ->whereBetween('closed_at', [$start, $end])
            ->orderBy('opened_at')
            ->get();

        $sales = $sessions->flatMap->sales;

        $totals = [
            'count' => $sales->count(),
            'total_sales' => (int) $sales->sum('total_amount'),
            'total_cash' => (int) $sales->sum('cash_amount'),
            'total_mobile' => (int) $sales->sum('mobile_amount'),
        ];

        return view('cash.history-day', [
            'day' => $day,
            'sessions' => $sessions,
            'sales' => $sales,
            'totals' => $totals,
        ]);
    }
}
