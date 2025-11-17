<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Plugins\Twitter\TwitterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TwitterController extends Controller
{
    private TwitterService $twitterService;

    public function __construct(TwitterService $twitterService)
    {
        $this->twitterService = $twitterService;
    }

    /**
     * Twitter認証を開始
     */
    public function redirect()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        try {
            return $this->twitterService->getAuthRedirect();
        } catch (\Exception $e) {
            return redirect()->route('plugins')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Twitter認証コールバック
     */
    public function callback(Request $request)
    {
        try {
            $connectedAccount = $this->twitterService->handleAuthCallback($request);
            
            Log::info('Twitter account connected successfully', [
                'user_id' => Auth::id(),
                'service' => 'twitter',
            ]);

            return redirect()->route('settings')
                ->with('success', __('Twitter account connected successfully!'));

        } catch (\Exception $e) {
            Log::error('Twitter authentication failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('settings')
                ->with('error', __('Failed to connect Twitter account: :message', ['message' => $e->getMessage()]));
        }
    }
}
