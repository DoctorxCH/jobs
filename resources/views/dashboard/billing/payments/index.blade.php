<x-dashboard.layout title="Billing · Payments">
    <div class="flex flex-col gap-6">
        <div>
            <div class="text-xs uppercase tracking-[0.2em] text-slate-500">Billing</div>
            <h1 class="mt-2 text-2xl font-bold">Payments</h1>
            <p class="mt-2 text-sm text-slate-600">
                Payments recorded for your invoices.
            </p>
        </div>

        @if ($payments->isEmpty())
            <div class="pixel-outline p-6 text-sm text-slate-600">
                No payments yet.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="text-xs uppercase tracking-[0.2em] text-slate-500">
                            <th class="py-2">Payment</th>
                            <th class="py-2">Date</th>
                            <th class="py-2">Status</th>
                            <th class="py-2">Amount</th>
                            <th class="py-2">Invoice</th>
                            <th class="py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach ($payments as $payment)
                            <tr>
                                <td class="py-3 font-bold">#{{ $payment->id }}</td>
                                <td class="py-3">{{ $payment->created_at?->format('d.m.Y') ?? '—' }}</td>
                                <td class="py-3">{{ $payment->status }}</td>
                                <td class="py-3">{{ format_money_minor($payment->amount_minor, $payment->currency) }}</td>
                                <td class="py-3">
                                    @if ($payment->invoice)
                                        <a href="{{ route('frontend.billing.invoices.show', $payment->invoice) }}" class="underline">
                                            #{{ $payment->invoice->id }}
                                        </a>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="py-3 text-right">
                                    <a href="{{ route('frontend.billing.payments.show', $payment) }}"
                                       class="inline-flex pixel-outline px-3 py-1 text-xs uppercase tracking-[0.2em]">
                                        View
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
