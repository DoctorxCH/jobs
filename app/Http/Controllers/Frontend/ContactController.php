<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ContactForm;
use App\Models\ContactSetting;
use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function create(): View
    {
        $settings = ContactSetting::query()->first();
        $form = null;

        if ($settings?->default_form_id) {
            $form = ContactForm::query()->where('active', true)->find($settings->default_form_id);
        }

        return view('dashboard.contact', compact('form'));
    }

    public function store(Request $request): RedirectResponse
    {
        $settings = ContactSetting::query()->first();
        $form = $settings?->default_form_id
            ? ContactForm::query()->find($settings->default_form_id)
            : null;

        $rules = [];
        $payload = [];

        if ($form && is_array($form->fields) && count($form->fields) > 0) {
            foreach ($form->fields as $field) {
                $key = $field['key'] ?? null;
                $inputKey = $key ? Str::slug($key, '_') : null;
                if (! $inputKey) {
                    continue;
                }

                $type = $field['type'] ?? 'text';
                $isRequired = (bool) ($field['required'] ?? false);

                $fieldRules = [];
                $fieldRules[] = $isRequired ? 'required' : 'nullable';
                $fieldRules[] = 'string';

                if ($type === 'email') {
                    $fieldRules[] = 'email';
                    $fieldRules[] = 'max:150';
                } elseif ($type === 'textarea') {
                    $fieldRules[] = 'max:5000';
                } else {
                    $fieldRules[] = 'max:150';
                }

                $rules[$inputKey] = $fieldRules;
            }
        } else {
            $rules = [
                'name' => ['required', 'string', 'max:150'],
                'email' => ['required', 'email', 'max:150'],
                'subject' => ['required', 'string', 'max:200'],
                'message' => ['required', 'string', 'max:5000'],
            ];
        }

        $data = $request->validate($rules);

        if (!Schema::hasTable('contact_requests')) {
            return back()->withErrors(['message' => __('main.contact_unavailable')]);
        }

        $user = $request->user();
        $companyId = null;

        if ($user && method_exists($user, 'effectiveCompanyId')) {
            $companyId = $user->effectiveCompanyId();
        }

        if (! $companyId && $user) {
            $companyId = Company::query()
                ->where('owner_user_id', $user->id)
                ->value('id');
        }

        $statusOptions = $settings?->status_options ?? [];
        $defaultStatus = is_array($statusOptions) && count($statusOptions) > 0
            ? (string) $statusOptions[0]
            : 'new';

        $name = $data['name'] ?? ($data['full_name'] ?? null);
        $email = $data['email'] ?? null;
        $subject = $data['subject'] ?? null;
        $message = $data['message'] ?? null;

        if ($form && is_array($form->fields) && count($form->fields) > 0) {
            foreach ($form->fields as $field) {
                $key = $field['key'] ?? null;
                $inputKey = $key ? Str::slug($key, '_') : null;
                if (! $key || ! $inputKey) {
                    continue;
                }

                $payload[$key] = $data[$inputKey] ?? null;

                $label = (string) ($field['label'] ?? $key);
                $labelKey = Str::slug($label, '_');
                $type = $field['type'] ?? 'text';

                if (! $name && (str_contains($labelKey, 'name') || str_contains($labelKey, 'meno'))) {
                    $name = $data[$inputKey] ?? $name;
                }
                if (! $email && ($type === 'email' || str_contains($labelKey, 'email'))) {
                    $email = $data[$inputKey] ?? $email;
                }
                if (! $subject && (str_contains($labelKey, 'subject') || str_contains($labelKey, 'predmet'))) {
                    $subject = $data[$inputKey] ?? $subject;
                }
                if (! $message && ($type === 'textarea' || str_contains($labelKey, 'message') || str_contains($labelKey, 'sprava'))) {
                    $message = $data[$inputKey] ?? $message;
                }
            }
        }

        DB::table('contact_requests')->insert([
            'company_id' => $companyId,
            'user_id' => $user?->id,
            'form_id' => $settings?->default_form_id,
            'name' => $name ?? '',
            'email' => $email ?? '',
            'subject' => $subject ?? '',
            'message' => $message ?? '',
            'payload' => $payload ? json_encode($payload) : null,
            'status' => $defaultStatus,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('status', __('main.contact_sent'));
    }
}
