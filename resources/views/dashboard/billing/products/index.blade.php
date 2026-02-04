<x-dashboard.layout title="{{ __('main.billing_products_title') }}">
    <div class="flex flex-col gap-6">
        <div>
            <div class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ __('main.billing') }}</div>
            <h1 class="mt-2 text-2xl font-bold">{{ __('main.products') }}</h1>
        </div>

        
        <h2 class="text-lg font-bold">{{ __('main.how_credits_work_title') }}</h2>
        <p class="mt-1 text-sm text-slate-600 whitespace-pre-line leading-relaxed">
            {{ __('main.how_credits_work_text') }}
        </p>
      

        @if ($products->isEmpty())
            <div class="pixel-outline p-6 text-sm text-slate-600">
                {{ __('main.no_active_products') }}
            </div>
        @else
            <div class="grid gap-4 md:grid-cols-2">
                @foreach ($products as $product)
                    @php
                        $price = $product->current_price;
                        $taxRate = $product->current_tax_rate ?? 0;
                        $grossMinor = $product->current_total_gross_minor;
                    @endphp

                    <div class="pixel-outline pixel-card p-6 flex flex-col gap-4">
                        <div>
                            <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">
                                {{ $product->product_type }}
                            </div>
                            <div class="mt-2 text-lg font-bold">{{ $product->name }}</div>
                            <p class="mt-2 text-sm text-slate-600 whitespace-pre-line leading-6">
                                {{ $product->description ?? __('main.no_description_yet') }}
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
                                <div>{{ __('main.net') }}: <span class="font-bold">{{ format_money_minor($price->unit_net_amount_minor, $price->currency) }}</span></div>
                                <div>
                                    {{ __('main.vat') }}: {{ number_format($taxRate, 2) }}%
                                    @if (($taxClassKey ?? null) === 'neplatca')
                                        <span class="ml-2 font-bold">{{ __('main.not_vat_payer') }}</span>
                                    @endif
                                </div>
                                <div>{{ __('main.total') }}: <span class="font-bold">{{ format_money_minor($grossMinor, $price->currency) }}</span></div>
                            @else
                                <div class="text-slate-500">{{ __('main.pricing_not_available') }}</div>
                            @endif
                        </div>

                        <div>
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('frontend.billing.products.show', $product) }}"
                                   class="pixel-button inline-flex px-4 py-2 text-xs uppercase tracking-[0.2em]">
                                    {{ __('main.view_details') }}
                                </a>
                                @if ($price)
                                    <a href="{{ route('frontend.billing.products.checkout', $product) }}"
                                       class="pixel-button inline-flex px-4 py-2 text-xs uppercase tracking-[0.2em]">
                                        {{ __('main.buy') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-dashboard.layout>
