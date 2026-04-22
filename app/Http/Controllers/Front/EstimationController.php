<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Estimation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EstimationController extends Controller
{
    /**
     * Store a new property estimation.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'property_type'           => 'required|string|max:50',
            'transaction_type'        => 'required|string|max:50',
            'city'                    => 'required|string|max:100',
            'surface'                 => 'nullable|numeric|min:1|max:99999',
            'bedrooms'                => 'nullable|integer|min:0|max:50',
            'bathrooms'               => 'nullable|integer|min:0|max:30',
            'floor'                   => 'nullable|integer|min:-5|max:200',
            'construction_year'       => 'nullable|integer|min:1900|max:' . (date('Y') + 2),
            'condition'               => 'nullable|string|in:neuf,excellent,bon,a_renover',
            'has_garage'              => 'nullable|boolean',
            'garage_places'           => 'nullable|integer|min:1|max:20',
            'has_garden'              => 'nullable|boolean',
            'garden_surface'          => 'nullable|numeric|min:0|max:99999',
            'has_terrace'             => 'nullable|boolean',
            'terrace_surface'         => 'nullable|numeric|min:0|max:99999',
            'has_pool'                => 'nullable|boolean',
            'has_elevator'            => 'nullable|boolean',
            'has_parking'             => 'nullable|boolean',
            'is_furnished'            => 'nullable|boolean',
            'has_security'            => 'nullable|boolean',
            'estimated_min'           => 'nullable|numeric',
            'estimated_mid'           => 'nullable|numeric',
            'estimated_max'           => 'nullable|numeric',
            'price_per_sqm'           => 'nullable|numeric',
            'user_type'               => 'nullable|string|max:50',
            'wants_professional_help' => 'nullable|boolean',
            'is_owner'                => 'nullable|boolean',
            'timeline'                => 'nullable|string|max:50',
            'contact_name'            => 'nullable|string|max:100',
            'contact_email'           => 'nullable|email|max:150',
            'contact_phone'           => 'nullable|string|max:25',
        ]);

        $validated['ip_address'] = $request->ip();

        Estimation::create($validated);

        return response()->json(['success' => true, 'message' => 'Estimation enregistrée avec succès.']);
    }
}
