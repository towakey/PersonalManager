<?php

namespace App\Services\Plugins\Google\Widgets;

use App\Http\Livewire\Widgets\BaseWidget;
use App\Services\Plugins\Contracts\ServicePluginInterface;
use App\Services\Plugins\Google\GoogleService;
use App\Models\ConnectedAccount;
use App\Models\Widget;
use Illuminate\Support\Facades\Auth;
use Exception;

class Calendar extends BaseWidget
{
    public array $events = [];
    public bool $loading = false;
    public string $error = '';

    /**
     * ウィジェットの識別子
     */
    public static function getIdentifier(): string
    {
        return 'google-calendar';
    }

    /**
     * ウィジェットの表示名に対応する言語キーを返す
     */
    public static function getDisplayName(): string
    {
        return 'widgets.google_calendar.name';
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
                'timeMin' => $settings['start_date'] ?? now()->startOfDay()->format(\DateTime::ATOM),
                'timeMax' => $settings['end_date'] ?? now()->addDays(7)->endOfDay()->format(\DateTime::ATOM),
                'maxResults' => $settings['max_results'] ?? 10,
                'orderBy' => 'startTime',
                'singleEvents' => 'true',
            ];

            $response = $apiClient->getCalendarEvents($params);

            $this->events = isset($response['items']) ? array_map([$this, 'parseEvent'], $response['items']) : [];

        } catch (Exception $e) {
            $this->error = $e->getMessage();
            $this->events = [];
        } finally {
            $this->loading = false;
        }
    }

    /**
     * イベントを解析して整形する
     */
    private function parseEvent(array $event): array
    {
        $start = $event['start'] ?? [];
        $end = $event['end'] ?? [];

        $startTime = isset($start['dateTime']) 
            ? $this->formatDateTime($start['dateTime'])
            : (isset($start['date']) ? $this->formatDate($start['date']) : '');

        $endTime = isset($end['dateTime']) 
            ? $this->formatDateTime($end['dateTime'])
            : (isset($end['date']) ? $this->formatDate($end['date']) : '');

        return [
            'id' => $event['id'] ?? '',
            'summary' => $event['summary'] ?? __('No title'),
            'description' => $event['description'] ?? '',
            'start_time' => $startTime,
            'end_time' => $endTime,
            'location' => $event['location'] ?? '',
            'html_link' => $event['htmlLink'] ?? '',
            'status' => $event['status'] ?? '',
        ];
    }

    /**
     * 日付時刻をフォーマットする
     */
    private function formatDateTime(string $dateTimeString): string
    {
        try {
            $date = new \DateTime($dateTimeString);
            return $date->format('M j, Y H:i');
        } catch (\Exception $e) {
            return $dateTimeString;
        }
    }

    /**
     * 日付をフォーマットする
     */
    private function formatDate(string $dateString): string
    {
        try {
            $date = new \DateTime($dateString);
            return $date->format('M j, Y');
        } catch (\Exception $e) {
            return $dateString;
        }
    }

    /**
     * 描画処理
     */
    public function render()
    {
        return view('widgets.google.calendar', [
            'events' => $this->events,
            'loading' => $this->loading,
            'error' => $this->error,
            'widget' => $this->widget,
        ]);
    }
}
