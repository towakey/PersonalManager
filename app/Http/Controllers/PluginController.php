<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Plugins\PluginManager;
use App\Models\ConnectedAccount;

class PluginController extends Controller
{
    protected $pluginManager;

    public function __construct(PluginManager $pluginManager)
    {
        $this->pluginManager = $pluginManager;
    }

    /**
     * プラグイン管理画面を表示
     */
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $connectedAccounts = ConnectedAccount::where('user_id', $user->id)->get();
        
        // 利用可能なサービスを取得
        $availableServices = $this->pluginManager->getAvailableServices()->map(function ($serviceClass) {
            return [
                'class' => $serviceClass,
                'identifier' => $serviceClass::getIdentifier(),
                'display_name' => $serviceClass::getDisplayName(),
                'description' => $serviceClass::getDescription(),
                'dependencies' => $serviceClass::getDependencies(),
                'connected' => $this->pluginManager->isUserConnectedToService($serviceClass::getIdentifier()),
                'auth_url' => $this->getAuthUrl($serviceClass::getIdentifier()),
            ];
        });

        // 利用可能なウィジェットを取得
        $availableWidgets = $this->pluginManager->getAvailableWidgetsForUser();

        return view('plugins', compact(
            'user',
            'connectedAccounts',
            'availableServices',
            'availableWidgets'
        ));
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
            default:
                return null;
        }
    }

    /**
     * サービス接続を解除
     */
    public function disconnectService(Request $request)
    {
        $validated = $request->validate([
            'service_name' => ['required', 'string'],
        ]);

        $account = ConnectedAccount::where('user_id', Auth::id())
            ->where('service_name', $validated['service_name'])
            ->first();

        if ($account) {
            $account->delete();
            return redirect()->route('plugins')
                ->with('success', __('plugins.service.disconnected'));
        }

        return redirect()->route('plugins')
            ->with('error', __('plugins.service.disconnect_error'));
    }

    /**
     * プラグインの有効/無効を切り替え
     */
    public function togglePlugin(Request $request)
    {
        $validated = $request->validate([
            'plugin_identifier' => ['required', 'string'],
            'enabled' => ['required', 'boolean'],
        ]);

        // プラグインの有効/無効状態を設定に保存
        // この機能は将来的に実装
        $message = $validated['enabled'] 
            ? __('plugins.plugin.enabled') 
            : __('plugins.plugin.disabled');

        return redirect()->route('plugins')
            ->with('success', $message);
    }
}
