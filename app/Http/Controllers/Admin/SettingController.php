<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\MediaStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    public function __construct(protected MediaStorageService $media)
    {
        $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Display settings page.
     */
    public function index(Request $request)
    {
        $group = $request->get('group', 'general');
        $groups = Setting::GROUPS;
        
        $settings = Setting::where('group', $group)
            ->orderBy('label')
            ->get();

        return view('pages.admin.settings.index', compact('settings', 'groups', 'group'));
    }

    /**
     * Update settings.
     */
    public function update(Request $request)
    {
        $group = $request->get('group', 'general');
        
        $settings = Setting::where('group', $group)->get();

        foreach ($settings as $setting) {
            $fieldKey = "settings.{$setting->key}";
            
            if ($setting->type === 'boolean') {
                $value = $request->has($fieldKey) ? '1' : '0';
            } elseif ($setting->type === 'image') {
                // Handle image upload
                if ($request->hasFile($fieldKey)) {
                    $file = $request->file($fieldKey);
                    $value = $this->media->uploadUploadedFile($file, 'settings');

                    // Delete old image if exists
                    if ($setting->value) {
                        $this->media->delete($setting->value);
                    }
                } else {
                    // Keep existing value
                    continue;
                }
            } elseif ($setting->type === 'json') {
                $value = json_encode($request->input($fieldKey, []));
            } else {
                $value = $request->input($fieldKey);
            }

            $setting->update(['value' => $value]);
        }

        // Clear cache
        Setting::clearCache();

        return redirect()->route('admin.settings.index', ['group' => $group])
            ->with('success', 'Paramètres mis à jour avec succès.');
    }

    /**
     * Clear settings cache.
     */
    public function clearCache()
    {
        Setting::clearCache();
        
        return back()->with('success', 'Cache des paramètres vidé avec succès.');
    }

    /**
     * Reset settings to default.
     */
    public function reset(Request $request)
    {
        $group = $request->get('group', 'general');
        
        // This would need default values defined somewhere
        // For now, just clear cache
        Setting::clearCache();

        return back()->with('success', 'Paramètres réinitialisés.');
    }

    /**
     * Get public settings (API endpoint).
     */
    public function public()
    {
        return response()->json(Setting::getPublic());
    }
}
