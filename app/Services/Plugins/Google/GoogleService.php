<?php

namespace App\Services\Plugins\Google;

use App\Services\Plugins\Contracts\ServicePluginInterface;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\ConnectedAccount;
use Exception;

class GoogleService implements ServicePluginInterface
{
    private string $clientId;
    private string $clientSecret;
    private string $redirectUri;

    public function __construct()
    {
        $this->clientId = config('services.google.client_id');
        $this->clientSecret = config('services.google.client_secret');
        $this->redirectUri = config('services.google.redirect_uri');
    }

    /**
     * サービスの一意な識別子を返す
     */
    public static function getIdentifier(): string
    {
        return 'google';
    }

    /**
     * サービスの表示名に対応する言語キーを返す
     */
    public static function getDisplayName(): string
    {
        return 'plugins.google.name';
    }

    /**
     * サービスの説明に対応する言語キーを返す
     */
    public static function getDescription(): string
    {
        return 'plugins.google.description';
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
            \App\Services\Plugins\Google\Widgets\Gmail::class,
            \App\Services\Plugins\Google\Widgets\Calendar::class,
        ];
    }

    /**
     * 認証を開始するためのリダイレクトレスポンスを生成する
     */
    public function getAuthRedirect(): RedirectResponse
    {
        $state = str_random(40);
        session(['google_oauth_state' => $state]);

        $params = [
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'scope' => 'https://www.googleapis.com/auth/gmail.readonly https://www.googleapis.com/auth/calendar.readonly email',
            'response_type' => 'code',
            'access_type' => 'offline',
            'state' => $state,
        ];

        $url = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);

        return redirect()->away($url);
    }

    /**
     * 認証コールバックを処理し、認証情報をConnectedAccountモデルとして返す
     */
    public function handleAuthCallback(Request $request): ConnectedAccount
    {
        $state = $request->get('state');
        $code = $request->get('code');

        if (!$state || $state !== session('google_oauth_state')) {
            throw new Exception('Invalid state parameter');
        }

        // アクセストークンを取得
        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'code' => $code,
            'redirect_uri' => $this->redirectUri,
            'grant_type' => 'authorization_code',
        ]);

        $tokenData = $response->json();

        if (!isset($tokenData['access_token'])) {
            throw new Exception('Failed to obtain access token');
        }

        // ユーザー情報を取得
        $userResponse = Http::withHeaders([
            'Authorization' => 'Bearer ' . $tokenData['access_token'],
        ])->get('https://www.googleapis.com/oauth2/v2/userinfo');

        if (!$userResponse->successful()) {
            throw new Exception('Failed to obtain user information');
        }

        $googleUser = $userResponse->json();

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

                $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
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
                    ->where('service_name', 'google')
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

            public function getGmailMessages(array $params = [])
            {
                $defaultParams = [
                    'maxResults' => 10,
                    'q' => 'is:unread',
                ];
                $params = array_merge($defaultParams, $params);

                $response = $this->makeRequest('GET', 'https://www.googleapis.com/gmail/v1/users/me/messages', [
                    'query' => $params,
                ]);

                return $response->json();
            }

            public function getGmailMessage(string $messageId)
            {
                $response = $this->makeRequest('GET', "https://www.googleapis.com/gmail/v1/users/me/messages/{$messageId}", [
                    'query' => ['format' => 'metadata', 'metadataHeaders' => ['Subject', 'From', 'Date']],
                ]);

                return $response->json();
            }

            public function getCalendarEvents(array $params = [])
            {
                $defaultParams = [
                    'timeMin' => now()->startOfDay()->format(\DateTime::ATOM),
                    'timeMax' => now()->addDays(7)->endOfDay()->format(\DateTime::ATOM),
                    'maxResults' => 10,
                    'orderBy' => 'startTime',
                    'singleEvents' => 'true',
                ];
                $params = array_merge($defaultParams, $params);

                $response = $this->makeRequest('GET', 'https://www.googleapis.com/calendar/v3/calendars/primary/events', [
                    'query' => $params,
                ]);

                return $response->json();
            }
        };
    }
}
