<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\CashSession;
use App\Services\OrderPaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CashSessionController extends Controller
{
    public function index(): View
    {
        return view('cash-sessions.index', [
            'sessions' => CashSession::with('user')->latest('opened_at')->paginate(20),
            'current' => auth()->user()->openCashSession(),
        ]);
    }

    public function open(Request $request): RedirectResponse
    {
        if (auth()->user()->openCashSession()) {
            return back()->withErrors(['session' => 'Vous avez déjà une session ouverte.']);
        }

        $data = $request->validate([
            'opening_float' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $session = CashSession::create([
            'user_id' => auth()->id(),
            'status' => 'open',
            'opening_float' => $data['opening_float'],
            'notes' => $data['notes'] ?? null,
            'opened_at' => now(),
        ]);

        ActivityLog::record('cash.open', $session, 'Ouverture de caisse');

        return back()->with('success', 'Caisse ouverte.');
    }

    public function close(Request $request, CashSession $cashSession, OrderPaymentService $service): RedirectResponse
    {
        if ($cashSession->user_id !== auth()->id() && ! auth()->user()->canAccess('admin', 'manager', 'accountant')) {
            abort(403);
        }

        if ($cashSession->status !== 'open') {
            return back()->withErrors(['session' => 'Session déjà clôturée.']);
        }

        $data = $request->validate([
            'closing_counted' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $service->closeCashSession($cashSession, (float) $data['closing_counted'], $data['notes'] ?? null);

        return redirect()->route('cash-sessions.show', $cashSession)->with('success', 'Caisse clôturée.');
    }

    public function show(CashSession $cashSession): View
    {
        $cashSession->load(['user', 'payments.order']);

        return view('cash-sessions.show', ['session' => $cashSession]);
    }
}
