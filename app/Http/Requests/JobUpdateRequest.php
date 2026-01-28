<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JobUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canCompanyManageJobs() ?? false;
    }

    public function rules(): array
    {
        // same rules as store (keep consistent)
        return (new JobStoreRequest())->rules();
    }
}
