<x-dashboard.layout title="{{ __('main.billing_payment_title') }}">
    <div class="flex flex-col gap-6">
        <div>
            <a href="{{ route('frontend.billing.payments.index') }}"
               class="text-xs uppercase tracking-[0.2em] text-slate-500">← {{ __('main.back_to_payments') }}</a>
            <h1 class="mt-2 text-2xl font-bold">{{ __('main.payment_number', ['id' => $payment->id]) }}</h1>
            <p class="mt-2 text-sm text-slate-600">
                {{ __('main.payment_created_status', ['date' => $payment->created_at?->format('d.m.Y H:i') ?? '—', 'status' => $payment->status]) }}
            </p>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <div class="pixel-outline p-4 text-sm text-slate-600">
                <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">{{ __('main.amount') }}</div>
                <div class="mt-2 text-lg font-bold">{{ format_money_minor($payment->amount_minor, $payment->currency) }}</div>
            </div>
            <div class="pixel-outline p-4 text-sm text-slate-600">
                <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">{{ __('main.method') }}</div>
                <div class="mt-2">{{ $payment->method }}</div>
            </div>
            <div class="pixel-outline p-4 text-sm text-slate-600">
                <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">{{ __('main.bank_reference') }}</div>
                <div class="mt-2">{{ $payment->bank_reference ?? '—' }}</div>
            </div>
        </div>

        <div class="pixel-outline p-6 text-sm text-slate-600">
            <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">{{ __('main.invoice') }}</div>
            @if ($payment->invoice)
                <a href="{{ route('frontend.billing.invoices.show', $payment->invoice) }}" class="underline">
                    {{ __('main.view_invoice', ['id' => $payment->invoice->id]) }}
                </a>
            @else
                <div>{{ __('main.invoice_not_available') }}</div>
            @endif
        </div>

        <div class="pixel-outline p-6">
            <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">{{ __('main.status_history') }}</div>
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
                    <div>{{ __('main.no_status_history') }}</div>
                @endforelse
            </div>
        </div>
    </div>
</x-dashboard.layout>
