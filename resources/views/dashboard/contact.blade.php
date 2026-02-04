<x-dashboard.layout title="{{ __('main.contact') }}">
    <div class="flex flex-col gap-6">
        <div>
            <div class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ __('main.contact') }}</div>
            <h1 class="mt-2 text-2xl font-bold">{{ __('main.contact_title') }}</h1>
            <p class="mt-2 text-sm text-slate-600">
                {{ __('main.contact_subtitle') }}
            </p>
        </div>

        <form method="POST" action="{{ route('frontend.contact.store') }}" class="pixel-outline p-6 flex flex-col gap-4 max-w-2xl">
            @csrf

            @if ($form && is_array($form->fields) && count($form->fields) > 0)
                <div class="grid gap-4 md:grid-cols-2">
                @foreach ($form->fields as $field)
                    @php
                        $key = $field['key'] ?? null;
                        $inputKey = $key ? \Illuminate\Support\Str::slug($key, '_') : null;
                        $label = $field['label'] ?? $key;
                        $type = $field['type'] ?? 'text';
                        $required = (bool) ($field['required'] ?? false);
                        $placeholder = $field['placeholder'] ?? null;
                        $options = $field['options'] ?? [];
                        $width = $field['width'] ?? 'full';
                        $widthClass = $width === 'half'
                            ? 'md:col-span-1'
                            : ($width === 'third' ? 'md:col-span-1 lg:col-span-1' : 'md:col-span-2');
                    @endphp

                    @if ($inputKey)
                        <div class="{{ $widthClass }}">
                            <label class="text-[10px] uppercase tracking-[0.28em] text-slate-500">{{ $label }}</label>

                            @if ($type === 'textarea')
                                <textarea
                                    name="{{ $inputKey }}"
                                    rows="6"
                                    class="mt-2 pixel-input w-full px-4 py-3 text-sm text-slate-900 outline-none"
                                    @if($required) required @endif
                                    @if($placeholder) placeholder="{{ $placeholder }}" @endif
                                >{{ old($inputKey) }}</textarea>
                            @elseif ($type === 'select')
                                <select
                                    name="{{ $inputKey }}"
                                    class="mt-2 pixel-input w-full px-4 py-3 text-sm text-slate-900 outline-none"
                                    @if($required) required @endif
                                >
                                    @foreach ($options as $option)
                                        <option value="{{ $option }}" @selected(old($inputKey) == $option)>{{ $option }}</option>
                                    @endforeach
                                </select>
                            @else
                                <input
                                    name="{{ $inputKey }}"
                                    type="{{ $type }}"
                                    class="mt-2 pixel-input w-full px-4 py-3 text-sm text-slate-900 outline-none"
                                    value="{{ old($inputKey) }}"
                                    @if($required) required @endif
                                    @if($placeholder) placeholder="{{ $placeholder }}" @endif
                                />
                            @endif

                            @error($inputKey)
                                <div class="mt-2 text-xs text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                    @endif
                @endforeach
                </div>
            @else
                <div>
                    <label class="text-[10px] uppercase tracking-[0.28em] text-slate-500">{{ __('main.name') }}</label>
                    <input
                        name="name"
                        class="mt-2 pixel-input w-full px-4 py-3 text-sm text-slate-900 outline-none"
                        value="{{ old('name', auth()->user()->name ?? '') }}"
                        required
                    />
                    @error('name')
                        <div class="mt-2 text-xs text-red-600">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="text-[10px] uppercase tracking-[0.28em] text-slate-500">{{ __('main.email') }}</label>
                    <input
                        name="email"
                        type="email"
                        class="mt-2 pixel-input w-full px-4 py-3 text-sm text-slate-900 outline-none"
                        value="{{ old('email', auth()->user()->email ?? '') }}"
                        required
                    />
                    @error('email')
                        <div class="mt-2 text-xs text-red-600">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="text-[10px] uppercase tracking-[0.28em] text-slate-500">{{ __('main.subject') }}</label>
                    <input
                        name="subject"
                        class="mt-2 pixel-input w-full px-4 py-3 text-sm text-slate-900 outline-none"
                        value="{{ old('subject') }}"
                        required
                    />
                    @error('subject')
                        <div class="mt-2 text-xs text-red-600">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="text-[10px] uppercase tracking-[0.28em] text-slate-500">{{ __('main.message') }}</label>
                    <textarea
                        name="message"
                        rows="6"
                        class="mt-2 pixel-input w-full px-4 py-3 text-sm text-slate-900 outline-none"
                        required
                    >{{ old('message') }}</textarea>
                    @error('message')
                        <div class="mt-2 text-xs text-red-600">{{ $message }}</div>
                    @enderror
                </div>
            @endif

            <button type="submit" class="inline-flex pixel-outline px-6 py-3 text-xs uppercase tracking-[0.2em]">
                {{ __('main.send_message') }}
            </button>
        </form>
    </div>
</x-dashboard.layout>
