<?php

namespace App\Http\Controllers\Frontend\Billing;

use App\Models\Billing\InvoiceItem;
use App\Models\Billing\InvoiceStatusHistory;
use App\Models\Billing\OrderItem;
use App\Models\Billing\OrderStatusHistory;
use App\Models\Billing\Product;
use App\Models\Billing\ProductPrice;
use App\Models\Company;
use App\Services\Billing\OrderService;
use App\Services\Billing\TaxRuleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProductController extends BaseBillingController
{
    public function __construct(
        private readonly TaxRuleService $taxRuleService,
        private readonly OrderService $orderService,
    ) {
    }

    public function index(Request $request)
    {
        $company = $this->resolveCompany($request);
        if (! $company) {
            return $this->companyRequiredView();
        }

        $products = Product::query()
            ->where('active', true)
            ->with(['prices' => function ($query) {
                $query->where('active', true)->orderBy('currency');
            }, 'prices.taxClass.taxRates'])
            ->orderBy('name')
            ->get();

        $taxRule = $this->taxRuleService->determineForCompany($company);

        $products->each(function (Product $product) use ($company, $taxRule) {
            $price = $this->selectPrice($product);
            $taxRate = $price ? $this->resolveTaxRate($price, $company, $taxRule) : 0.0;

            $product->setAttribute('current_price', $price);
            $product->setAttribute('current_tax_rate', $taxRate);
            $product->setAttribute(
                'current_total_gross_minor',
                $price ? $this->grossMinor($price->unit_net_amount_minor, $taxRate) : null
            );
        });

        return view('dashboard.billing.products.index', [
            'company' => $company,
            'products' => $products,
        ]);
    }

    public function show(Request $request, Product $product)
    {
        $company = $this->resolveCompany($request);
        if (! $company) {
            return $this->companyRequiredView();
        }

        if (! $product->active) {
            abort(404);
        }

        $product->load(['prices' => function ($query) {
            $query->where('active', true)->orderBy('currency');
        }, 'prices.taxClass.taxRates']);

        $price = $this->selectPrice($product);
        $taxRule = $this->taxRuleService->determineForCompany($company);
        $taxRate = $price ? $this->resolveTaxRate($price, $company, $taxRule) : 0.0;

        return view('dashboard.billing.products.show', [
            'company' => $company,
            'product' => $product,
            'price' => $price,
            'taxRate' => $taxRate,
            'taxRule' => $taxRule,
            'grossMinor' => $price ? $this->grossMinor($price->unit_net_amount_minor, $taxRate) : null,
            'taxMinor' => $price ? $this->taxMinor($price->unit_net_amount_minor, $taxRate) : null,
        ]);
    }
public function checkout(Request $request, Product $product)
    {
        $company = $this->resolveCompany($request);
        if (! $company) {
            return $this->companyRequiredView();
        }

        if (! $product->active) {
            abort(404);
        }

        $product->load(['prices' => function ($query) {
            $query->where('active', true)->orderBy('currency');
        }, 'prices.taxClass.taxRates']);

        $price = $this->selectPrice($product);
        if (! $price) {
            return $this->companyRequiredView('This product is not available for purchase right now.');
        }

        $taxRule = $this->taxRuleService->determineForCompany($company);
        $taxRate = $this->resolveTaxRate($price, $company, $taxRule);

        // Allow preselect via ?qty=30 (slider persistence)
        $qty = (int) $request->query('qty', 1);
        if ($qty < 1) { $qty = 1; }
        if ($qty > 100) { $qty = 100; }

        $unitTaxMinor = $this->taxMinor($price->unit_net_amount_minor, $taxRate);
        $unitGrossMinor = $this->grossMinor($price->unit_net_amount_minor, $taxRate);

        $subtotalNetMinor = $price->unit_net_amount_minor * $qty;
        $taxMinor = $unitTaxMinor * $qty;
        $grossMinor = $unitGrossMinor * $qty;

        return view('dashboard.billing.products.checkout', [
            'company' => $company,
            'product' => $product,
            'price' => $price,
            'taxRate' => $taxRate,
            'taxRule' => $taxRule,

            'qty' => $qty,
            'unitTaxMinor' => $unitTaxMinor,
            'unitGrossMinor' => $unitGrossMinor,
            'subtotalNetMinor' => $subtotalNetMinor,
            'taxMinor' => $taxMinor,
            'grossMinor' => $grossMinor,
        ]);
    }
public function placeOrder(Request $request, Product $product): RedirectResponse
    {
        $company = $this->resolveCompany($request);
        if (! $company) {
            return redirect()
                ->route('frontend.billing.products.index')
                ->with('status', 'Billing is available only for users linked to a company.');
        }

        if (! $product->active) {
            abort(404);
        }

        $product->load(['prices' => function ($query) {
            $query->where('active', true)->orderBy('currency');
        }, 'prices.taxClass.taxRates']);

        $price = $this->selectPrice($product);
        if (! $price) {
            return redirect()
                ->route('frontend.billing.products.index')
                ->with('status', 'This product is not available for purchase right now.');
        }

        $qty = (int) $request->input('qty', 1);
        if ($qty < 1 || $qty > 100) {
            return redirect()
                ->route('frontend.billing.products.checkout', $product)
                ->with('status', 'Invalid quantity. Please choose between 1 and 100.');
        }

        $taxRule = $this->taxRuleService->determineForCompany($company);
        $taxRate = $this->resolveTaxRate($price, $company, $taxRule);

        $unitTaxMinor = $this->taxMinor($price->unit_net_amount_minor, $taxRate);
        $unitGrossMinor = $this->grossMinor($price->unit_net_amount_minor, $taxRate);

        $subtotalNetMinor = $price->unit_net_amount_minor * $qty;
        $taxMinor = $unitTaxMinor * $qty;
        $grossMinor = $unitGrossMinor * $qty;

        $order = $this->orderService->createDraft($company, $request->user(), $price->currency, []);
        $order->update([
            'status' => 'submitted',
            'tax_rule_applied' => $taxRule['tax_rule'],
            'reverse_charge' => $taxRule['reverse_charge'],
            'tax_rate_percent_snapshot' => $taxRate,
            'subtotal_net_minor' => $subtotalNetMinor,
            'discount_minor' => 0,
            'tax_minor' => $taxMinor,
            'total_gross_minor' => $grossMinor,
            'coupon_discount_minor' => 0,
        ]);

        OrderItem::query()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'name_snapshot' => $product->name,
            'qty' => $qty,
            'unit_net_minor' => $price->unit_net_amount_minor,
            'tax_rate_percent' => $taxRate,
            'tax_minor' => $taxMinor,
            'total_gross_minor' => $grossMinor,
        ]);

        OrderStatusHistory::query()->create([
            'order_id' => $order->id,
            'from_status' => null,
            'to_status' => 'submitted',
            'changed_by_user_id' => $request->user()?->id,
            'note' => 'Order placed from company dashboard checkout.',
            'meta' => null,
            'changed_at' => now(),
        ]);

        $invoice = $this->orderService->createInvoiceForOrder($order);

        $customerName = $company->legal_name ?: trim(($company->contact_first_name ?? '').' '.($company->contact_last_name ?? ''));
        $customerName = $customerName !== '' ? $customerName : 'Company';

        $invoice->update([
            'status' => 'issued_unpaid',
            'issued_at' => now(),
            'due_at' => now()->addDays(14),
            'customer_name_snapshot' => $customerName,
            'customer_address_snapshot' => $this->formatCompanyAddress($company),
            'customer_country_snapshot' => $company->country_code ?? 'SK',
            'customer_vat_id_snapshot' => $company->ic_dph ?: $company->dic,
            'tax_rule_applied' => $taxRule['tax_rule'],
            'reverse_charge' => $taxRule['reverse_charge'],
            'tax_rate_percent_snapshot' => $taxRate,
            'subtotal_net_minor' => $subtotalNetMinor,
            'discount_minor' => 0,
            'tax_minor' => $taxMinor,
            'total_gross_minor' => $grossMinor,
        ]);

        InvoiceItem::query()->create([
            'invoice_id' => $invoice->id,
            'product_id' => $product->id,
            'name_snapshot' => $product->name,
            'qty' => $qty,
            'unit_net_minor' => $price->unit_net_amount_minor,
            'tax_rate_percent' => $taxRate,
            'tax_minor' => $taxMinor,
            'total_gross_minor' => $grossMinor,
        ]);

        InvoiceStatusHistory::query()->create([
            'invoice_id' => $invoice->id,
            'from_status' => null,
            'to_status' => 'issued_unpaid',
            'changed_by_user_id' => $request->user()?->id,
            'note' => 'Invoice issued from company dashboard checkout.',
            'meta' => null,
            'changed_at' => now(),
        ]);

        return redirect()
            ->route('frontend.billing.invoices.show', $invoice)
            ->with('status', 'Order placed! Your invoice is ready below.');
    }


    private function selectPrice(Product $product): ?ProductPrice
    {
        $prices = $product->prices
            ->where('active', true)
            ->sortBy('currency');

        $eur = $prices->firstWhere('currency', 'EUR');

        return $eur ?? $prices->first();
    }

    private function resolveTaxRate(ProductPrice $price, Company $company, array $taxRule): float
    {
        if ($taxRule['reverse_charge'] ?? false) {
            return 0.0;
        }

        if (isset($taxRule['tax_rate_percent']) && $taxRule['tax_rate_percent'] !== null) {
            return (float) $taxRule['tax_rate_percent'];
        }

        $rates = $price->taxClass?->taxRates ?? collect();
        $country = strtoupper((string) ($company->country_code ?? 'SK'));
        $today = now()->toDateString();

        $rate = $rates->first(function ($rate) use ($country, $today) {
            $matchesCountry = strtoupper((string) $rate->country_code) === $country;
            $validFromOk = ! $rate->valid_from || $rate->valid_from <= $today;
            $validToOk = ! $rate->valid_to || $rate->valid_to >= $today;

            return $matchesCountry && $validFromOk && $validToOk;
        });

        $rate = $rate ?? $rates->first();

        return $rate ? (float) $rate->rate_percent : 0.0;
    }

    private function taxMinor(int $unitNetMinor, float $taxRate): int
    {
        return (int) round($unitNetMinor * ($taxRate / 100));
    }

    private function grossMinor(int $unitNetMinor, float $taxRate): int
    {
        return $unitNetMinor + $this->taxMinor($unitNetMinor, $taxRate);
    }

    private function formatCompanyAddress(Company $company): string
    {
        $parts = array_filter([
            $company->street,
            trim(($company->postal_code ?? '').' '.($company->city ?? '')),
            $company->country_code,
        ]);

        return implode("\n", $parts);
    }
}
