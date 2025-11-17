<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\OauthSetting;

class AdminOauthSettingsController extends Controller
{
    /**
     * Show the OAuth settings edit page.
     */
    public function edit()
    {
        $user = Auth::user();

        if (!$user || !$user->is_admin) {
            abort(403);
        }

        $github = OauthSetting::firstOrNew(['provider' => 'github']);
        $google = OauthSetting::firstOrNew(['provider' => 'google']);
        $twitter = OauthSetting::firstOrNew(['provider' => 'twitter']);

        return view('admin.oauth-settings', [
            'github' => $github,
            'google' => $google,
            'twitter' => $twitter,
        ]);
    }

    /**
     * Update OAuth settings.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        if (!$user || !$user->is_admin) {
            abort(403);
        }

        $validated = $request->validate([
            'github.client_id' => ['nullable', 'string', 'max:255'],
            'github.client_secret' => ['nullable', 'string', 'max:255'],
            'github.redirect' => ['nullable', 'string', 'max:255'],
            'google.client_id' => ['nullable', 'string', 'max:255'],
            'google.client_secret' => ['nullable', 'string', 'max:255'],
            'google.redirect' => ['nullable', 'string', 'max:255'],
            'twitter.client_id' => ['nullable', 'string', 'max:255'],
            'twitter.client_secret' => ['nullable', 'string', 'max:255'],
            'twitter.redirect' => ['nullable', 'string', 'max:255'],
        ]);

        foreach (['github', 'google', 'twitter'] as $provider) {
            $data = $validated[$provider] ?? [];

            $setting = OauthSetting::firstOrNew(['provider' => $provider]);
            $setting->client_id = $data['client_id'] ?? null;
            $setting->client_secret = $data['client_secret'] ?? null;
            $setting->redirect = $data['redirect'] ?? null;
            $setting->save();
        }

        return redirect()->route('admin.settings.oauth.edit')
            ->with('success', __('settings.oauth.updated'));
    }
}
