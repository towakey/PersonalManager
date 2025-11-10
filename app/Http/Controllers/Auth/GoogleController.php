<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Plugins\Google\GoogleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class GoogleController extends Controller
{
    private GoogleService $googleService;

    public function __construct(GoogleService $googleService)
    {
        $this->googleService = $googleService;
    }

    /**
     * Google認証を開始
     */
    public function redirect()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        return $this->googleService->getAuthRedirect();
    }

    /**
     * Google認証コールバック
     */
    public function callback(Request $request)
    {
        try {
            $connectedAccount = $this->googleService->handleAuthCallback($request);
            
            Log::info('Google account connected successfully', [
                'user_id' => Auth::id(),
                'service' => 'google',
            ]);

            return redirect()->route('dashboard')
                ->with('success', __('Google account connected successfully!'));

        } catch (\Exception $e) {
            Log::error('Google authentication failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('dashboard')
                ->with('error', __('Failed to connect Google account: :message', ['message' => $e->getMessage()]));
        }
    }
}
