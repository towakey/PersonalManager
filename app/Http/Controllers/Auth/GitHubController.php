<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Plugins\GitHub\GitHubService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class GitHubController extends Controller
{
    private GitHubService $githubService;

    public function __construct(GitHubService $githubService)
    {
        $this->githubService = $githubService;
    }

    /**
     * GitHub認証を開始
     */
    public function redirect()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        return $this->githubService->getAuthRedirect();
    }

    /**
     * GitHub認証コールバック
     */
    public function callback(Request $request)
    {
        try {
            $connectedAccount = $this->githubService->handleAuthCallback($request);
            
            Log::info('GitHub account connected successfully', [
                'user_id' => Auth::id(),
                'service' => 'github',
            ]);

            return redirect()->route('settings')
                ->with('success', __('GitHub account connected successfully!'));

        } catch (\Exception $e) {
            Log::error('GitHub authentication failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('settings')
                ->with('error', __('Failed to connect GitHub account: :message', ['message' => $e->getMessage()]));
        }
    }
}
