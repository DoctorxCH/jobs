<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\LegalPage;
use Illuminate\View\View;

class LegalPageController extends Controller
{
    private function resolvePage(string $slug): ?LegalPage
    {
        $page = LegalPage::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->orderByDesc('updated_at')
            ->first();

        if (! $page) {
            $page = LegalPage::query()
                ->where('is_active', true)
                ->orderByDesc('updated_at')
                ->first();
        }

        return $page;
    }

    public function agb(): View
    {
        $page = $this->resolvePage('agb');

        return view('legal.agb', [
            'page' => $page,
        ]);
    }

    public function privacy(): View
    {
        $page = $this->resolvePage('privacy');

        return view('legal.privacy', [
            'page' => $page,
        ]);
    }
}
