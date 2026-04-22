<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class SubscriptionPlanController extends Controller
{
    public function index()
    {
        $plans = SubscriptionPlan::orderBy('priority_level')->get();
        return view('pages.admin.plans.index', compact('plans'));
    }

    public function create()
    {
        return view('pages.admin.plans.create');
    }

    public function store(Request $request)
    {
        if ($request->filled('features') && is_string($request->features)) {
            $request->merge([
                'features' => array_filter(array_map('trim', explode("\n", $request->features)))
            ]);
        }

        $validated = $request->validate($this->rules());
        $validated['slug'] = Str::slug($validated['name']);
        
        SubscriptionPlan::create($validated);
        Cache::forget('subscription_plans_all');

        return redirect()->route('admin.plans.index')->with('success', 'Plan créé avec succès.');
    }

    public function edit(SubscriptionPlan $plan)
    {
        return view('pages.admin.plans.edit', compact('plan'));
    }

    public function update(Request $request, SubscriptionPlan $plan)
    {
        if ($request->has('features') && is_string($request->features)) {
            $request->merge([
                'features' => array_filter(array_map('trim', explode("\n", $request->features)))
            ]);
        }

        $validated = $request->validate($this->rules($plan->id));
        if ($plan->slug !== 'gratuit') {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $plan->update($validated);
        Cache::forget('subscription_plans_all');

        return redirect()->route('admin.plans.index')->with('success', 'Plan mis à jour avec succès.');
    }

    public function destroy(SubscriptionPlan $plan)
    {
        if ($plan->slug === 'gratuit') {
            return back()->with('error', 'Le plan gratuit ne peut pas être supprimé.');
        }

        $plan->delete();
        Cache::forget('subscription_plans_all');

        return redirect()->route('admin.plans.index')->with('success', 'Plan supprimé avec succès.');
    }

    protected function rules($id = null)
    {
        return [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'nullable|integer|min:1',
            'priority_level' => 'required|integer|min:0',
            'listing_duration_days' => 'required|integer|min:1',
            'sponsored_listing_duration_days' => 'required|integer|min:0',
            'can_create_sponsored_listing' => 'boolean',
            'max_images_particulier' => 'required|integer|min:1',
            'max_images_agent' => 'required|integer|min:1',
            'max_ads_particulier' => 'required|integer|min:1',
            'max_ads_agent' => 'required|integer|min:1',
            'is_active' => 'boolean',
            'features' => 'nullable|array',
            'features.*' => 'string|max:255',
        ];
    }
}
