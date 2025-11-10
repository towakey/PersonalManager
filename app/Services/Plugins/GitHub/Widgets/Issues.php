<?php

namespace App\Services\Plugins\GitHub\Widgets;

use App\Http\Livewire\Widgets\BaseWidget;
use App\Services\Plugins\Contracts\ServicePluginInterface;
use App\Services\Plugins\GitHub\GitHubService;
use App\Models\ConnectedAccount;
use App\Models\Widget;
use Illuminate\Support\Facades\Auth;
use Exception;

class Issues extends BaseWidget
{
    public array $issues = [];
    public bool $loading = false;
    public string $error = '';

    /**
     * ウィジェットの識別子
     */
    public static function getIdentifier(): string
    {
        return 'github-issues';
    }

    /**
     * ウィジェットの表示名に対応する言語キーを返す
     */
    public static function getDisplayName(): string
    {
        return 'widgets.github_issues.name';
    }

    /**
     * このウィジェットが属するサービスのServiceクラス名
     */
    public static function getServiceClass(): string
    {
        return GitHubService::class;
    }

    /**
     * このウィジェットが依存するサービスの識別子リストを返す
     */
    public static function getDependencies(): array
    {
        return ['github'];
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
                ->where('service_name', 'github')
                ->first();

            if (!$connectedAccount) {
                throw new Exception('GitHub account not connected');
            }

            $githubService = new GitHubService();
            $apiClient = $githubService->getApiClient($connectedAccount);

            $settings = $this->widget->settings ?? [];
            $params = [
                'state' => $settings['state'] ?? 'open',
                'sort' => $settings['sort'] ?? 'created',
                'direction' => $settings['direction'] ?? 'desc',
            ];

            $this->issues = $apiClient->getIssues($params);

        } catch (Exception $e) {
            $this->error = $e->getMessage();
            $this->issues = [];
        } finally {
            $this->loading = false;
        }
    }

    /**
     * 描画処理
     */
    public function render()
    {
        return view('widgets.github.issues', [
            'issues' => $this->issues,
            'loading' => $this->loading,
            'error' => $this->error,
            'widget' => $this->widget,
        ]);
    }
}
