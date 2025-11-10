<?php

namespace App\Services\Plugins\Google\Widgets;

use App\Http\Livewire\Widgets\BaseWidget;
use App\Services\Plugins\Contracts\ServicePluginInterface;
use App\Services\Plugins\Google\GoogleService;
use App\Models\ConnectedAccount;
use App\Models\Widget;
use Illuminate\Support\Facades\Auth;
use Exception;

class Gmail extends BaseWidget
{
    public array $messages = [];
    public bool $loading = false;
    public string $error = '';

    /**
     * ウィジェットの識別子
     */
    public static function getIdentifier(): string
    {
        return 'google-gmail';
    }

    /**
     * ウィジェットの表示名に対応する言語キーを返す
     */
    public static function getDisplayName(): string
    {
        return 'widgets.google_gmail.name';
    }

    /**
     * このウィジェットが属するサービスのServiceクラス名
     */
    public static function getServiceClass(): string
    {
        return GoogleService::class;
    }

    /**
     * このウィジェットが依存するサービスの識別子リストを返す
     */
    public static function getDependencies(): array
    {
        return ['google'];
    }

    /**
     * このウィジェットがデータ公開機能に対応しているかを返す
     */
    public static function isSharable(): bool
    {
        return true;
    }

    /**
     * 初期マウント処理
     */
    public function mount(Widget $widget)
    {
        parent::mount($widget);
        $this->refresh();
    }

    /**
     * データをリフレッシュする
     */
    public function refresh()
    {
        $this->loading = true;
        $this->error = '';

        try {
            $user = Auth::user();
            $connectedAccount = $user->connectedAccounts()
                ->where('service_name', 'google')
                ->first();

            if (!$connectedAccount) {
                throw new Exception('Google account not connected');
            }

            $googleService = new GoogleService();
            $apiClient = $googleService->getApiClient($connectedAccount);

            $settings = $this->widget->settings ?? [];
            $params = [
                'maxResults' => $settings['max_results'] ?? 10,
                'q' => $settings['query'] ?? 'is:unread',
            ];

            $response = $apiClient->getGmailMessages($params);

            if (isset($response['messages'])) {
                $this->messages = [];
                foreach ($response['messages'] as $messageRef) {
                    $message = $apiClient->getGmailMessage($messageRef['id']);
                    if (isset($message['payload'])) {
                        $this->messages[] = $this->parseMessage($message);
                    }
                }
            } else {
                $this->messages = [];
            }

        } catch (Exception $e) {
            $this->error = $e->getMessage();
            $this->messages = [];
        } finally {
            $this->loading = false;
        }
    }

    /**
     * メッセージを解析して整形する
     */
    private function parseMessage(array $message): array
    {
        $headers = [];
        foreach ($message['payload']['headers'] as $header) {
            $headers[$header['name']] = $header['value'];
        }

        return [
            'id' => $message['id'],
            'subject' => $headers['Subject'] ?? __('No subject'),
            'from' => $headers['From'] ?? '',
            'date' => isset($headers['Date']) ? $this->formatDate($headers['Date']) : '',
            'snippet' => $message['snippet'] ?? '',
            'threadId' => $message['threadId'] ?? '',
        ];
    }

    /**
     * 日付をフォーマットする
     */
    private function formatDate(string $dateString): string
    {
        try {
            $date = new \DateTime($dateString);
            return $date->format('M j, Y H:i');
        } catch (\Exception $e) {
            return $dateString;
        }
    }

    /**
     * 描画処理
     */
    public function render()
    {
        return view('widgets.google.gmail', [
            'messages' => $this->messages,
            'loading' => $this->loading,
            'error' => $this->error,
            'widget' => $this->widget,
        ]);
    }
}
