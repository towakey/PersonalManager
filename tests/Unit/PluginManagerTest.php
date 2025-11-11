<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\Plugins\PluginManager;
use App\Services\Plugins\Contracts\ServicePluginInterface;
use App\Services\Plugins\GitHub\GitHubService;
use App\Services\Plugins\Google\GoogleService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PluginManagerTest extends TestCase
{
    use RefreshDatabase;

    private PluginManager $pluginManager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pluginManager = new PluginManager();
    }

    /**
     * 利用可能なサービスの一覧を取得できるかテスト
     */
    public function test_get_available_services()
    {
        $services = $this->pluginManager->getAvailableServices();

        $this->assertIsIterable($services);
        $this->assertContains(GitHubService::class, $services);
        $this->assertContains(GoogleService::class, $services);
    }

    /**
     * 利用可能なウィジェットの一覧を取得できるかテスト
     */
    public function test_get_available_widgets()
    {
        $widgets = $this->pluginManager->getAvailableWidgets();

        $this->assertIsIterable($widgets);
        $this->assertNotEmpty($widgets);
    }

    /**
     * サービスの利用可能性チェックが正しく動作するかテスト
     */
    public function test_is_service_available()
    {
        $this->assertTrue($this->pluginManager->isServiceAvailable('github'));
        $this->assertTrue($this->pluginManager->isServiceAvailable('google'));
        $this->assertFalse($this->pluginManager->isServiceAvailable('nonexistent'));
    }

    /**
     * ウィジェットの利用可能性チェックが正しく動作するかテスト
     */
    public function test_is_widget_available()
    {
        $this->assertTrue($this->pluginManager->isWidgetAvailable('github-notifications'));
        $this->assertTrue($this->pluginManager->isWidgetAvailable('google-gmail'));
        $this->assertFalse($this->pluginManager->isWidgetAvailable('nonexistent-widget'));
    }

    /**
     * ユーザーのサービス接続状態チェックが正しく動作するかテスト
     */
    public function test_is_user_connected_to_service()
    {
        // 未ログイン状態
        $this->assertFalse($this->pluginManager->isUserConnectedToService('github'));

        // テストユーザーを作成してログイン
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        // 未接続状態
        $this->assertFalse($this->pluginManager->isUserConnectedToService('github'));

        // 接続状態を作成
        \App\Models\ConnectedAccount::factory()->create([
            'user_id' => $user->id,
            'service_name' => 'github',
        ]);

        // 接続済み状態
        $this->assertTrue($this->pluginManager->isUserConnectedToService('github'));
    }

    /**
     * ウィジェットの依存関係チェックが正しく動作するかテスト
     */
    public function test_are_widget_dependencies_met()
    {
        // テストユーザーを作成してログイン
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        // 未接続状態では依存関係が満たされていない
        $this->assertFalse(
            $this->pluginManager->areWidgetDependenciesMet('App\Services\Plugins\GitHub\Widgets\Notifications')
        );

        // GitHubアカウントを接続
        \App\Models\ConnectedAccount::factory()->create([
            'user_id' => $user->id,
            'service_name' => 'github',
        ]);

        // 接続済み状態では依存関係が満たされている
        $this->assertTrue(
            $this->pluginManager->areWidgetDependenciesMet('App\Services\Plugins\GitHub\Widgets\Notifications')
        );
    }

    /**
     * 利用可能なウィジェット情報が正しく取得できるかテスト
     */
    public function test_get_available_widgets_for_user()
    {
        // テストユーザーを作成してログイン
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        $widgets = $this->pluginManager->getAvailableWidgetsForUser();

        $this->assertIsIterable($widgets);
        
        // 未接続状態では依存関係が満たされていないウィジェットは含まれない
        $githubWidgets = $widgets->filter(fn($w) => $w['service_identifier'] === 'github');
        $this->assertEmpty($githubWidgets);

        // GitHubアカウントを接続
        \App\Models\ConnectedAccount::factory()->create([
            'user_id' => $user->id,
            'service_name' => 'github',
        ]);

        $widgets = $this->pluginManager->getAvailableWidgetsForUser();
        
        // 接続済み状態ではGitHubウィジェットが含まれる
        $githubWidgets = $widgets->filter(fn($w) => $w['service_identifier'] === 'github');
        $this->assertNotEmpty($githubWidgets);
    }
}
