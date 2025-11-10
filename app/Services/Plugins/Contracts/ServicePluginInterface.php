<?php

namespace App\Services\Plugins\Contracts;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Models\ConnectedAccount;

interface ServicePluginInterface
{
    /**
     * サービスの一意な識別子を返す (例: 'github')
     * @return string
     */
    public static function getIdentifier(): string;

    /**
     * サービスの表示名に対応する言語キーを返す (例: 'plugins.github.name')
     * @return string
     */
    public static function getDisplayName(): string;

    /**
     * サービスの説明に対応する言語キーを返す (例: 'plugins.github.description')
     * @return string
     */
    public static function getDescription(): string;

    /**
     * このサービスが依存する他のサービスの識別子リストを返す (例: ['google_auth'])
     * @return array<string>
     */
    public static function getDependencies(): array;

    /**
     * このサービスが提供するウィジェットのLivewireコンポーネントクラス名リストを返す
     * @return array<class-string>
     */
    public static function getAvailableWidgets(): array;

    /**
     * 認証を開始するためのリダイレクトレスポンスを生成する
     * @return RedirectResponse
     */
    public function getAuthRedirect(): RedirectResponse;

    /**
     * 認証コールバックを処理し、認証情報をConnectedAccountモデルとして返す
     * @param Request $request
     * @return ConnectedAccount
     */
    public function handleAuthCallback(Request $request): ConnectedAccount;

    /**
     * 指定されたアカウントのAPIクライアントを生成して返す
     * @param ConnectedAccount $account
     * @return mixed
     */
    public function getApiClient(ConnectedAccount $account);
}
