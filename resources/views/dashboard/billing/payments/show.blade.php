<x-dashboard.layout title="Billing · Payment">
    <div class="flex flex-col gap-6">
        <div>
            <a href="{{ route('frontend.billing.payments.index') }}"
               class="text-xs uppercase tracking-[0.2em] text-slate-500">← Back to payments</a>
            <h1 class="mt-2 text-2xl font-bold">Payment #{{ $payment->id }}</h1>
            <p class="mt-2 text-sm text-slate-600">
                Created {{ $payment->created_at?->format('d.m.Y H:i') ?? '—' }} · Status {{ $payment->status }}
            </p>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <div class="pixel-outline p-4 text-sm text-slate-600">
                <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">Amount</div>
                <div class="mt-2 text-lg font-bold">{{ format_money_minor($payment->amount_minor, $payment->currency) }}</div>
            </div>
            <div class="pixel-outline p-4 text-sm text-slate-600">
                <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">Method</div>
                <div class="mt-2">{{ $payment->method }}</div>
            </div>
            <div class="pixel-outline p-4 text-sm text-slate-600">
                <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">Bank reference</div>
                <div class="mt-2">{{ $payment->bank_reference ?? '—' }}</div>
            </div>
        </div>

        <div class="pixel-outline p-6 text-sm text-slate-600">
            <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">Invoice</div>
            @if ($payment->invoice)
                <a href="{{ route('frontend.billing.invoices.show', $payment->invoice) }}" class="underline">
                    View invoice #{{ $payment->invoice->id }}
                </a>
            @else
                <div>Invoice not available.</div>
            @endif
        </div>

        <div class="pixel-outline p-6">
            <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">Status history</div>
            <div class="mt-4 space-y-2 text-sm text-slate-600">
                @forelse ($payment->statusHistory as $history)
                    <div class="flex flex-col gap-1">
                        <div class="font-bold">{{ $history->to_status }}</div>
                        <div>{{ $history->changed_at?->format('d.m.Y H:i') ?? '—' }}</div>
                        @if ($history->note)
                            <div>{{ $history->note }}</div>
                        @endif
                    </div>
                @empty
                    <div>No status history recorded.</div>
                @endforelse
            </div>
        </div>
    </div>
</x-dashboard.layout>
