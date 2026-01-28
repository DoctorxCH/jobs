<?php

namespace App\Http\Controllers\Frontend\Billing;

use App\Models\Billing\Invoice;
use App\Models\Billing\Order;
use Illuminate\Http\Request;

class OrderController extends BaseBillingController
{
    public function index(Request $request)
    {
        $company = $this->resolveCompany($request);
        if (! $company) {
            return $this->companyRequiredView();
        }

        $orders = Order::query()
            ->where('company_id', $company->id)
            ->latest()
            ->get();

        $invoices = Invoice::query()
            ->whereIn('order_id', $orders->pluck('id'))
            ->get()
            ->keyBy('order_id');

        return view('dashboard.billing.orders.index', [
            'company' => $company,
            'orders' => $orders,
            'invoices' => $invoices,
        ]);
    }

    public function show(Request $request, int $order)
    {
        $company = $this->resolveCompany($request);
        if (! $company) {
            return $this->companyRequiredView();
        }

        $order = Order::query()
            ->where('company_id', $company->id)
            ->with(['items.product', 'statusHistory'])
            ->findOrFail($order);

        $invoice = Invoice::query()
            ->where('order_id', $order->id)
            ->first();

        return view('dashboard.billing.orders.show', [
            'company' => $company,
            'order' => $order,
            'invoice' => $invoice,
        ]);
    }
}
