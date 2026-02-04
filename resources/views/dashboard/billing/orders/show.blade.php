<x-dashboard.layout title="{{ __('main.billing_order_title') }}">
    <div class="flex flex-col gap-6">
        <div>
            <a href="{{ route('frontend.billing.orders.index') }}"
               class="text-xs uppercase tracking-[0.2em] text-slate-500">← {{ __('main.back_to_orders') }}</a>
            <h1 class="mt-2 text-2xl font-bold">{{ __('main.order_number', ['id' => $order->id]) }}</h1>
            <p class="mt-2 text-sm text-slate-600">
                {{ __('main.order_created_status', ['date' => $order->created_at?->format('d.m.Y H:i') ?? '—', 'status' => $order->status]) }}
            </p>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <div class="pixel-outline p-4 text-sm text-slate-600">
                <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">{{ __('main.total') }}</div>
                <div class="mt-2 text-lg font-bold">{{ format_money_minor($order->total_gross_minor, $order->currency) }}</div>
            </div>
            <div class="pixel-outline p-4 text-sm text-slate-600">
                <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">{{ __('main.tax') }}</div>
                <div class="mt-2">{{ format_money_minor($order->tax_minor, $order->currency) }}</div>
            </div>
            <div class="pixel-outline p-4 text-sm text-slate-600">
                <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">{{ __('main.invoice') }}</div>
                <div class="mt-2">
                    @if ($invoice)
                        <a href="{{ route('frontend.billing.invoices.show', $invoice) }}" class="underline">
                            {{ __('main.view_invoice', ['id' => $invoice->id]) }}
                        </a>
                    @else
                        —
                    @endif
                </div>
            </div>
        </div>

        <div class="pixel-outline p-6">
            <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">{{ __('main.items') }}</div>
            <div class="mt-4 overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="text-xs uppercase tracking-[0.2em] text-slate-500">
                            <th class="py-2">{{ __('main.item') }}</th>
                            <th class="py-2">{{ __('main.qty') }}</th>
                            <th class="py-2">{{ __('main.unit') }}</th>
                            <th class="py-2">{{ __('main.tax') }}</th>
                            <th class="py-2">{{ __('main.total') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse ($order->items as $item)
                            <tr>
                                <td class="py-3 font-bold">{{ $item->name_snapshot }}</td>
                                <td class="py-3">{{ $item->qty }}</td>
                                <td class="py-3">{{ format_money_minor($item->unit_net_minor, $order->currency) }}</td>
                                <td class="py-3">{{ format_money_minor($item->tax_minor, $order->currency) }}</td>
                                <td class="py-3">{{ format_money_minor($item->total_gross_minor, $order->currency) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td class="py-3 text-slate-600" colspan="5">{{ __('main.no_items_recorded') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="pixel-outline p-6">
            <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">{{ __('main.status_history') }}</div>
            <div class="mt-4 space-y-2 text-sm text-slate-600">
                @forelse ($order->statusHistory as $history)
                    <div class="flex flex-col gap-1">
                        <div class="font-bold">{{ $history->to_status }}</div>
                        <div>{{ $history->changed_at?->format('d.m.Y H:i') ?? '—' }}</div>
                        @if ($history->note)
                            <div>{{ $history->note }}</div>
                        @endif
                    </div>
                @empty
                    <div>{{ __('main.no_status_history') }}</div>
                @endforelse
            </div>
        </div>
    </div>
</x-dashboard.layout>
