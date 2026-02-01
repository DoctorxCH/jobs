<x-dashboard.layout title="Billing · Products">
    <div class="flex flex-col gap-6">
        <div>
            <div class="text-xs uppercase tracking-[0.2em] text-slate-500">Billing</div>
            <h1 class="mt-2 text-2xl font-bold">Products</h1>
            <p class="mt-2 text-sm text-slate-600">
                Choose a product to purchase for your company.
            </p>
        </div>

        @if ($products->isEmpty())
            <div class="pixel-outline p-6 text-sm text-slate-600">
                No active products are available right now.
            </div>
        @else
            <div class="grid gap-4 md:grid-cols-2">
                @foreach ($products as $product)
                    @php
                        $price = $product->current_price;
                        $taxRate = $product->current_tax_rate ?? 0;
                        $grossMinor = $product->current_total_gross_minor;
                    @endphp

                    <div class="pixel-outline p-6 flex flex-col gap-4 product-details">
                        <div>
                            <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">
                                {{ $product->product_type }}
                            </div>
                            <div class="mt-2 text-lg font-bold">{{ $product->name }}</div>
                            <p class="mt-2 text-sm text-slate-600 whitespace-pre-line leading-6">
                                {{ $product->description ?? 'No description provided yet.' }}
                            </p>
                        </div>

                        <div class="text-sm text-slate-700">
                            @if ($price)
                                @php
                                    $taxClassKey = $product->taxClass?->key ?? $price?->taxClass?->key ?? null;
                                    if (! $taxClassKey) {
                                        $taxClassId = $product->tax_class_id ?? $price?->tax_class_id ?? null;
                                        if ($taxClassId) {
                                            $taxClassKey = \Illuminate\Support\Facades\DB::table('tax_classes')
                                                ->where('id', $taxClassId)
                                                ->value('key');
                                        }
                                    }
                                @endphp
                                <div>Net: <span class="font-bold">{{ format_money_minor($price->unit_net_amount_minor, $price->currency) }}</span></div>
                                <div>
                                    VAT: {{ number_format($taxRate, 2) }}%
                                    @if (($taxClassKey ?? null) === 'neplatca')
                                        <span class="ml-2 font-bold">{{ __('main.not_vat_payer') }}</span>
                                    @endif
                                </div>
                                <div>Total: <span class="font-bold">{{ format_money_minor($grossMinor, $price->currency) }}</span></div>
                            @else
                                <div class="text-slate-500">Pricing not available.</div>
                            @endif
                        </div>

                        <div>
                            <a href="{{ route('frontend.billing.products.show', $product) }}"
                               class="inline-flex pixel-outline px-4 py-2 text-xs uppercase tracking-[0.2em]">
                                View details
                            </a>
                            @if ($price)
                                <a href="{{ route('frontend.billing.products.checkout', $product) }}"
                                   class="ml-2 inline-flex pixel-outline px-4 py-2 text-xs uppercase tracking-[0.2em]">
                                    Buy
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-dashboard.layout>
