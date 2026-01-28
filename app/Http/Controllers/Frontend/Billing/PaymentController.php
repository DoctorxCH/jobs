<?php

namespace App\Http\Controllers\Frontend\Billing;

use App\Models\Billing\Payment;
use Illuminate\Http\Request;

class PaymentController extends BaseBillingController
{
    public function index(Request $request)
    {
        $company = $this->resolveCompany($request);
        if (! $company) {
            return $this->companyRequiredView();
        }

        $payments = Payment::query()
            ->whereHas('invoice', function ($query) use ($company) {
                $query->where('company_id', $company->id);
            })
            ->with('invoice')
            ->latest()
            ->get();

        return view('dashboard.billing.payments.index', [
            'company' => $company,
            'payments' => $payments,
        ]);
    }

    public function show(Request $request, int $payment)
    {
        $company = $this->resolveCompany($request);
        if (! $company) {
            return $this->companyRequiredView();
        }

        $payment = Payment::query()
            ->whereHas('invoice', function ($query) use ($company) {
                $query->where('company_id', $company->id);
            })
            ->with(['invoice', 'statusHistory'])
            ->findOrFail($payment);

        return view('dashboard.billing.payments.show', [
            'company' => $company,
            'payment' => $payment,
        ]);
    }
}
