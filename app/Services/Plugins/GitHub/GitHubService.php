<?php

namespace App\Services\Plugins\GitHub;

use App\Services\Plugins\Contracts\ServicePluginInterface;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\ConnectedAccount;
use Exception;

class GitHubService implements ServicePluginInterface
{
    private string $clientId;
    private string $clientSecret;
    private string $redirectUri;

    public function __construct()
    {
        $this->clientId = config('services.github.client_id');
        $this->clientSecret = config('services.github.client_secret');
        $this->redirectUri = config('services.github.redirect_uri');
    }

    /**
     * サービスの一意な識別子を返す
     */
    public static function getIdentifier(): string
    {
        return 'github';
    }

    /**
     * サービスの表示名に対応する言語キーを返す
     */
    public static function getDisplayName(): string
    {
        return 'plugins.github.name';
    }

    /**
     * サービスの説明に対応する言語キーを返す
     */
    public static function getDescription(): string
    {
        return 'plugins.github.description';
    }

    /**
     * このサービスが依存する他のサービスの識別子リストを返す
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * このサービスが提供するウィジェットのLivewireコンポーネントクラス名リストを返す
     */
    public static function getAvailableWidgets(): array
    {
        return [
            \App\Services\Plugins\GitHub\Widgets\Notifications::class,
            \App\Services\Plugins\GitHub\Widgets\Issues::class,
        ];
    }

    /**
     * 認証を開始するためのリダイレクトレスポンスを生成する
     */
    public function getAuthRedirect(): RedirectResponse
    {
        $state = str_random(40);
        session(['github_oauth_state' => $state]);

        $params = [
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'scope' => 'user:email repo notifications read:org',
            'state' => $state,
        ];

        $url = 'https://github.com/login/oauth/authorize?' . http_build_query($params);

        return redirect()->away($url);
    }

    /**
     * 認証コールバックを処理し、認証情報をConnectedAccountモデルとして返す
     */
    public function handleAuthCallback(Request $request): ConnectedAccount
    {
        $state = $request->get('state');
        $code = $request->get('code');

        if (!$state || $state !== session('github_oauth_state')) {
            throw new Exception('Invalid state parameter');
        }

        // アクセストークンを取得
        $response = Http::asForm()->post('https://github.com/login/oauth/access_token', [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'code' => $code,
            'redirect_uri' => $this->redirectUri,
        ]);

        parse_str($response->body(), $tokenData);

        if (!isset($tokenData['access_token'])) {
            throw new Exception('Failed to obtain access token');
        }

        // ユーザー情報を取得
        $userResponse = Http::withHeaders([
            'Authorization' => 'token ' . $tokenData['access_token'],
        ])->get('https://api.github.com/user');

        if (!$userResponse->successful()) {
            throw new Exception('Failed to obtain user information');
        }

        $githubUser = $userResponse->json();

        // 既存の接続を確認
        $existingAccount = ConnectedAccount::where('user_id', Auth::id())
            ->where('service_name', $this->getIdentifier())
            ->first();

        if ($existingAccount) {
            // 既存の接続を更新
            $existingAccount->setAccessToken($tokenData['access_token']);
            $existingAccount->setRefreshToken($tokenData['refresh_token'] ?? null);
            $existingAccount->expires_at = isset($tokenData['expires_in']) 
                ? now()->addSeconds($tokenData['expires_in']) 
                : null;
            $existingAccount->save();
            return $existingAccount;
        }

        // 新しい接続を作成
        return ConnectedAccount::create([
            'user_id' => Auth::id(),
            'service_name' => $this->getIdentifier(),
            'access_token' => encrypt($tokenData['access_token']),
            'refresh_token' => isset($tokenData['refresh_token']) ? encrypt($tokenData['refresh_token']) : null,
            'expires_at' => isset($tokenData['expires_in']) ? now()->addSeconds($tokenData['expires_in']) : null,
        ]);
    }

    /**
     * 指定されたアカウントのAPIクライアントを生成して返す
     */
    public function getApiClient(ConnectedAccount $account)
    {
        $accessToken = $account->getAccessToken();

        return new class($accessToken) {
            private string $accessToken;

            public function __construct(string $accessToken)
            {
                $this->accessToken = $accessToken;
            }

            public function getNotifications(array $params = [])
            {
                $defaultParams = [
                    'all' => 'false',
                    'participating' => 'false',
                ];
                $params = array_merge($defaultParams, $params);

                $response = Http::withHeaders([
                    'Authorization' => 'token ' . $this->accessToken,
                    'Accept' => 'application/vnd.github.v3+json',
                ])->get('https://api.github.com/notifications', $params);

                return $response->json();
            }

            public function getIssues(array $params = [])
            {
                $defaultParams = [
                    'state' => 'open',
                    'sort' => 'created',
                    'direction' => 'desc',
                ];
                $params = array_merge($defaultParams, $params);

                $response = Http::withHeaders([
                    'Authorization' => 'token ' . $this->accessToken,
                    'Accept' => 'application/vnd.github.v3+json',
                ])->get('https://api.github.com/user/issues', $params);

                return $response->json();
            }

            public function getUser()
            {
                $response = Http::withHeaders([
                    'Authorization' => 'token ' . $this->accessToken,
                    'Accept' => 'application/vnd.github.v3+json',
                ])->get('https://api.github.com/user');

                return $response->json();
            }
        };
    }
}
