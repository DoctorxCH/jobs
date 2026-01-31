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
            ->first();

        return view('legal.agb', [
            'page' => $page,
        ]);
    }
}
