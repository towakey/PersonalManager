<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\ConnectedAccount;
use App\Models\Group;
use App\Models\ServerConnection;
use App\Models\Widget;
use App\Models\SharingSetting;

class Settings extends Component
{
    // プロフィール設定
    public $name;
    public $email;
    public $locale;
    public $current_password;
    public $password;
    public $password_confirmation;

    // アカウント連携
    public $connected_accounts = [];
    public $available_services = [];

    // データ公開設定
    public $widgets = [];
    public $sharing_settings = [];
    public $sharing_rules = [];
    public $selected_widget_id = null;
    public $new_rule_target_type = '';
    public $new_rule_target_id = '';

    // サーバー間連携
    public $server_connections = [];
    public $new_server_name = '';
    public $new_server_url = '';
    public $new_server_identifier = '';

    // UI状態
    public $activeTab = 'profile';
    public $showSuccessMessage = false;
    public $successMessage = '';

    protected $listeners = [
        'settingsUpdated' => 'handleSettingsUpdated',
    ];

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255',
        'locale' => 'required|string|in:ja,en',
        'current_password' => 'required|current_password',
        'password' => 'nullable|confirmed|min:8',
        'new_server_name' => 'required|string|max:255',
        'new_server_url' => 'required|url',
        'new_server_identifier' => 'required|string|max:255',
    ];

    public function mount()
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->locale = $user->locale ?? 'ja';

        $this->loadConnectedAccounts();
        $this->loadAvailableServices();
        $this->loadWidgets();
        $this->loadServerConnections();
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
     * 利用可能なサービスを読み込む
     */
    private function loadAvailableServices()
    {
        $this->available_services = [
            [
                'id' => 'github',
                'name' => __('services.github.name'),
                'description' => __('services.github.description'),
                'connected' => $this->isServiceConnected('github'),
                'auth_url' => route('auth.github'),
            ],
            [
                'id' => 'google',
                'name' => __('services.google.name'),
                'description' => __('services.google.description'),
                'connected' => $this->isServiceConnected('google'),
                'auth_url' => route('auth.google'),
            ],
        ];
    }

    /**
     * サービスが連携済みかチェック
     */
    private function isServiceConnected($serviceName)
    {
        return ConnectedAccount::where('user_id', Auth::id())
            ->where('service_name', $serviceName)
            ->exists();
    }

    /**
     * ウィジェットと共有設定を読み込む
     */
    private function loadWidgets()
    {
        $this->widgets = Widget::where('user_id', Auth::id())
            ->with('sharingSetting.sharingRules')
            ->get()
            ->toArray();
    }

    /**
     * サーバー接続を読み込む
     */
    private function loadServerConnections()
    {
        $this->server_connections = ServerConnection::all()
            ->toArray();
    }

    /**
     * タブを切り替え
     */
    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    /**
     * プロフィールを更新
     */
    public function updateProfile()
    {
        $this->validate();

        $user = Auth::user();
        $user->name = $this->name;
        $user->email = $this->email;
        $user->locale = $this->locale;

        if (!empty($this->password)) {
            $user->password = Hash::make($this->password);
        }

        $user->save();

        $this->showSuccess(__('settings.profile.updated'));
        $this->reset(['current_password', 'password', 'password_confirmation']);
    }

    /**
     * アカウント連携を解除
     */
    public function disconnectAccount($serviceName)
    {
        $account = ConnectedAccount::where('user_id', Auth::id())
            ->where('service_name', $serviceName)
            ->first();

        if ($account) {
            $account->delete();
            $this->loadConnectedAccounts();
            $this->loadAvailableServices();
            $this->showSuccess(__('settings.accounts.disconnected'));
        }
    }

    /**
     * ウィジェットの共有設定を更新
     */
    public function updateSharingSettings($widgetId, $sharingType)
    {
        $widget = Widget::where('id', $widgetId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $sharingSetting = SharingSetting::firstOrCreate(
            ['widget_id' => $widgetId],
            [
                'sharing_type' => $sharingType,
                'access_token' => \Str::random(32),
            ]
        );

        $sharingSetting->sharing_type = $sharingType;
        $sharingSetting->save();

        // プライベート設定の場合は既存の共有ルールを削除
        if ($sharingType === 'private') {
            $sharingSetting->sharingRules()->delete();
        }

        $this->loadWidgets();
        $this->showSuccess(__('settings.sharing.updated'));
    }

    /**
     * 共有ルールを追加
     */
    public function addSharingRule($widgetId, $targetType, $targetId)
    {
        $widget = Widget::where('id', $widgetId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $sharingSetting = $widget->sharingSetting;
        if (!$sharingSetting) {
            $sharingSetting = SharingSetting::create([
                'widget_id' => $widgetId,
                'sharing_type' => 'specific_users',
                'access_token' => \Str::random(32),
            ]);
        }

        // 既存のルールを確認
        $existingRule = $sharingSetting->sharingRules()
            ->where('target_type', $targetType)
            ->where('target_id', $targetId)
            ->first();

        if (!$existingRule) {
            $sharingSetting->sharingRules()->create([
                'target_type' => $targetType,
                'target_id' => $targetId,
            ]);

            $this->loadWidgets();
            $this->showSuccess(__('settings.sharing.rule_added'));
        }
    }

    /**
     * 共有ルールを削除
     */
    public function removeSharingRule($ruleId)
    {
        $rule = \App\Models\SharingRule::findOrFail($ruleId);
        
        // 所有者チェック
        if ($rule->sharingSetting->widget->user_id !== Auth::id()) {
            return;
        }

        $rule->delete();
        $this->loadWidgets();
        $this->showSuccess(__('settings.sharing.rule_removed'));
    }

    /**
     * サーバーを追加
     */
    public function addServer()
    {
        $this->validate([
            'new_server_name' => 'required|string|max:255',
            'new_server_url' => 'required|url',
            'new_server_identifier' => 'required|string|max:255',
        ]);

        ServerConnection::create([
            'name' => $this->new_server_name,
            'url' => $this->new_server_url,
            'server_identifier' => $this->new_server_identifier,
            'status' => 'pending_sent',
        ]);

        $this->reset(['new_server_name', 'new_server_url', 'new_server_identifier']);
        $this->loadServerConnections();
        $this->showSuccess(__('settings.servers.invitation_sent'));
    }

    /**
     * サーバー接続を削除
     */
    public function removeServer($serverId)
    {
        $server = ServerConnection::findOrFail($serverId);
        $server->delete();

        $this->loadServerConnections();
        $this->showSuccess(__('settings.servers.removed'));
    }

    /**
     * サーバー招待を承認
     */
    public function approveServer($serverId)
    {
        $server = ServerConnection::findOrFail($serverId);
        $server->status = 'approved';
        $server->save();

        $this->loadServerConnections();
        $this->showSuccess(__('settings.servers.approved'));
    }

    /**
     * サーバー招待を拒否
     */
    public function rejectServer($serverId)
    {
        $server = ServerConnection::findOrFail($serverId);
        $server->status = 'rejected';
        $server->save();

        $this->loadServerConnections();
        $this->showSuccess(__('settings.servers.rejected'));
    }

    /**
     * 成功メッセージを表示
     */
    private function showSuccess($message)
    {
        $this->successMessage = $message;
        $this->showSuccessMessage = true;

        $this->dispatch('settingsUpdated');
    }

    /**
     * 設定更新イベントを処理
     */
    public function handleSettingsUpdated()
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
        return view('livewire.settings');
    }
}
