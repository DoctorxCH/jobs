<?php

namespace App\Http\Controllers\Frontend\Billing;

use App\Models\Billing\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InvoiceController extends BaseBillingController
{
    public function index(Request $request)
    {
        $company = $this->resolveCompany($request);
        if (! $company) {
            return $this->companyRequiredView();
        }

        $invoices = Invoice::query()
            ->where('company_id', $company->id)
            ->with('external')
            ->latest('issued_at')
            ->get();

        return view('dashboard.billing.invoices.index', [
            'company' => $company,
            'invoices' => $invoices,
        ]);
    }

    public function show(Request $request, int $invoice)
    {
        $company = $this->resolveCompany($request);
        if (! $company) {
            return $this->companyRequiredView();
        }

        $invoice = Invoice::query()
            ->where('company_id', $company->id)
            ->with(['items.product', 'statusHistory', 'order', 'external', 'payments'])
            ->findOrFail($invoice);

        return view('dashboard.billing.invoices.show', [
            'company' => $company,
            'invoice' => $invoice,
        ]);
    }

    public function download(Request $request, int $invoice)
    {
        $company = $this->resolveCompany($request);
        if (! $company) {
            return $this->companyRequiredView();
        }

        $invoice = Invoice::query()
            ->where('company_id', $company->id)
            ->with('external')
            ->findOrFail($invoice);

        if ($invoice->pdf_path && Storage::exists($invoice->pdf_path)) {
            return Storage::download($invoice->pdf_path);
        }

        if ($invoice->external?->external_pdf_url) {
            return redirect()->away($invoice->external->external_pdf_url);
        }

        return $this->companyRequiredView('PDF not available for this invoice.');
    }
}
