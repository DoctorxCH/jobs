<x-dashboard.layout title="{{ __('main.billing_orders_title') }}">
    <div class="flex flex-col gap-6">
        <div>
            <div class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ __('main.billing') }}</div>
            <h1 class="mt-2 text-2xl font-bold">{{ __('main.orders') }}</h1>
            <p class="mt-2 text-sm text-slate-600">
                {{ __('main.orders_intro') }}
            </p>
        </div>

        @if ($orders->isEmpty())
            <div class="pixel-outline p-6 text-sm text-slate-600">
                {{ __('main.no_orders_yet') }}
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="text-xs uppercase tracking-[0.2em] text-slate-500">
                            <th class="py-2">{{ __('main.order') }}</th>
                            <th class="py-2">{{ __('main.date') }}</th>
                            <th class="py-2">{{ __('main.status') }}</th>
                            <th class="py-2">{{ __('main.total') }}</th>
                            <th class="py-2">{{ __('main.invoice') }}</th>
                            <th class="py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach ($orders as $order)
                            @php
                                $invoice = $invoices->get($order->id);
                            @endphp
                            <tr>
                                <td class="py-3 font-bold">#{{ $order->id }}</td>
                                <td class="py-3">{{ $order->created_at?->format('d.m.Y') ?? '—' }}</td>
                                <td class="py-3">{{ $order->status }}</td>
                                <td class="py-3">{{ format_money_minor($order->total_gross_minor, $order->currency) }}</td>
                                <td class="py-3">
                                    @if ($invoice)
                                        <a href="{{ route('frontend.billing.invoices.show', $invoice) }}"
                                           class="underline">
                                            #{{ $invoice->id }}
                                        </a>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="py-3 text-right">
                                    <a href="{{ route('frontend.billing.orders.show', $order) }}"
                                       class="inline-flex pixel-outline px-3 py-1 text-xs uppercase tracking-[0.2em]">
                                        {{ __('main.view') }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-dashboard.layout>
