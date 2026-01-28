<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Services\CompanyLookup\CompanyLookupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CompanyLookupController extends Controller
{
    public function __invoke(Request $request, CompanyLookupService $lookup)
    {
        $v = Validator::make($request->all(), [
            'ico' => ['required', 'string', 'regex:/^\d{8}$/'],
            'country' => ['nullable', 'string', 'size:2'],
        ]);

        if ($v->fails()) {
            return response()->json(['ok' => false, 'errors' => $v->errors()], 422);
        }

        $ico = (string) $request->input('ico');

        // early uniqueness check
        if (Company::where('ico', $ico)->exists()) {
            return response()->json([
                'ok' => false,
                'code' => 'ico_already_registered',
                'message' => 'This ICO is already registered.',
            ], 409);
        }

        $country = strtoupper($request->input('country', 'SK'));

        $data = $lookup->lookup($country, $ico);

        if (! $data) {
            return response()->json(['ok' => false, 'message' => 'Not found'], 404);
        }

        return response()->json(['ok' => true, 'data' => $data]);
    }
}
