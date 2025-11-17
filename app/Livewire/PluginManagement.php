<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Services\Plugins\PluginManager;
use App\Models\ConnectedAccount;

class PluginManagement extends Component
{
    public $available_services = [];
    public $available_widgets = [];
    public $connected_accounts = [];
    
    // UI状態
    public $activeTab = 'services';
    public $showSuccessMessage = false;
    public $successMessage = '';
    public $selectedService = null;

    protected $listeners = [
        'pluginUpdated' => 'handlePluginUpdated',
    ];

    public function mount()
    {
        $this->loadAvailableServices();
        $this->loadAvailableWidgets();
        $this->loadConnectedAccounts();
    }

    /**
     * 利用可能なサービスを読み込む
     */
    private function loadAvailableServices()
    {
        $pluginManager = app(PluginManager::class);
        
        $this->available_services = $pluginManager->getAvailableServices()->map(function ($serviceClass) use ($pluginManager) {
            $identifier = $serviceClass::getIdentifier();
            
            return [
                'class' => $serviceClass,
                'identifier' => $identifier,
                'display_name' => $serviceClass::getDisplayName(),
                'description' => $serviceClass::getDescription(),
                'dependencies' => $serviceClass::getDependencies(),
                'connected' => $pluginManager->isUserConnectedToService($identifier),
                'auth_url' => $this->getAuthUrl($identifier),
                'dependencies_met' => $this->checkDependencies($serviceClass::getDependencies()),
                'configured' => $this->isServiceConfigured($identifier),
            ];
        })->toArray();
    }

    /**
     * 利用可能なウィジェットを読み込む
     */
    private function loadAvailableWidgets()
    {
        $pluginManager = app(PluginManager::class);
        
        $this->available_widgets = $pluginManager->getAvailableWidgetsForUser()->toArray();
    }

    /**
     * 連携済みアカウントを読み込む
     */
    private function loadConnectedAccounts()
    {
        $this->connected_accounts = ConnectedAccount::where('user_id', Auth::id())
            ->get()
            ->toArray();
    }

    /**
     * サービスが設定されているかチェック
     */
    private function isServiceConfigured($serviceIdentifier): bool
    {
        switch ($serviceIdentifier) {
            case 'github':
                return !empty(config('services.github.client_id')) && 
                       !empty(config('services.github.client_secret'));
            case 'google':
                return !empty(config('services.google.client_id')) && 
                       !empty(config('services.google.client_secret'));
            case 'twitter':
                return !empty(config('services.twitter.client_id')) && 
                       !empty(config('services.twitter.client_secret'));
            default:
                return false;
        }
    }

    /**
     * 依存関係をチェック
     */
    private function checkDependencies($dependencies)
    {
        $pluginManager = app(PluginManager::class);
        
        foreach ($dependencies as $dependency) {
            if (!$pluginManager->isServiceAvailable($dependency) || 
                !$pluginManager->isUserConnectedToService($dependency)) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * サービスの認証URLを取得
     */
    private function getAuthUrl($serviceIdentifier)
    {
        switch ($serviceIdentifier) {
            case 'github':
                return route('auth.github');
            case 'google':
                return route('auth.google');
            case 'twitter':
                return route('auth.twitter');
            default:
                return null;
        }
    }

    /**
     * タブを切り替え
     */
    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    /**
     * サービス接続を解除
     */
    public function disconnectService($serviceName)
    {
        $account = ConnectedAccount::where('user_id', Auth::id())
            ->where('service_name', $serviceName)
            ->first();

        if ($account) {
            $account->delete();
            $this->loadConnectedAccounts();
            $this->loadAvailableServices();
            $this->loadAvailableWidgets();
            $this->showSuccess(__('plugins.service.disconnected'));
        }
    }

    /**
     * サービス詳細を表示
     */
    public function showServiceDetails($serviceIdentifier)
    {
        $this->selectedService = collect($this->available_services)
            ->firstWhere('identifier', $serviceIdentifier);
    }

    /**
     * サービス詳細を閉じる
     */
    public function closeServiceDetails()
    {
        $this->selectedService = null;
    }

    /**
     * プラグインを有効/無効に切り替え
     */
    public function togglePlugin($pluginIdentifier, $enabled)
    {
        // この機能は将来的に実装
        $message = $enabled 
            ? __('plugins.plugin.enabled') 
            : __('plugins.plugin.disabled');

        $this->showSuccess($message);
    }

    /**
     * 成功メッセージを表示
     */
    private function showSuccess($message)
    {
        $this->successMessage = $message;
        $this->showSuccessMessage = true;

        $this->dispatch('pluginUpdated');
    }

    /**
     * プラグイン更新イベントを処理
     */
    public function handlePluginUpdated()
    {
        // 3秒後に成功メッセージを非表示
        $this->dispatch('close-success-message');
    }

    /**
     * 成功メッセージを閉じる
     */
    public function closeSuccessMessage()
    {
        $this->showSuccessMessage = false;
        $this->successMessage = '';
    }

    /**
     * 描画処理
     */
    public function render()
    {
        return view('livewire.plugin-management');
    }
}
