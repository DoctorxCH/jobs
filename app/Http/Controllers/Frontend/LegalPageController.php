<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\LegalPage;
use Illuminate\View\View;

class LegalPageController extends Controller
{
    public function agb(): View
    {
        $page = LegalPage::query()
            ->where('slug', 'agb')
            ->where('is_active', true)
            ->orderByDesc('updated_at')
            ->first();

        if (! $page) {
            $page = LegalPage::query()
                ->where('is_active', true)
                ->orderByDesc('updated_at')
                ->first();
        }

        return view('legal.agb', [
            'page' => $page,
        ]);
    }
}
