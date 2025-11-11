<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\ConnectedAccount;
use App\Models\Group;
use App\Models\ServerConnection;

class SettingsController extends Controller
{
    /**
     * 設定画面を表示
     */
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $connectedAccounts = ConnectedAccount::where('user_id', $user->id)->get();
        $groups = Group::where('owner_id', $user->id)->with('users')->get();
        $serverConnections = ServerConnection::all();

        return view('settings', compact('user', 'connectedAccounts', 'groups', 'serverConnections'));
    }

    /**
     * プロフィール設定を更新
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'locale' => ['required', 'string', 'in:ja,en'],
            'current_password' => ['required', 'current_password'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->locale = $validated['locale'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('settings')
            ->with('success', __('settings.profile.updated'));
    }

    /**
     * アカウント連携を更新
     */
    public function updateAccounts(Request $request)
    {
        $validated = $request->validate([
            'action' => ['required', 'string', 'in:connect,disconnect'],
            'service_name' => ['required', 'string'],
        ]);

        if ($validated['action'] === 'disconnect') {
            $account = ConnectedAccount::where('user_id', Auth::id())
                ->where('service_name', $validated['service_name'])
                ->first();

            if ($account) {
                $account->delete();
                return redirect()->route('settings')
                    ->with('success', __('settings.accounts.disconnected'));
            }
        }

        return redirect()->route('settings')
            ->with('error', __('settings.accounts.error'));
    }

    /**
     * データ公開設定を更新
     */
    public function updateSharing(Request $request)
    {
        $validated = $request->validate([
            'widget_id' => ['required', 'integer', 'exists:widgets,id'],
            'sharing_type' => ['required', 'string', 'in:private,specific_users,specific_groups,specific_servers'],
            'target_ids' => ['nullable', 'array'],
            'target_ids.*' => ['integer'],
        ]);

        // ウィジェットの所有者チェック
        $widget = \App\Models\Widget::where('id', $validated['widget_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // 共有設定の更新ロジックをここに実装
        // これは後ほど実装します

        return redirect()->route('settings')
            ->with('success', __('settings.sharing.updated'));
    }

    /**
     * サーバー間連携を更新
     */
    public function updateServers(Request $request)
    {
        $validated = $request->validate([
            'action' => ['required', 'string', 'in:add,remove,approve,reject'],
            'server_id' => ['nullable', 'integer', 'exists:server_connections,id'],
            'name' => ['required_if:action,add', 'string', 'max:255'],
            'url' => ['required_if:action,add', 'url'],
            'server_identifier' => ['required_if:action,add', 'string', 'max:255'],
        ]);

        switch ($validated['action']) {
            case 'add':
                ServerConnection::create([
                    'name' => $validated['name'],
                    'url' => $validated['url'],
                    'server_identifier' => $validated['server_identifier'],
                    'status' => 'pending_sent',
                ]);
                $message = __('settings.servers.invitation_sent');
                break;

            case 'remove':
                if (isset($validated['server_id'])) {
                    $server = ServerConnection::findOrFail($validated['server_id']);
                    $server->delete();
                    $message = __('settings.servers.removed');
                }
                break;

            case 'approve':
                if (isset($validated['server_id'])) {
                    $server = ServerConnection::findOrFail($validated['server_id']);
                    $server->status = 'approved';
                    $server->save();
                    $message = __('settings.servers.approved');
                }
                break;

            case 'reject':
                if (isset($validated['server_id'])) {
                    $server = ServerConnection::findOrFail($validated['server_id']);
                    $server->status = 'rejected';
                    $server->save();
                    $message = __('settings.servers.rejected');
                }
                break;

            default:
                $message = __('settings.servers.error');
        }

        return redirect()->route('settings')
            ->with('success', $message);
    }
}
