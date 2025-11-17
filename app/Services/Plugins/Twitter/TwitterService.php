<?php

namespace App\Services\Plugins\Twitter;

use App\Services\Plugins\Contracts\ServicePluginInterface;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\ConnectedAccount;
use Exception;

class TwitterService implements ServicePluginInterface
{
    private ?string $clientId;
    private ?string $clientSecret;
    private ?string $redirectUri;

    public function __construct()
    {
        $this->clientId = config('services.twitter.client_id');
        $this->clientSecret = config('services.twitter.client_secret');
        $this->redirectUri = config('services.twitter.redirect');
    }

    /**
     * サービスの一意な識別子を返す
     */
    public static function getIdentifier(): string
    {
        return 'twitter';
    }

    /**
     * サービスの表示名に対応する言語キーを返す
     */
    public static function getDisplayName(): string
    {
        return 'plugins.twitter.name';
    }

    /**
     * サービスの説明に対応する言語キーを返す
     */
    public static function getDescription(): string
    {
        return 'plugins.twitter.description';
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
        return [];
    }

    /**
     * 認証を開始するためのリダイレクトレスポンスを生成する
     */
    public function getAuthRedirect(): RedirectResponse
    {
        // 設定値が未設定の場合はエラー
        if (empty($this->clientId) || empty($this->clientSecret) || empty($this->redirectUri)) {
            throw new Exception('Twitter OAuth credentials are not configured. Please set TWITTER_CLIENT_ID and TWITTER_CLIENT_SECRET in your .env file.');
        }

        $state = Str::random(40);
        $codeVerifier = Str::random(128);
        $codeChallenge = hash('sha256', $codeVerifier, true);
        $codeChallenge = base64_encode($codeChallenge);
        $codeChallenge = strtr($codeChallenge, '+/', '-_');
        $codeChallenge = rtrim($codeChallenge, '=');

        session([
            'twitter_oauth_state' => $state,
            'twitter_code_verifier' => $codeVerifier,
        ]);

        $params = [
            'response_type' => 'code',
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'scope' => 'tweet.read users.read offline.access',
            'state' => $state,
            'code_challenge' => $codeChallenge,
            'code_challenge_method' => 'S256',
        ];

        $url = 'https://twitter.com/i/oauth2/authorize?' . http_build_query($params);

        return redirect()->away($url);
    }

    /**
     * 認証コールバックを処理し、認証情報をConnectedAccountモデルとして返す
     */
    public function handleAuthCallback(Request $request): ConnectedAccount
    {
        $state = $request->get('state');
        $code = $request->get('code');
        $codeVerifier = session('twitter_code_verifier');

        if (!$state || $state !== session('twitter_oauth_state')) {
            throw new Exception('Invalid state parameter');
        }

        if (!$codeVerifier) {
            throw new Exception('Code verifier not found in session');
        }

        // アクセストークンを取得
        $response = Http::asForm()->post('https://api.twitter.com/2/oauth2/token', [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'code' => $code,
            'redirect_uri' => $this->redirectUri,
            'grant_type' => 'authorization_code',
            'code_verifier' => $codeVerifier,
        ]);

        $tokenData = $response->json();

        if (!isset($tokenData['access_token'])) {
            throw new Exception('Failed to obtain access token: ' . ($tokenData['error_description'] ?? 'Unknown error'));
        }

        // ユーザー情報を取得
        $userResponse = Http::withHeaders([
            'Authorization' => 'Bearer ' . $tokenData['access_token'],
        ])->get('https://api.twitter.com/2/users/me', [
            'user.fields' => 'id,name,username,profile_image_url',
        ]);

        if (!$userResponse->successful()) {
            throw new Exception('Failed to obtain user information');
        }

        $twitterUser = $userResponse->json();

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
        $refreshToken = $account->getRefreshToken();

        return new class($accessToken, $refreshToken, $this->clientId, $this->clientSecret, $this->redirectUri) {
            private string $accessToken;
            private ?string $refreshToken;
            private string $clientId;
            private string $clientSecret;
            private string $redirectUri;

            public function __construct(string $accessToken, ?string $refreshToken, string $clientId, string $clientSecret, string $redirectUri)
            {
                $this->accessToken = $accessToken;
                $this->refreshToken = $refreshToken;
                $this->clientId = $clientId;
                $this->clientSecret = $clientSecret;
                $this->redirectUri = $redirectUri;
            }

            private function refreshAccessToken(): string
            {
                if (!$this->refreshToken) {
                    throw new Exception('No refresh token available');
                }

                $response = Http::asForm()->post('https://api.twitter.com/2/oauth2/token', [
                    'refresh_token' => $this->refreshToken,
                    'grant_type' => 'refresh_token',
                ]);

                $tokenData = $response->json();

                if (!isset($tokenData['access_token'])) {
                    throw new Exception('Failed to refresh access token');
                }

                $this->accessToken = $tokenData['access_token'];
                
                // Update the account with new token
                $account = Auth::user()->connectedAccounts()
                    ->where('service_name', 'twitter')
                    ->first();
                
                if ($account) {
                    $account->setAccessToken($this->accessToken);
                    $account->expires_at = isset($tokenData['expires_in']) 
                        ? now()->addSeconds($tokenData['expires_in']) 
                        : null;
                    $account->save();
                }

                return $this->accessToken;
            }

            private function makeRequest(string $method, string $url, array $options = [])
            {
                $headers = $options['headers'] ?? [];
                $headers['Authorization'] = 'Bearer ' . $this->accessToken;

                $response = Http::withHeaders($headers)->{$method}($url, $options['data'] ?? []);

                // If token expired, try to refresh and retry once
                if ($response->status() === 401 && $this->refreshToken) {
                    $this->refreshAccessToken();
                    $headers['Authorization'] = 'Bearer ' . $this->accessToken;
                    $response = Http::withHeaders($headers)->{$method}($url, $options['data'] ?? []);
                }

                return $response;
            }

            public function getUser()
            {
                $response = $this->makeRequest('GET', 'https://api.twitter.com/2/users/me', [
                    'query' => ['user.fields' => 'id,name,username,profile_image_url,created_at,description'],
                ]);

                return $response->json();
            }

            public function getTweets(array $params = [])
            {
                $defaultParams = [
                    'max_results' => 10,
                    'tweet_fields' => 'created_at,public_metrics',
                ];
                $params = array_merge($defaultParams, $params);

                $user = $this->getUser();
                $userId = $user['data']['id'] ?? null;

                if (!$userId) {
                    throw new Exception('Unable to get user ID');
                }

                $response = $this->makeRequest('GET', "https://api.twitter.com/2/users/{$userId}/tweets", [
                    'query' => $params,
                ]);

                return $response->json();
            }
        };
    }
}
